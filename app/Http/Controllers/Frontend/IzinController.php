<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use App\Services\TelegramBotService;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\HariLibur;

class IzinController extends Controller
{
    protected $workingDaysCalculator;

    public function __construct(WorkingDaysCalculator $workingDaysCalculator)
    {
        $this->workingDaysCalculator = $workingDaysCalculator;
    }
    public function index(Request $request)
    {
        $karyawan = auth()->user()->karyawan;

        $query = PengajuanIzin::where('karyawan_id', $karyawan->id);

        $filters = [
            'bulan' => $request->get('bulan', date('m')),
            'tahun' => $request->get('tahun', date('Y')),
            'status' => $request->get('status', ''),
        ];

        if ($filters['bulan']) {
            $query->whereMonth('tanggal_awal', $filters['bulan']);
        }

        if ($filters['tahun']) {
            $query->whereYear('tanggal_awal', $filters['tahun']);
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $pengajuan_izin = $query->orderBy('tanggal_awal', 'desc')->get();

        // Get years for dropdown filter
        $years = PengajuanIzin::where('karyawan_id', $karyawan->id)
            ->selectRaw('YEAR(tanggal_awal) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $statuses = [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        // Data for create form
        $jenis_cuti_db = Cuti::orderBy('nama_cuti')->get(); // Mengganti nama variabel
        $hari_libur = HariLibur::pluck('tanggal')->toArray();

        return view('frontend.izin.index', compact('pengajuan_izin', 'filters', 'years', 'statuses', 'jenis_cuti_db', 'hari_libur'));
    }

    public function getCutiDetails($id)
    {
        $karyawan = auth()->user()->karyawan;
        $cuti = Cuti::find($id);

        if (!$cuti) {
            return response()->json(['error' => 'Jenis Cuti tidak ditemukan'], 404);
        }

        $sisa = null;
        if ($cuti->potong_cuti) {
            // Assuming sisa_cuti_tahunan is the field for annual leave balance
            $sisa = $karyawan->sisa_cuti_tahunan;
        }

        return response()->json([
            'sisa_cuti' => $sisa,
            'potong_cuti' => $cuti->potong_cuti,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin' => 'required|in:Cuti,Izin,Sakit',
            'cuti_id' => 'required_if:jenis_izin,Cuti|exists:cuti,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:255',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $karyawan = auth()->user()->karyawan;
        $tanggal_awal = Carbon::parse($request->tanggal_mulai);
        $tanggal_akhir = Carbon::parse($request->tanggal_selesai);
        
        // Use WorkingDaysCalculator to calculate only working days (excluding weekends)
        $jumlah_hari_izin = $this->workingDaysCalculator->calculateWorkingDays($tanggal_awal, $tanggal_akhir);
        $sisaCutiTahunan = null;

        // Handle leave balance calculation for Cuti (annual leave)
        if ($request->jenis_izin == 'Cuti') {
            $cuti = Cuti::find($request->cuti_id);
            if ($cuti && $cuti->potong_cuti) {
                $sisaCuti = $karyawan->sisa_cuti_tahunan ?? 12;
                if ($jumlah_hari_izin > $sisaCuti) {
                    return redirect()->back()->with('error', 'Jatah cuti tidak mencukupi. Sisa cuti Anda: ' . $sisaCuti . ' hari kerja.');
                }
                $sisaCutiTahunan = $sisaCuti - $jumlah_hari_izin;
            }
        }

        $tanggal = Carbon::now()->format('ymd');
        $lastKode = PengajuanIzin::where('kode_izin', 'like', "IZ{$tanggal}%")->orderBy('kode_izin', 'desc')->first();
        $newNumber = $lastKode ? ((int) substr($lastKode->kode_izin, -3)) + 1 : 1;
        $kodeIzin = "IZ{$tanggal}" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('bukti')) {
            $filePath = $request->file('bukti')->store('public/pengajuan-izin');
        }

        // --- Logic to calculate remaining annual leave using working days ---
        if ($request->jenis_izin == 'Cuti') {
            $lastLeave = PengajuanIzin::where('karyawan_id', $karyawan->id)
                ->where('status', 'approved')
                ->latest('tanggal_akhir')
                ->first();

            $sisaCutiSebelumnya = $lastLeave ? $lastLeave->sisa_cuti : 12;
            $sisaCutiTahunan = $sisaCutiSebelumnya - $jumlah_hari_izin;
        }
        // --- End of logic ---

        $pengajuan = PengajuanIzin::create([
            'kode_izin' => $kodeIzin,
            'karyawan_id' => $karyawan->id,
            'cuti_id' => $request->jenis_izin == 'Cuti' ? $request->cuti_id : null,
            'tanggal_awal' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_selesai,
            'jumlah_hari' => $jumlah_hari_izin,
            'sisa_cuti' => $sisaCutiTahunan,
            'keterangan' => $request->keterangan,
            'file_pendukung' => $filePath,
            'status' => 'pending',
            'jenis_pengajuan' => $request->jenis_izin,
        ]);

        // --- Start Telegram Notification Logic ---
        $telegramService = new TelegramBotService();
        $config = \App\Models\TelegramNotification::first();
        if ($config && $config->is_active && $config->notify_leave_request) {
            // Notify Employee
            $user = $pengajuan->karyawan->user;
            if ($user && $user->id_chat_telegram) {
                $message = "Pengajuan izin Anda dengan kode {$pengajuan->kode_izin} telah berhasil dibuat dan sedang menunggu persetujuan.";
                try {
                    $telegramService->sendMessage($user->id_chat_telegram, $message);
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim notifikasi pengajuan izin ke karyawan ' . $user->id . ': ' . $e->getMessage());
                }
            }

            // Notify Admins
            $admins = \App\Models\User::whereNotNull('id_admin_telegram')->get();
            if ($admins->isNotEmpty()) {
                $messageToAdmin = "Pengajuan izin baru dari {$pengajuan->karyawan->nama} ({$pengajuan->kode_izin}) pada tanggal {$pengajuan->tanggal_awal->format('d/m/Y')} - {$pengajuan->tanggal_akhir->format('d/m/Y')}. Mohon untuk ditinjau.";
                foreach ($admins as $admin) {
                    try {
                        $telegramService->sendMessage($admin->id_admin_telegram, $messageToAdmin);
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim notifikasi pengajuan izin ke admin ' . $admin->id . ': ' . $e->getMessage());
                    }
                }
            }
        }
        // --- End Telegram Notification Logic ---

        return redirect()->route('frontend.izin.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function cancel($id)
    {
        $pengajuan = PengajuanIzin::where('id', $id)
            ->where('karyawan_id', Auth::guard('karyawan')->id())
            ->firstOrFail();

        // Karyawan hanya bisa membatalkan jika status masih 'Menunggu'
        if ($pengajuan->status_approved == '0') {
            $pengajuan->status_approved = '3'; // Dibatalkan oleh karyawan
            $pengajuan->save();
            return redirect()->route('frontend.izin.index')->with('success', 'Pengajuan berhasil dibatalkan.');
        }

        return redirect()->route('frontend.izin.index')->with('error', 'Pengajuan tidak dapat dibatalkan.');
    }
}
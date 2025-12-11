<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\HariLibur;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramBotService;
use App\Models\TelegramNotification;
use App\Models\User;

class IzinController extends Controller
{
    /**
     * Menampilkan halaman riwayat pengajuan izin.
     */
    public function index()
    {
        $karyawan = Auth::user()->karyawan;
        $riwayat = PengajuanIzin::where('karyawan_id', $karyawan->id)
            ->with('cuti')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.izin.index', compact('riwayat'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan izin baru.
     */
    public function create()
    {
        $karyawan = Auth::user()->karyawan;
        $jenisCuti = Cuti::where('status', 'active')->get();
        $hariLibur = HariLibur::pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        return view('frontend.izin.create', compact('karyawan', 'jenisCuti', 'hariLibur'));
    }

    /**
     * Menyimpan pengajuan izin baru.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $karyawan = Auth::user()->karyawan;
            $cuti = Cuti::findOrFail($request->cuti_id);
            $tanggalAwal = Carbon::parse($request->tanggal_awal);
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir);

            // Hitung jumlah hari kerja
            $hariLibur = HariLibur::pluck('tanggal')->map(fn($date) => $date->format('Y-m-d'));
            $jumlahHari = 0;
            $period = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
            foreach ($period as $date) {
                if ($date->isWeekday() && !$hariLibur->contains($date->format('Y-m-d'))) {
                    $jumlahHari++;
                }
            }

            if ($jumlahHari <= 0) {
                return back()->with('error', 'Jumlah hari izin tidak valid. Pastikan rentang tanggal bukan hari libur atau akhir pekan.');
            }

            // Cek sisa cuti jika perlu
            if ($cuti->jenis_cuti === 'tahunan' && $karyawan->sisa_cuti_tahunan < $jumlahHari) {
                return back()->with('error', 'Sisa cuti tahunan Anda tidak mencukupi.');
            }

            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $filePath = $request->file('file_pendukung')->store('public/file_pendukung_izin');
            }

            $pengajuan = PengajuanIzin::create([
                'karyawan_id' => $karyawan->id,
                'cuti_id' => $request->cuti_id,
                'tanggal_awal' => $tanggalAwal,
                'tanggal_akhir' => $tanggalAkhir,
                'jumlah_hari' => $jumlahHari,
                'keterangan' => $request->keterangan,
                'file_pendukung' => $filePath,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('frontend.izin.index')
                ->with('success', 'Pengajuan izin berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error in IzinController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan izin');
        }
    }
}
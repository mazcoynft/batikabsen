<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\User;
use App\Models\JamKerjaKaryawan;
use App\Models\JamKerja;
use App\Models\Pengumuman;
// PERBAIKAN: Tambahkan model yang diperlukan untuk halaman izin
use App\Models\Cuti;
use App\Models\PengajuanIzin;
use App\Services\TelegramBotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FrontendController extends Controller
{
    public function showLogin()
    {
        return view('frontend.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric',
            'password' => 'required',
        ]);

        $user = User::where('nik_app', $request->nik)->first();

        if (!$user || !Hash::check($request->password, $user->pwd_app)) {
            return back()->withErrors([
                'nik' => 'NIK atau password salah.',
            ]);
        }

        Auth::guard('frontend')->login($user);

        return redirect()->route('frontend.dashboard');
    }
    
    public function dashboard()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            Log::error('User tidak memiliki data karyawan', ['user_id' => $user->id, 'user_email' => $user->email]);
            return redirect()->route('frontend.login')->with('error', 'Data karyawan tidak ditemukan. Silakan hubungi administrator.');
        }
        
        $bulanIni = Carbon::now()->format('Y-m');
        $hariIni = Carbon::now();
        
        // Perbaikan: Hanya menghitung sampai hari ini, tidak termasuk hari-hari berikutnya
        $kehadiran = Presensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tgl_presensi', $hariIni->month)
            ->whereYear('tgl_presensi', $hariIni->year)
            ->whereDate('tgl_presensi', '<=', $hariIni->toDateString())
            ->whereNotNull('jam_in')
            ->count();
            
        $izin = Presensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tgl_presensi', $hariIni->month)
            ->whereYear('tgl_presensi', $hariIni->year)
            ->whereDate('tgl_presensi', '<=', $hariIni->toDateString())
            ->whereIn('status', ['i', 's', 'c'])
            ->count();
            
        $terlambat = Presensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tgl_presensi', $hariIni->month)
            ->whereYear('tgl_presensi', $hariIni->year)
            ->whereDate('tgl_presensi', '<=', $hariIni->toDateString())
            ->where('status_presensi_in', '2')
            ->count();
            
        $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tgl_presensi', $hariIni->toDateString())
            ->first();
            
        // Perbaikan: Kosongkan jam_masuk dan jam_pulang jika status adalah izin/cuti/sakit
        $jam_masuk = null;
        $jam_pulang = null;
        $status_masuk = null; // Inisialisasi variabel status_masuk
        $foto_masuk = null; // Tambahkan variabel untuk foto masuk
        $foto_pulang = null; // Tambahkan variabel untuk foto pulang
        
        if ($presensiHariIni) {
            if (!in_array($presensiHariIni->status, ['i', 's', 'c'])) {
                $jam_masuk = $presensiHariIni->jam_in;
                $jam_pulang = $presensiHariIni->jam_out;
                $foto_masuk = $presensiHariIni->foto_in; // Ambil foto masuk
                $foto_pulang = $presensiHariIni->foto_out; // Ambil foto pulang
                $status_masuk = $presensiHariIni->status_presensi_in == '1' ? 'tepat' : 'terlambat';
            }
        }
        
        // Perbaikan: Hanya menampilkan data sampai hari ini, urutkan dari terbaru (hari ini di atas)
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tgl_presensi', $hariIni->month)
            ->whereYear('tgl_presensi', $hariIni->year)
            ->whereDate('tgl_presensi', '<=', $hariIni->toDateString())
            ->orderBy('tgl_presensi', 'desc')
            ->get();
            
        $leaderboard = Presensi::with('karyawan')
            ->whereDate('tgl_presensi', $hariIni->toDateString())
            ->orderBy('jam_in', 'asc')
            ->get();
        
        $pengumuman = Pengumuman::active()->limit(3)->get();
        
        return view('frontend.dashboard', compact(
            'kehadiran', 'izin', 'terlambat', 'jam_masuk', 'jam_pulang', 
            'status_masuk', 'presensi', 'leaderboard', 'pengumuman',
            'foto_masuk', 'foto_pulang' // Tambahkan foto_masuk dan foto_pulang ke compact
        ));
    }

    public function history()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return redirect()->route('frontend.login')->with('error', 'Data karyawan tidak ditemukan.');
        }
        
        // Filter berdasarkan tanggal jika ada
        $startDate = request('start_date') ? Carbon::parse(request('start_date')) : Carbon::now()->startOfMonth();
        $endDate = request('end_date') ? Carbon::parse(request('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        
        $presensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereBetween('tgl_presensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('tgl_presensi', 'desc')
            ->get();
            
        return view('frontend.history', compact('presensi'));
    }

    public function absen()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;
        
        $presensiHariIni = null;
        if ($karyawan) {
            $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tgl_presensi', Carbon::now()->toDateString())
                ->first();
        }
        
        // PERBAIKAN: Menghapus pengambilan $jamKerja yang tidak digunakan
        return view('frontend.absen', compact('presensiHariIni', 'karyawan'));
    }

    public function getJamKerja()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan'], 404);
        }
        
        $hariIni = strtolower(Carbon::now()->locale('id')->dayName);
        $jamKerjaKaryawan = $karyawan->jamKerjaHarian()->where('hari', $hariIni)->first();
        
        if ($jamKerjaKaryawan) {
            $jamKerja = $jamKerjaKaryawan->jamKerja;
        } else {
            // Fallback ke jam kerja default
            $jamKerja = JamKerja::find($karyawan->jam_kerja_id);
        }
        
        $cabang = $karyawan->cabang;
        
        // Ambil data presensi hari ini
        $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tgl_presensi', Carbon::now()->toDateString())
            ->first();
        
        return response()->json([
            'jam_kerja' => $jamKerja,
            'cabang' => $cabang,
            'presensi' => $presensiHariIni,
            'tanggal' => Carbon::now()->format('d-m-Y'),
            'hari' => Carbon::now()->locale('id')->dayName
        ]);
    }

    public function checkAbsenStatus()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;
        
        if (!$karyawan) {
            return response()->json(['error' => 'Data karyawan tidak ditemukan'], 404);
        }
        
        // Ambil data presensi hari ini
        $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tgl_presensi', Carbon::now()->toDateString())
            ->first();
        
        return response()->json([
            'presensi' => $presensiHariIni,
            'sudah_absen_masuk' => $presensiHariIni && $presensiHariIni->jam_in,
            'sudah_absen_pulang' => $presensiHariIni && $presensiHariIni->jam_out,
        ]);
    }

    public function storeAbsen(Request $request, TelegramBotService $telegramService)
    {
        try {
            $user = Auth::guard('frontend')->user();
            $karyawan = $user->karyawan;

            if (!$karyawan) {
                return response()->json(['status' => 'error', 'message' => 'Data karyawan tidak ditemukan'], 404);
            }

            $tglSekarang = Carbon::now()->format('Y-m-d');
            $jamSekarang = Carbon::now();
            $hariIni = strtolower(Carbon::now()->locale('id')->dayName);

            // PERBAIKAN: Menggunakan logika yang sama dengan getJamKerja() termasuk fallback
            $jamKerjaKaryawan = $karyawan->jamKerjaHarian()->where('hari', $hariIni)->first();
            if ($jamKerjaKaryawan) {
                $jamKerja = $jamKerjaKaryawan->jamKerja;
            } else {
                $jamKerja = JamKerja::find($karyawan->jam_kerja_id);
            }
            
            if (!$jamKerja) {
                return response()->json(['status' => 'error', 'message' => 'Jam kerja tidak ditemukan untuk hari ini'], 404);
            }

            $presensi = Presensi::where('karyawan_id', $karyawan->id)
                ->where('tgl_presensi', $tglSekarang)
                ->first();

            if (!$request->has('foto') || empty($request->foto)) {
                return response()->json(['status' => 'error', 'message' => 'Foto wajib diisi'], 422);
            }

            if (!$request->has('latitude') || !$request->has('longitude')) {
                return response()->json(['status' => 'error', 'message' => 'Lokasi wajib diisi'], 422);
            }

            $lokasiUser = $request->latitude . ', ' . $request->longitude;

            $image_parts = explode(";base64,", $request->foto);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'absensi/' . uniqid() . '.png';
            Storage::disk('public')->put($fileName, $image_base64);

            $isAbsenMasuk = $request->is_absen_masuk;
            $isWfh = $request->has('jenis_presensi') && $request->jenis_presensi === 'wfh';
            $isOnsite = $request->has('jenis_presensi') && $request->jenis_presensi === 'onsite';

            if ($isAbsenMasuk && !$presensi) {
                $jamMasukSetting = Carbon::parse($jamKerja->jam_masuk);
                
                // Set status berdasarkan jenis presensi
                if ($isWfh) {
                    $statusMasuk = '4'; // Status khusus untuk WFH
                    $jenisPresensi = 'wfh';
                } else if ($isOnsite) {
                    $statusMasuk = '3'; // Status khusus untuk Onsite
                    $jenisPresensi = 'onsite';
                } else {
                    $statusMasuk = $jamSekarang->lte($jamMasukSetting) ? '1' : '2'; // 1=Tepat waktu, 2=Terlambat
                    $jenisPresensi = 'normal';
                }
                
                $presensi = new Presensi();
                $presensi->karyawan_id = $karyawan->id;
                $presensi->tgl_presensi = $tglSekarang;
                $presensi->jam_in = $jamSekarang->format('H:i:s');
                $presensi->foto_in = $fileName;
                $presensi->lokasi_in = $lokasiUser;
                $presensi->status_presensi_in = $statusMasuk;
                $presensi->jenis_presensi = $jenisPresensi;
                $presensi->status = 'h';
                $presensi->kode_jam_kerja = $jamKerja->kode_jam_kerja;
                
                // Tambahkan keterangan jika ada
                if ($request->has('keterangan') && !empty($request->keterangan)) {
                    $presensi->keterangan = $request->keterangan;
                }
                
                $presensi->save();

                try {
                    $telegramService->sendAttendanceNotification($presensi);
                } catch (\Exception $e) {
                    \Log::error('Error sending Telegram notification: ' . $e->getMessage());
                }

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Absen masuk berhasil', 
                    'data' => $presensi,
                    'jenis_presensi' => $jenisPresensi
                ]);
            }
            else if (!$isAbsenMasuk && $presensi) {
                // Absen pulang
                $presensi->jam_out = $jamSekarang->format('H:i:s');
                $presensi->foto_out = $fileName;
                $presensi->lokasi_out = $lokasiUser;
                $presensi->save();

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Absen pulang berhasil', 
                    'data' => $presensi
                ]);
            } else if (!$isAbsenMasuk && !$presensi) {
                 return response()->json(['status' => 'error', 'message' => 'Anda belum melakukan absen masuk hari ini'], 400);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah melakukan absen masuk hari ini'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error in storeAbsen: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // PERBAIKAN: Mengisi method izin() dengan logika yang dibutuhkan oleh view
    public function izin()
    {
        $user = Auth::guard('frontend')->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
             return redirect()->route('frontend.login')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // 1. Ambil semua jenis cuti yang aktif
        $rekap_izin = PengajuanIzin::where('karyawan_id', $karyawan->id)
            ->where('jenis_pengajuan', 'Izin')
            ->whereYear('tanggal_awal', date('Y'))
            ->whereMonth('tanggal_awal', date('m'))
            ->where('status', 'approved')
            ->sum('jumlah_hari');

        $jenis_cuti = Cuti::get();

        return view('frontend.dashboard', compact(
            'greeting',
            'kehadiran', 'izin', 'terlambat', 'jam_masuk', 'jam_pulang', 
            'status_masuk', 'presensi', 'leaderboard', 'pengumuman'
        ));
    }

    public function profile()
    {
        return view('frontend.profile');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::guard('frontend')->user();

        if ($request->hasFile('avatar')) {
            $newPath = $request->file('avatar')->store('avatars', 'public');

            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $user->avatar_url = $newPath;
            $user->save();
        }

        return back()->with('success', 'Avatar berhasil diperbarui');
    }

    public function logout(Request $request)
    {
        Auth::guard('frontend')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('frontend.login');
    }
}

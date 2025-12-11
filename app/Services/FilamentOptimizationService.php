<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FilamentOptimizationService
{
    /**
     * Get optimized dashboard statistics
     */
    public static function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', 300, function () {
            $today = Carbon::today();
            
            // Use single query with subqueries for better performance
            $stats = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM presensi WHERE DATE(tgl_presensi) = ? AND jam_in <= '08:30:59') as hadir,
                    (SELECT COUNT(*) FROM presensi WHERE DATE(tgl_presensi) = ? AND jam_in > '08:30:59') as terlambat,
                    (SELECT COUNT(*) FROM pengajuan_izin 
                     WHERE jenis_pengajuan IN ('Izin', 'Cuti') 
                     AND status = 'approved' 
                     AND DATE(tanggal_awal) <= ? 
                     AND DATE(tanggal_akhir) >= ?) as izin,
                    (SELECT COUNT(*) FROM pengajuan_izin 
                     WHERE jenis_pengajuan = 'Sakit' 
                     AND status = 'approved' 
                     AND DATE(tanggal_awal) <= ? 
                     AND DATE(tanggal_akhir) >= ?) as sakit
            ", [$today, $today, $today, $today, $today, $today]);

            return [
                'hadir' => $stats[0]->hadir ?? 0,
                'terlambat' => $stats[0]->terlambat ?? 0,
                'izin' => $stats[0]->izin ?? 0,
                'sakit' => $stats[0]->sakit ?? 0,
            ];
        });
    }

    /**
     * Get optimized uang makan data per karyawan
     */
    public static function getUangMakanData(): array
    {
        return Cache::remember('uang_makan_weekly_per_karyawan', 300, function () {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->startOfWeek()->addDays(4);
            
            // Get uang makan per karyawan for this week
            $results = DB::select("
                SELECT 
                    k.nama as nama_karyawan,
                    COUNT(p.id) as hari_hadir,
                    (COUNT(p.id) * 20000) as total_uang_makan
                FROM karyawan k
                LEFT JOIN presensi p ON k.id = p.karyawan_id 
                    AND DATE(p.tgl_presensi) BETWEEN ? AND ?
                    AND p.jam_in <= '08:30:59'
                    AND p.jam_in IS NOT NULL
                GROUP BY k.id, k.nama
                HAVING COUNT(p.id) > 0
                ORDER BY total_uang_makan DESC
                LIMIT 10
            ", [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);

            $karyawanData = [];
            $pieLabels = [];
            $pieData = [];
            $totalUangMakan = 0;
            $maxUangMakan = 0;

            foreach ($results as $result) {
                $karyawanData[] = [
                    'nama' => $result->nama_karyawan,
                    'hari_hadir' => $result->hari_hadir,
                    'uang_makan' => $result->total_uang_makan
                ];
                
                $pieLabels[] = $result->nama_karyawan;
                $pieData[] = $result->total_uang_makan;
                $totalUangMakan += $result->total_uang_makan;
                
                if ($result->total_uang_makan > $maxUangMakan) {
                    $maxUangMakan = $result->total_uang_makan;
                }
            }

            return [
                'karyawan' => $karyawanData,
                'pieLabels' => $pieLabels,
                'pieData' => $pieData,
                'totalUangMakan' => $totalUangMakan,
                'maxUangMakan' => $maxUangMakan,
                'periode' => $startOfWeek->locale('id')->translatedFormat('j M') . ' - ' . $endOfWeek->locale('id')->translatedFormat('j M Y')
            ];
        });
    }

    /**
     * Get optimized piket schedule
     */
    public static function getPiketSchedule(): array
    {
        return Cache::remember('piket_schedule_monthly', 1800, function () {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            $piketData = DB::select("
                SELECT 
                    WEEK(tanggal_awal_piket, 1) - WEEK(DATE_SUB(tanggal_awal_piket, INTERVAL DAYOFMONTH(tanggal_awal_piket) - 1 DAY), 1) + 1 as minggu,
                    nama_karyawan
                FROM piket 
                WHERE MONTH(tanggal_awal_piket) = ?
                AND YEAR(tanggal_awal_piket) = ?
                AND jenis_piket = 'piket_mingguan'
                ORDER BY tanggal_awal_piket ASC
            ", [$currentMonth, $currentYear]);

            $jadwalPiket = [];
            for ($minggu = 1; $minggu <= 4; $minggu++) {
                $nama = 'Belum ada';
                foreach ($piketData as $piket) {
                    if ($piket->minggu == $minggu) {
                        $nama = $piket->nama_karyawan;
                        break;
                    }
                }
                
                $jadwalPiket[] = [
                    'minggu' => $minggu,
                    'nama' => $nama
                ];
            }

            return [
                'jadwal' => $jadwalPiket,
                'bulan' => Carbon::now()->locale('id')->translatedFormat('F Y')
            ];
        });
    }

    /**
     * Clear all dashboard caches
     */
    public static function clearDashboardCache(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('uang_makan_weekly');
        Cache::forget('piket_schedule_monthly');
    }

    /**
     * Optimize database connections
     */
    public static function optimizeDatabase(): void
    {
        // Set MySQL session variables for better performance
        DB::statement('SET SESSION query_cache_type = ON');
        DB::statement('SET SESSION query_cache_size = 67108864'); // 64MB
        DB::statement('SET SESSION tmp_table_size = 67108864'); // 64MB
        DB::statement('SET SESSION max_heap_table_size = 67108864'); // 64MB
        
        // Enable persistent connections
        config(['database.connections.mysql.options' => [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_TIMEOUT => 30,
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        ]]);
    }
}
<?php

use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use App\Models\User;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Integration tests for leave request flow with working days calculation
 * These tests verify the complete leave request process from submission to approval
 */

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->workingDaysCalculator = new WorkingDaysCalculator();
    
    // Create test user and karyawan
    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $this->karyawan = Karyawan::create([
        'user_id' => $this->user->id,
        'nama' => 'Test Karyawan',
        'nik' => '1234567890',
        'jabatan' => 'Developer',
        'no_hp' => '081234567890',
        'sisa_cuti_tahunan' => 12,
    ]);
    
    // Create test cuti types
    $this->cutiTahunan = Cuti::create([
        'nama_cuti' => 'Cuti Tahunan',
        'kode_cuti' => 'CT001',
        'jml_hari' => 12,
        'potong_cuti' => true,
    ]);
    
    $this->cutiSakit = Cuti::create([
        'nama_cuti' => 'Cuti Sakit',
        'kode_cuti' => 'CS001',
        'jml_hari' => 0,
        'potong_cuti' => false,
    ]);
});

describe('Leave Request Integration Flow', function () {
    
    it('successfully processes annual leave request with working days calculation', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Cuti',
            'cuti_id' => $this->cutiTahunan->id,
            'tanggal_mulai' => '2024-01-01', // Monday
            'tanggal_selesai' => '2024-01-05', // Friday
            'keterangan' => 'Annual leave test',
        ]);
        
        // Should redirect to index with success message
        $response->assertRedirect(route('frontend.izin.index'));
        $response->assertSessionHas('success');
        
        // Verify pengajuan was created with correct working days
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->not->toBeNull();
        expect($pengajuan->jumlah_hari)->toBe(5); // 5 working days
        expect($pengajuan->sisa_cuti)->toBe(7); // 12 - 5
        expect($pengajuan->jenis_pengajuan)->toBe('Cuti');
        expect($pengajuan->status)->toBe('pending');
    });
    
    it('successfully processes sick leave request without balance deduction', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Sakit',
            'tanggal_mulai' => '2024-01-01', // Monday
            'tanggal_selesai' => '2024-01-03', // Wednesday
            'keterangan' => 'Sick leave test',
        ]);
        
        $response->assertRedirect(route('frontend.izin.index'));
        $response->assertSessionHas('success');
        
        // Verify pengajuan was created without balance deduction
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->not->toBeNull();
        expect($pengajuan->jumlah_hari)->toBe(3); // 3 working days
        expect($pengajuan->sisa_cuti)->toBeNull(); // No balance deduction
        expect($pengajuan->jenis_pengajuan)->toBe('Sakit');
    });
    
    it('successfully processes personal leave request without balance deduction', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Izin',
            'tanggal_mulai' => '2024-01-08', // Monday
            'tanggal_selesai' => '2024-01-09', // Tuesday
            'keterangan' => 'Personal leave test',
        ]);
        
        $response->assertRedirect(route('frontend.izin.index'));
        $response->assertSessionHas('success');
        
        // Verify pengajuan was created without balance deduction
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->not->toBeNull();
        expect($pengajuan->jumlah_hari)->toBe(2); // 2 working days
        expect($pengajuan->sisa_cuti)->toBeNull(); // No balance deduction
        expect($pengajuan->jenis_pengajuan)->toBe('Izin');
    });
    
    it('rejects annual leave request when insufficient balance', function () {
        // Set low balance
        $this->karyawan->update(['sisa_cuti_tahunan' => 2]);
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Cuti',
            'cuti_id' => $this->cutiTahunan->id,
            'tanggal_mulai' => '2024-01-01', // Monday
            'tanggal_selesai' => '2024-01-05', // Friday (5 working days)
            'keterangan' => 'Insufficient balance test',
        ]);
        
        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Verify no pengajuan was created
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->toBeNull();
    });
    
    it('correctly handles weekend-only leave requests', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Cuti',
            'cuti_id' => $this->cutiTahunan->id,
            'tanggal_mulai' => '2024-01-06', // Saturday
            'tanggal_selesai' => '2024-01-07', // Sunday
            'keterangan' => 'Weekend leave test',
        ]);
        
        $response->assertRedirect(route('frontend.izin.index'));
        $response->assertSessionHas('success');
        
        // Verify pengajuan was created with 0 working days
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->not->toBeNull();
        expect($pengajuan->jumlah_hari)->toBe(0); // 0 working days
        expect($pengajuan->sisa_cuti)->toBe(12); // No balance deduction for 0 days
    });
    
    it('correctly handles leave requests spanning weekends', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Cuti',
            'cuti_id' => $this->cutiTahunan->id,
            'tanggal_mulai' => '2024-01-05', // Friday
            'tanggal_selesai' => '2024-01-08', // Monday (spans weekend)
            'keterangan' => 'Weekend spanning leave test',
        ]);
        
        $response->assertRedirect(route('frontend.izin.index'));
        $response->assertSessionHas('success');
        
        // Verify pengajuan was created with correct working days
        $pengajuan = PengajuanIzin::where('karyawan_id', $this->karyawan->id)->first();
        expect($pengajuan)->not->toBeNull();
        expect($pengajuan->jumlah_hari)->toBe(2); // Friday + Monday = 2 working days
        expect($pengajuan->sisa_cuti)->toBe(10); // 12 - 2
    });
    
    it('validates required fields in leave request', function () {
        $this->actingAs($this->user, 'web');
        
        // Test missing jenis_izin
        $response = $this->post(route('frontend.izin.store'), [
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'keterangan' => 'Missing jenis_izin',
        ]);
        
        $response->assertSessionHasErrors(['jenis_izin']);
        
        // Test missing tanggal_mulai
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Izin',
            'tanggal_selesai' => '2024-01-05',
            'keterangan' => 'Missing tanggal_mulai',
        ]);
        
        $response->assertSessionHasErrors(['tanggal_mulai']);
        
        // Test missing keterangan
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Izin',
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
        ]);
        
        $response->assertSessionHasErrors(['keterangan']);
    });
    
    it('validates date range in leave request', function () {
        $this->actingAs($this->user, 'web');
        
        // Test end date before start date
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Izin',
            'tanggal_mulai' => '2024-01-05',
            'tanggal_selesai' => '2024-01-01', // Before start date
            'keterangan' => 'Invalid date range',
        ]);
        
        $response->assertSessionHasErrors(['tanggal_selesai']);
    });
    
    it('requires cuti_id when jenis_izin is Cuti', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->post(route('frontend.izin.store'), [
            'jenis_izin' => 'Cuti',
            // Missing cuti_id
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'keterangan' => 'Missing cuti_id',
        ]);
        
        $response->assertSessionHasErrors(['cuti_id']);
    });
});

describe('Leave Request Index Page', function () {
    
    it('displays leave request form and history correctly', function () {
        // Create some test leave requests
        PengajuanIzin::create([
            'kode_izin' => 'IZ240101001',
            'karyawan_id' => $this->karyawan->id,
            'cuti_id' => $this->cutiTahunan->id,
            'tanggal_awal' => '2024-01-01',
            'tanggal_akhir' => '2024-01-05',
            'jumlah_hari' => 5,
            'sisa_cuti' => 7,
            'keterangan' => 'Test annual leave',
            'status' => 'pending',
            'jenis_pengajuan' => 'Cuti',
        ]);
        
        $this->actingAs($this->user, 'web');
        
        $response = $this->get(route('frontend.izin.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Pengajuan Izin');
        $response->assertSee('Form Pengajuan');
        $response->assertSee('Riwayat Izin');
        $response->assertSee('Test annual leave');
        $response->assertSee('Pending');
    });
    
    it('shows working days calculation in frontend', function () {
        $this->actingAs($this->user, 'web');
        
        $response = $this->get(route('frontend.izin.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Jumlah Hari Kerja');
        // Verify JavaScript function exists for working days calculation
        $response->assertSee('calculateWorkingDays');
    });
});
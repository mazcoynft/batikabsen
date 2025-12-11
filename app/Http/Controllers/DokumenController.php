<?php

namespace App\Http\Controllers;

use App\Models\DokumenKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return redirect()->route('frontend.dashboard')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $tipe = $request->get('tipe', 'slip_gaji'); // default slip_gaji
        
        // Get documents
        $dokumens = DokumenKaryawan::where('nik', $karyawan->nik)
            ->where('tipe', $tipe)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.dokumen.index', compact('dokumens', 'karyawan', 'tipe'));
    }
    
    public function markAsRead($id)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('id_users', $user->id)->first();
        
        if (!$karyawan) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $dokumen = DokumenKaryawan::where('id', $id)
            ->where('nik', $karyawan->nik)
            ->first();
        
        if ($dokumen) {
            $dokumen->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Not found'], 404);
    }
}

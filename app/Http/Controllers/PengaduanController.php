<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    /**
     * Menampilkan semua data pengaduan untuk Admin.
     */
    public function adminIndex()
    {
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        return view('pengaduan.admin', compact('pengaduans'));
    }

    /**
     * Menampilkan semua data pengaduan untuk Petugas.
     */
    public function petugasIndex()
    {
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        return view('pengaduan.petugas', compact('pengaduans'));
    }

    /**
     * Menampilkan detail pengaduan (untuk modal).
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['user', 'kategori'])->findOrFail($id);

        return response()->json($pengaduan);
    }

    /**
     * Mengupdate status pengaduan.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status pengaduan berhasil diupdate.');
    }
}
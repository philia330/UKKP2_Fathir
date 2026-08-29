<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;

class PengaduanController extends Controller
{
    /**
     * Menampilkan semua data pengaduan untuk Admin.
     */
    public function adminIndex()
    {
        $pengaduans = Pengaduan::with('user')->latest()->paginate(10);

        return view('pengaduan.admin', compact('pengaduans'));
    }

    /**
     * Menampilkan semua data pengaduan untuk Petugas.
     */
    public function petugasIndex()
    {
        $pengaduans = Pengaduan::with('user')->latest()->paginate(10);

        return view('pengaduan.petugas', compact('pengaduans'));
    }
}
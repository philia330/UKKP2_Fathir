<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

/**
 * Controller untuk menampilkan semua data pengaduan dan mengelola statusnya.
 *
 * Controller ini dipakai baik oleh admin maupun petugas untuk melihat laporan,
 * membuka detail pengaduan, dan memperbarui status tanggapan.
 */
class PengaduanController extends Controller
{
    /**
     * Menampilkan semua data pengaduan untuk Admin.
     *
     * Data diambil lengkap dengan relasi user dan kategori supaya admin
     * bisa melihat siapa yang membuat pengaduan dan jenis pengaduannya.
     */
    public function adminIndex()
    {
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        return view('pengaduan.admin', compact('pengaduans'));
    }

    /**
     * Menampilkan semua data pengaduan untuk Petugas.
     *
     * Tujuannya sama seperti admin, hanya layout dan aksesnya yang dibedakan
     * sesuai role yang login.
     */
    public function petugasIndex()
    {
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        return view('pengaduan.petugas', compact('pengaduans'));
    }

    /**
     * Menampilkan detail pengaduan dalam format JSON.
     *
     * Data ini dipanggil oleh modal di halaman admin dan petugas untuk menampilkan
     * detail lengkap tanpa harus pindah halaman.
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['user', 'kategori'])->findOrFail($id);

        return response()->json($pengaduan);
    }

    /**
     * Mengupdate status pengaduan.
     *
     * Status hanya boleh menerima tiga nilai: menunggu, diproses, dan selesai.
     * Setelah diubah, aplikasi akan kembali ke halaman sebelumnya.
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
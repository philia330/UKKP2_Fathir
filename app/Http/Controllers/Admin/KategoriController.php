<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola kategori pengaduan.
 *
 * Admin menggunakan controller ini agar kategori pengaduan dapat dibuat,
 * dilihat, diubah, dan dihapus sesuai kebutuhan data sistem.
 */
class KategoriController extends Controller
{
    /**
     * Menampilkan daftar semua kategori yang tersedia.
     *
     * Daftar ini digunakan di halaman admin untuk mengelola jenis pengaduan,
     * misalnya keluhan IT, fasilitas, keamanan, dan lain-lain.
     */
    public function index()
    {
        $kategoris = Kategori::latest()->paginate(10);

        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Menampilkan form untuk menambah kategori baru.
     *
     * Form ini berisi input nama kategori dan deskripsi singkat.
     */
    public function create()
    {
        return view('admin.kategori.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     *
     * Nama kategori harus unik agar tidak ada dua kategori dengan nama sama.
     * Deskripsi bersifat opsional karena bisa dibiarkan kosong.
     */
    public function store(Request $request)
    {
        // Validasi data input agar kategori valid dan tidak duplikat.
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan data baru ke tabel kategori.
        Kategori::create($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu kategori.
     *
     * Halaman ini berisi informasi lengkap kategori termasuk jumlah pengaduan
     * yang terkait dengan kategori tersebut.
     */
    public function show(Kategori $kategori)
    {
        return view('admin.kategori.show', compact('kategori'));
    }

    /**
     * Menampilkan form edit kategori.
     *
     * Data lama akan muncul agar admin bisa mengubah nama atau deskripsi
     * tanpa harus input ulang dari awal.
     */
    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    /**
     * Memperbarui data kategori yang dipilih.
     *
     * Validasi memastikan nama kategori tetap unik, kecuali untuk kategori yang
     * sedang diedit agar tidak error karena data itu sendiri.
     */
    public function update(Request $request, Kategori $kategori)
    {
        // Validasi data input, nama unik kecuali milik kategori ini sendiri.
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        // Update data kategori dengan data baru.
        $kategori->update($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database.
     *
     * Setelah dihapus, kategori tersebut tidak akan muncul lagi di sistem.
     * Pada saat pengaduan terkait kategori ini bisa tetap ada dengan relasi null
     * sesuai konfigurasi database jika karena onDelete set null.
     */
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

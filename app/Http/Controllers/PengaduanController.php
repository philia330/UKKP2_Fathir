<?php

// ============================================================
// NAMESPACE
// Namespace itu seperti "alamat folder" untuk class ini.
// Class ini berada di folder: app/Http/Controllers
// ============================================================
namespace App\Http\Controllers;

// ============================================================
// IMPORT (use)
// "use" artinya memanggil class lain supaya bisa dipakai di file ini.
// Tanpa "use", PHP tidak tahu class Pengaduan dan Request itu di mana.
// ============================================================

// Model Pengaduan = penghubung antara kode PHP dan tabel "pengaduans" di database.
use App\Models\Pengaduan;

// Request = class bawaan Laravel untuk membaca data kiriman user
// (misalnya data dari form atau dari URL).
use Illuminate\Http\Request;

/**
 * ============================================================
 * CLASS: PengaduanController
 * ============================================================
 * Class = "cetakan" yang berisi kumpulan method (fungsi) yang saling berkaitan.
 * Controller = "pengatur lalu lintas" aplikasi. Tugasnya:
 *   1. Menerima permintaan (request) dari user lewat route.
 *   2. Mengambil / mengolah data lewat Model.
 *   3. Mengirim hasilnya ke View (tampilan) atau sebagai JSON.
 *
 * Controller ini dipakai oleh admin dan petugas untuk:
 *   - melihat daftar pengaduan,
 *   - melihat detail pengaduan,
 *   - mengubah status pengaduan.
 *
 * "extends Controller" artinya class ini mewarisi (menurunkan) semua fitur
 * dari class Controller bawaan Laravel (pewarisan / inheritance).
 */
class PengaduanController extends Controller
{
    /**
     * ------------------------------------------------------------
     * METHOD: adminIndex()
     * ------------------------------------------------------------
     * Method = fungsi yang berada di dalam class.
     * "public" artinya method ini boleh dipanggil dari luar class (misalnya oleh route).
     *
     * Fungsi : menampilkan semua pengaduan di halaman ADMIN.
     *
     * ALUR KERJA:
     *   1. Admin membuka halaman daftar pengaduan.
     *   2. Route memanggil method ini.
     *   3. Method mengambil data pengaduan dari database (10 data per halaman).
     *   4. Data dikirim ke view 'pengaduan.admin' untuk ditampilkan.
     */
    public function adminIndex()
    {
        // VARIABLE: $pengaduans
        // Variable = "kotak" penyimpan data. Di PHP selalu diawali tanda $.
        // Isinya: kumpulan data pengaduan (sudah dibagi per halaman).
        //
        // Penjelasan rantai perintahnya (dibaca dari kiri ke kanan):
        //   Pengaduan::with(['user', 'kategori'])
        //       -> Pengaduan = model (tabel pengaduans).
        //       -> with() = ambil juga data relasinya sekaligus, yaitu data
        //          user (pembuat pengaduan) dan kategori (jenis pengaduan).
        //          Ini disebut "Eager Loading", gunanya supaya aplikasi
        //          tidak lambat karena query database tidak berulang-ulang.
        //   ->latest()
        //       -> urutkan dari data paling BARU ke paling lama.
        //   ->paginate(10)
        //       -> bagi data per halaman, 1 halaman berisi 10 data.
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        // view('pengaduan.admin') = tampilkan file tampilan:
        //   resources/views/pengaduan/admin.blade.php
        // compact('pengaduans') = kirim variable $pengaduans ke view tersebut,
        //   sehingga di view bisa dipakai lagi dengan nama $pengaduans.
        // "return" = mengembalikan hasil sebagai respon ke browser user.
        return view('pengaduan.admin', compact('pengaduans'));
    }

    /**
     * ------------------------------------------------------------
     * METHOD: petugasIndex()
     * ------------------------------------------------------------
     * Fungsi : menampilkan semua pengaduan di halaman PETUGAS.
     *
     * Isinya sama persis dengan adminIndex(). Bedanya hanya di VIEW yang dipakai
     * (tampilan / layout petugas). Pembatasan siapa yang boleh masuk
     * diatur oleh middleware di route, bukan di method ini.
     *
     * ALUR KERJA:
     *   1. Petugas membuka halaman daftar pengaduan.
     *   2. Route (setelah lolos middleware) memanggil method ini.
     *   3. Ambil data pengaduan + relasi user & kategori dari database.
     *   4. Kirim ke view 'pengaduan.petugas'.
     */
    public function petugasIndex()
    {
        // VARIABLE: $pengaduans
        // Cara kerjanya sama dengan adminIndex(): ambil data terbaru,
        // lengkap dengan user dan kategori, dibagi 10 data per halaman.
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        // Tampilkan view khusus petugas: resources/views/pengaduan/petugas.blade.php
        return view('pengaduan.petugas', compact('pengaduans'));
    }

    /**
     * ------------------------------------------------------------
     * METHOD: show($id)
     * ------------------------------------------------------------
     * Fungsi : mengambil detail SATU pengaduan dan mengirimnya dalam format JSON.
     *
     * JSON = format data berbentuk teks yang mudah dibaca JavaScript.
     * Method ini dipanggil lewat JavaScript (AJAX/fetch) oleh modal (popup)
     * di halaman admin dan petugas, jadi halaman tidak perlu pindah/refresh.
     *
     * PARAMETER:
     *   $id = nomor id pengaduan yang ingin dilihat (diambil dari URL).
     *   Parameter = data titipan yang dikirim ke dalam method.
     *
     * ALUR KERJA:
     *   1. User klik tombol "Detail" -> JavaScript meminta data ke URL pengaduan/{id}.
     *   2. Method mencari pengaduan berdasarkan $id.
     *   3. Jika ketemu, data dikirim sebagai JSON ke JavaScript.
     *   4. JavaScript menampilkan data itu di dalam modal.
     */
    public function show($id)
    {
        // VARIABLE: $pengaduan (satu data, bukan banyak)
        //   with(['user', 'kategori']) -> ikut ambil data user dan kategorinya.
        //   findOrFail($id) -> cari data dengan id tersebut.
        //       - Kalau ADA     -> data dikembalikan.
        //       - Kalau TIDAK   -> otomatis muncul error 404 (halaman tidak ditemukan).
        $pengaduan = Pengaduan::with(['user', 'kategori'])->findOrFail($id);

        // response()->json(...) = ubah data menjadi format JSON lalu kirim ke
        // browser. Bukan view, karena yang dibutuhkan hanya datanya.
        return response()->json($pengaduan);
    }

    /**
     * ------------------------------------------------------------
     * METHOD: updateStatus(Request $request, $id)
     * ------------------------------------------------------------
     * Fungsi : mengubah status sebuah pengaduan.
     *
     * Status hanya boleh salah satu dari 3 nilai:
     *   - menunggu  (belum ditangani)
     *   - diproses  (sedang ditangani)
     *   - selesai   (sudah selesai)
     *
     * PARAMETER:
     *   $request = objek Request, isinya data yang dikirim dari form
     *              (di sini: pilihan status baru).
     *   $id      = id pengaduan yang statusnya mau diubah (dari URL).
     *
     * ALUR KERJA:
     *   1. Admin/petugas memilih status baru lalu menekan tombol simpan.
     *   2. Data status divalidasi (dicek apakah nilainya benar).
     *   3. Pengaduan dicari berdasarkan $id.
     *   4. Status pengaduan diupdate di database.
     *   5. User dikembalikan ke halaman sebelumnya + pesan sukses.
     */
    public function updateStatus(Request $request, $id)
    {
        // VARIABLE: $validated
        // validate() = memeriksa data kiriman user sebelum dipakai.
        // Ini penting supaya user tidak bisa mengirim status sembarangan.
        //
        // Aturan 'status' => 'required|in:menunggu,diproses,selesai':
        //   - required -> wajib diisi, tidak boleh kosong.
        //   - in:...   -> nilainya harus salah satu dari: menunggu, diproses, selesai.
        //
        // Kalau data TIDAK valid: proses berhenti dan user dikembalikan ke form
        // dengan pesan error. Kalau valid: data bersih disimpan di $validated.
        $validated = $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai',
        ]);

        // VARIABLE: $pengaduan
        // Cari pengaduan berdasarkan $id. Kalau tidak ada -> error 404.
        $pengaduan = Pengaduan::findOrFail($id);

        // update() = simpan perubahan ke database.
        // Hanya kolom 'status' yang diubah, nilainya diambil dari $validated['status'].
        // Tanda [ ] dipakai untuk mengambil isi dari array berdasarkan nama kuncinya.
        $pengaduan->update(['status' => $validated['status']]);

        // redirect()->back() = kembalikan user ke halaman sebelumnya.
        // with('success', '...') = titipkan pesan sementara (flash message)
        //   bernama 'success'. Pesan ini bisa ditampilkan di view lalu hilang
        //   setelah halaman dimuat ulang.
        return redirect()->back()->with('success', 'Status pengaduan berhasil diupdate.');
    }
}
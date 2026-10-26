<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER dalam pola MVC (Model - View - Controller).
|
|   Browser (admin klik/ketik)
|        |
|        v
|   ROUTE (routes/web.php)  -> menentukan URL ini diarahkan ke method mana
|        |
|        v
|   MIDDLEWARE              -> "satpam", cek login / cek role admin dulu
|        |
|        v
|   CONTROLLER (file ini)   -> "otak", menerima request lalu memproses
|        |
|        v
|   MODEL (Kategori)        -> "penghubung ke database"
|        |
|        v
|   VIEW (blade)            -> "tampilan" yang dikirim kembali ke browser
|
| Fitur file ini disebut CRUD:
|   C = Create (tambah)  -> create() + store()
|   R = Read   (lihat)   -> index() + show()
|   U = Update (ubah)    -> edit() + update()
|   D = Delete (hapus)   -> destroy()
*/

// NAMESPACE = "alamat folder" class ini di dalam project Laravel.
// Letaknya di app/Http/Controllers/Admin/. Dipakai agar Laravel bisa
// menemukan class ini secara otomatis (autoload).
namespace App\Http\Controllers\Admin;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.
// Tanpa use, kita harus menulis alamat lengkapnya setiap kali dipakai.

// Controller induk (parent). Semua controller mewarisi class ini.
use App\Http\Controllers\Controller;

// MODEL Kategori = class yang mewakili tabel "kategori" di database.
// Lewat model ini kita bisa ambil, tambah, ubah, dan hapus data kategori.
use App\Models\Kategori;

// Request = class bawaan Laravel yang membawa semua data kiriman dari
// browser (isi form, parameter URL, dll).
use Illuminate\Http\Request;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * Controller untuk mengelola kategori pengaduan.
 *
 * Admin menggunakan controller ini agar kategori pengaduan dapat dibuat,
 * dilihat, diubah, dan dihapus sesuai kebutuhan data sistem.
 *
 * "extends Controller" artinya KategoriController MEWARISI (pewarisan/
 * inheritance) semua kemampuan dari class Controller induknya.
 */
class KategoriController extends Controller
{
    /**
     * METHOD index() -> bagian "R" (Read) untuk melihat DAFTAR data.
     *
     * Menampilkan daftar semua kategori yang tersedia.
     *
     * Daftar ini digunakan di halaman admin untuk mengelola jenis pengaduan,
     * misalnya keluhan IT, fasilitas, keamanan, dan lain-lain.
     *
     * ALUR:
     *  1. Ambil data kategori dari database lewat Model.
     *  2. Urutkan dari yang terbaru, bagi per halaman (10 data/halaman).
     *  3. Kirim datanya ke halaman (view) untuk ditampilkan.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function index()
    {
        // $kategoris = VARIABLE (wadah untuk menyimpan data sementara).
        // Tanda $ selalu menandakan variable di PHP.
        //
        // Kategori::latest()->paginate(10) dibaca dari kiri ke kanan:
        //   Kategori      -> pakai Model Kategori (tabel "kategori")
        //   ::            -> cara memanggil method static milik class
        //   latest()      -> urutkan dari data TERBARU (berdasarkan created_at)
        //   ->            -> lanjut memanggil method berikutnya (method chaining)
        //   paginate(10)  -> ambil 10 data saja per halaman, otomatis
        //                    membuat nomor halaman (1, 2, 3, ...)
        $kategoris = Kategori::latest()->paginate(10);

        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'admin.kategori.index' = file resources/views/admin/kategori/index.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        //
        // compact('kategoris') = membungkus variable $kategoris menjadi
        // array agar bisa dipakai di halaman blade dengan nama $kategoris.
        // Hasilnya sama dengan: ['kategoris' => $kategoris]
        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * METHOD create() -> bagian "C" (Create) tahap 1: TAMPILKAN FORM.
     *
     * Menampilkan form untuk menambah kategori baru.
     *
     * Form ini berisi input nama kategori dan deskripsi singkat.
     *
     * CATATAN: method ini HANYA menampilkan form kosong.
     * Penyimpanan datanya dilakukan oleh method store() di bawah.
     */
    public function create()
    {
        // Tidak ada data yang dikirim, karena form tambah memang kosong.
        // Langsung tampilkan halaman form tambah kategori.
        return view('admin.kategori.create');
    }

    /**
     * METHOD store() -> bagian "C" (Create) tahap 2: SIMPAN KE DATABASE.
     *
     * Menyimpan kategori baru ke database.
     *
     * Nama kategori harus unik agar tidak ada dua kategori dengan nama sama.
     * Deskripsi bersifat opsional karena bisa dibiarkan kosong.
     *
     * ALUR:
     *  1. Admin mengisi form lalu klik tombol Simpan.
     *  2. Data dikirim ke method ini lewat $request.
     *  3. Data dicek (validasi). Kalau salah -> kembali ke form + pesan error.
     *  4. Kalau benar -> simpan ke database.
     *  5. Pindah ke halaman daftar + pesan sukses.
     *
     * PARAMETER (data masukan method):
     *   Request $request = objek yang membawa semua isi form dari browser.
     *   "Request" adalah tipe datanya (class), "$request" adalah nama variable-nya.
     */
    public function store(Request $request)
    {
        // Validasi data input agar kategori valid dan tidak duplikat.
        //
        // $request->validate([...]) = fungsi untuk MEMERIKSA isi form.
        // Jika ada yang salah, Laravel otomatis menghentikan proses ini
        // dan mengembalikan admin ke form beserta pesan error-nya.
        // Jika semua benar, hasilnya (data yang sudah bersih) disimpan
        // ke variable $validated.
        $validated = $request->validate([
            // Aturan untuk input "nama":
            //   required  -> wajib diisi, tidak boleh kosong
            //   string    -> harus berupa teks
            //   max:255   -> panjang maksimal 255 karakter
            //   unique:kategori,nama -> tidak boleh sama dengan data lain
            //                           di tabel "kategori" kolom "nama"
            'nama' => 'required|string|max:255|unique:kategori,nama',

            // Aturan untuk input "deskripsi":
            //   nullable  -> boleh dikosongkan
            //   string    -> kalau diisi, harus berupa teks
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan data baru ke tabel kategori.
        //
        // Kategori::create(...) = membuat 1 baris data baru di tabel kategori.
        // Syaratnya: kolom 'nama' dan 'deskripsi' harus terdaftar di
        // $fillable pada Model Kategori (ini aturan keamanan Laravel).
        Kategori::create($validated);

        // redirect() = pindahkan browser ke halaman lain.
        // ->route('admin.kategori.index') = tujuan pindah, dipanggil lewat
        //     NAMA route (bukan URL), jadi aman kalau URL-nya berubah.
        // ->with('success', '...') = kirim pesan singkat (flash message)
        //     yang hanya muncul 1x di halaman tujuan, lalu hilang sendiri.
        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * METHOD show() -> bagian "R" (Read) untuk melihat DETAIL 1 data.
     *
     * Menampilkan detail satu kategori.
     *
     * Halaman ini berisi informasi lengkap kategori termasuk jumlah pengaduan
     * yang terkait dengan kategori tersebut.
     *
     * PARAMETER: Kategori $kategori
     * Ini fitur Laravel bernama ROUTE MODEL BINDING. Contohnya URL
     * /admin/kategori/5 -> Laravel otomatis mencari kategori ber-id 5
     * di database, lalu memasukkannya ke variable $kategori.
     * Jika id tidak ditemukan, Laravel otomatis menampilkan halaman 404.
     */
    public function show(Kategori $kategori)
    {
        // Kirim data satu kategori ($kategori) ke halaman detail.
        return view('admin.kategori.show', compact('kategori'));
    }

    /**
     * METHOD edit() -> bagian "U" (Update) tahap 1: TAMPILKAN FORM EDIT.
     *
     * Menampilkan form edit kategori.
     *
     * Data lama akan muncul agar admin bisa mengubah nama atau deskripsi
     * tanpa harus input ulang dari awal.
     *
     * Sama seperti show(), $kategori diambil otomatis dari database
     * lewat Route Model Binding berdasarkan id di URL.
     */
    public function edit(Kategori $kategori)
    {
        // Kirim data lama ke form, supaya kolom form terisi otomatis.
        return view('admin.kategori.edit', compact('kategori'));
    }

    /**
     * METHOD update() -> bagian "U" (Update) tahap 2: SIMPAN PERUBAHAN.
     *
     * Memperbarui data kategori yang dipilih.
     *
     * Validasi memastikan nama kategori tetap unik, kecuali untuk kategori yang
     * sedang diedit agar tidak error karena data itu sendiri.
     *
     * PARAMETER:
     *   Request $request   = isi form yang baru dikirim admin
     *   Kategori $kategori = data kategori LAMA yang sedang diedit
     */
    public function update(Request $request, Kategori $kategori)
    {
        // Validasi data input, nama unik kecuali milik kategori ini sendiri.
        $validated = $request->validate([
            // Bedanya dengan store(): ada tambahan ',' . $kategori->id
            //   $kategori->id = ambil nilai kolom id milik kategori ini.
            //   Tanda titik (.) di PHP = menggabungkan dua teks.
            // Artinya: "nama harus unik, KECUALI untuk baris dengan id ini".
            // Tanpa ini, menyimpan tanpa mengubah nama akan dianggap
            // duplikat dan muncul error, padahal itu datanya sendiri.
            'nama' => 'required|string|max:255|unique:kategori,nama,' . $kategori->id,

            // Deskripsi boleh kosong, kalau diisi harus berupa teks.
            'deskripsi' => 'nullable|string',
        ]);

        // Update data kategori dengan data baru.
        // ->update(...) = mengubah baris data yang SUDAH ada di database
        // (berbeda dengan create yang membuat baris BARU).
        $kategori->update($validated);

        // Kembali ke halaman daftar sambil membawa pesan sukses.
        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * METHOD destroy() -> bagian "D" (Delete) untuk MENGHAPUS data.
     *
     * Menghapus kategori dari database.
     *
     * Setelah dihapus, kategori tersebut tidak akan muncul lagi di sistem.
     * Pada saat pengaduan terkait kategori ini bisa tetap ada dengan relasi null
     * sesuai konfigurasi database jika karena onDelete set null.
     *
     * PENJELASAN onDelete set null:
     * Ini aturan di database (migration). Jika kategori dihapus, pengaduan
     * yang memakai kategori itu TIDAK ikut terhapus, hanya kolom
     * kategori_id-nya menjadi kosong (null).
     */
    public function destroy(Kategori $kategori)
    {
        // ->delete() = hapus baris data ini dari tabel kategori.
        $kategori->delete();

        // Kembali ke halaman daftar sambil membawa pesan sukses.
        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
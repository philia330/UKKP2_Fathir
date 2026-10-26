<?php

/*
|--------------------------------------------------------------------------
| ALUR UMUM FILE INI (BACA DULU)
|--------------------------------------------------------------------------
| Ini adalah CONTROLLER untuk CUSTOMER (bukan admin).
| Customer hanya bisa:
|   - melihat DAFTAR pengaduan miliknya sendiri  -> index()
|   - membuka FORM pengaduan baru                -> create()
|   - MENYIMPAN pengaduan baru                   -> store()
| (Tidak ada edit dan hapus, jadi customer tidak bisa mengubah laporan.)
|
|   Browser (customer klik/ketik)
|        |
|        v
|   ROUTE (routes/web.php)  -> URL diarahkan ke method yang tepat
|        |
|        v
|   MIDDLEWARE              -> "satpam": cek sudah login? role-nya customer?
|        |
|        v
|   CONTROLLER (file ini)   -> "otak": memproses permintaan
|        |
|        v
|   MODEL (Pengaduan, Kategori) -> "penghubung ke database"
|        |
|        v
|   VIEW (blade)            -> "tampilan" yang dikirim ke browser
*/

// NAMESPACE = "alamat folder" class ini di dalam project Laravel
// (app/Http/Controllers/Customer/). Dipisah dari folder Admin supaya rapi:
// controller admin di folder Admin, controller customer di folder Customer.
namespace App\Http\Controllers\Customer;

// USE = "memanggil" class lain supaya bisa dipakai di file ini.

// Controller induk (parent). Semua controller mewarisi class ini.
use App\Http\Controllers\Controller;

// MODEL Kategori = mewakili tabel "kategori" (jenis pengaduan).
use App\Models\Kategori;

// MODEL Pengaduan = mewakili tabel "pengaduan" (laporan dari customer).
use App\Models\Pengaduan;

// Request = class bawaan Laravel yang membawa semua data kiriman dari
// browser (isi form, file upload, dll).
use Illuminate\Http\Request;

// Auth = alat bawaan Laravel untuk mengetahui SIAPA user yang sedang login.
// Contoh: Auth::id() menghasilkan id user yang sedang login.
use Illuminate\Support\Facades\Auth;

/**
 * CLASS = "cetakan/blueprint" yang berisi kumpulan method (fungsi).
 *
 * Controller yang menangani proses pengaduan milik customer.
 *
 * Customer hanya bisa menambah dan melihat pengaduan yang dibuatnya sendiri,
 * sehingga data antar pelanggan tidak tercampur.
 *
 * "extends Controller" artinya PengaduanController MEWARISI (inheritance)
 * semua kemampuan dari class Controller induknya.
 */
class PengaduanController extends Controller
{
    /**
     * METHOD index() -> melihat DAFTAR pengaduan milik customer yang login.
     *
     * Menampilkan daftar pengaduan milik customer yang sedang login.
     *
     * Query difilter berdasarkan user_id agar customer hanya melihat
     * pengaduannya sendiri dan tidak dapat melihat laporan orang lain.
     *
     * ALUR:
     *  1. Cari tahu siapa yang sedang login (Auth::id()).
     *  2. Ambil HANYA pengaduan milik user itu dari database.
     *  3. Ikut ambil data kategorinya, urutkan terbaru, bagi per halaman.
     *  4. Kirim ke view untuk ditampilkan sebagai tabel.
     *
     * Method = fungsi yang berada DI DALAM class.
     * "public" artinya boleh dipanggil dari luar class (oleh Route).
     */
    public function index()
    {
        // $pengaduans = VARIABLE (wadah penyimpan data sementara).
        // Tanda $ selalu menandakan variable di PHP.
        //
        // Dibaca dari atas ke bawah (method chaining):
        //   Pengaduan::where('user_id', Auth::id())
        //       -> pakai Model Pengaduan, ambil hanya baris yang kolom
        //          user_id-nya SAMA DENGAN id user yang sedang login.
        //          (Ini filter keamanan: customer tidak bisa lihat milik orang lain.)
        //   ->with('kategori')
        //       -> ikut ambil data kategori dari relasi "kategori" (Eager Loading).
        //          Tanpa with(), tiap baris akan menjalankan query tambahan
        //          untuk mengambil kategori, sehingga aplikasi jadi lambat
        //          (disebut masalah N+1 query).
        //   ->latest()
        //       -> urutkan dari data TERBARU (kolom created_at).
        //   ->paginate(10)
        //       -> ambil 10 data per halaman, otomatis membuat nomor halaman.
        $pengaduans = Pengaduan::where('user_id', Auth::id())
            ->with('kategori')
            ->latest()
            ->paginate(10);

        // view() = fungsi bawaan Laravel untuk menampilkan halaman.
        // 'pengaduan.customer.index' = file
        //   resources/views/pengaduan/customer/index.blade.php
        // (tanda titik dibaca sebagai tanda folder "/").
        //
        // compact('pengaduans') = membungkus variable $pengaduans menjadi array
        // agar bisa dipakai di blade dengan nama $pengaduans.
        // Hasilnya sama dengan: ['pengaduans' => $pengaduans]
        return view('pengaduan.customer.index', compact('pengaduans'));
    }

    /**
     * METHOD create() -> TAMPILKAN FORM pengaduan baru.
     *
     * Menampilkan form untuk membuat pengaduan baru.
     *
     * Data kategori diambil dari database lalu ditampilkan dalam dropdown
     * supaya customer bisa memilih jenis pengaduan yang sesuai.
     *
     * CATATAN: method ini HANYA menampilkan form.
     * Penyimpanan datanya dilakukan oleh method store() di bawah.
     */
    public function create()
    {
        // Mengambil semua kategori untuk dropdown pilihan pengaduan.
        //
        // Kategori::orderBy('nama')->get() dibaca:
        //   Kategori        -> pakai Model Kategori (tabel "kategori")
        //   orderBy('nama') -> urutkan berdasarkan kolom nama (A sampai Z)
        //   get()           -> jalankan query dan ambil SEMUA hasilnya
        //                      (berbeda dengan paginate yang dibatasi per halaman)
        $kategoris = Kategori::orderBy('nama')->get();

        // Kirim daftar kategori ke form supaya bisa dibuat dropdown (select).
        return view('pengaduan.customer.create', compact('kategoris'));
    }

    /**
     * METHOD store() -> SIMPAN pengaduan baru ke database.
     *
     * Menyimpan pengaduan baru dari customer.
     *
     * user_id diambil dari akun yang sedang login, bukan dari input form,
     * supaya customer tidak bisa membuat pengaduan atas nama user lain.
     *
     * ALUR:
     *  1. Customer mengisi form lalu klik Kirim.
     *  2. Data dikirim ke method ini lewat $request.
     *  3. Data divalidasi. Kalau salah -> kembali ke form + pesan error.
     *  4. Kalau ada foto, foto disimpan ke folder penyimpanan.
     *  5. user_id diisi otomatis dari akun yang login.
     *  6. Data disimpan ke database, lalu pindah ke halaman daftar.
     *
     * PARAMETER (data masukan method):
     *   Request $request = objek yang membawa semua isi form dan file upload.
     *   "Request" = tipe datanya (class), "$request" = nama variable-nya.
     */
    public function store(Request $request)
    {
        // Validasi data dari form agar isian sesuai aturan.
        //
        // $request->validate([...]) = fungsi untuk MEMERIKSA isi form.
        // Jika ada yang salah, Laravel otomatis menghentikan proses dan
        // mengembalikan customer ke form beserta pesan error-nya.
        // Jika semua benar, data yang sudah bersih disimpan ke $validated
        // (berbentuk array: pasangan 'nama_field' => nilainya).
        $validated = $request->validate([
            // kategori_id:
            //   required            -> wajib dipilih
            //   exists:kategori,id  -> nilainya harus ADA di tabel "kategori"
            //                          kolom "id" (mencegah id palsu/ngawur)
            'kategori_id' => 'required|exists:kategori,id',

            // pengaduan (isi laporan):
            //   required -> wajib diisi
            //   string   -> harus berupa teks
            'pengaduan' => 'required|string',

            // foto (bukti):
            //   nullable -> boleh tidak upload foto
            //   image    -> kalau upload, harus file gambar (jpg, png, dll)
            //   max:2048 -> ukuran maksimal 2048 KB (sekitar 2 MB)
            'foto' => 'nullable|image|max:2048',
        ]);

        // Upload foto bukti jika user memilih file gambar.
        //
        // IF = percabangan (kondisi). Isinya hanya dijalankan kalau kondisinya benar.
        // $request->hasFile('foto') = true jika ada file bernama "foto" yang diupload.
        if ($request->hasFile('foto')) {
            // $request->file('foto') = ambil file yang diupload.
            // ->store('foto', 'public') = simpan file ke:
            //     'foto'   -> folder bernama "foto"
            //     'public' -> disk "public" (storage/app/public)
            // Laravel otomatis memberi nama file acak agar tidak bentrok,
            // lalu mengembalikan LOKASI file-nya (contoh: "foto/abc123.jpg").
            // Lokasi itulah yang disimpan di variable $path.
            $path = $request->file('foto')->store('foto', 'public');

            // Ganti isi $validated['foto'] dari file asli menjadi teks lokasinya,
            // karena yang disimpan di database adalah lokasi file, bukan filenya.
            $validated['foto'] = $path;
        }

        // Menambahkan user_id otomatis dari customer yang sedang login.
        // Auth::id() = id user yang login saat ini.
        // Ini lebih aman daripada mengambil dari form, karena data form
        // bisa diubah-ubah oleh orang yang berniat jahat.
        $validated['user_id'] = Auth::id();

        // Pengaduan::create($validated) = membuat 1 baris BARU di tabel pengaduan.
        // Syarat: kolom kategori_id, pengaduan, foto, dan user_id harus
        // terdaftar di $fillable pada Model Pengaduan (aturan keamanan Laravel).
        Pengaduan::create($validated);

        // redirect() = pindahkan browser ke halaman lain.
        // ->route('customer.pengaduan.index') = tujuan pindah, dipanggil lewat
        //     NAMA route (bukan URL), jadi aman kalau URL-nya berubah.
        // ->with('success', '...') = kirim pesan singkat (flash message)
        //     yang hanya muncul 1x di halaman tujuan, lalu hilang sendiri.
        return redirect()
            ->route('customer.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dibuat.');
    }
}
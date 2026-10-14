# 5. Kategori, pengaduan, status, dan upload foto

## Tahap 16 — CRUD kategori untuk admin

**Tujuan:** admin menyiapkan kategori yang dapat dipilih saat customer membuat laporan.  
**Persiapan:** Model `Kategori` dan route resource tersedia.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller Admin/KategoriController --resource
```

Isi lengkap **`app/Http/Controllers/Admin/KategoriController.php`**:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori dengan pagination.
    public function index()
    {
        return view('kategori.index', ['kategoris' => Kategori::latest()->paginate(10)]);
    }

    // Membuka form kategori baru.
    public function create()
    {
        return view('kategori.create', ['kategori' => new Kategori()]);
    }

    // Memvalidasi dan menyimpan kategori baru.
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kategori,nama'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Kategori::create($data);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Membuka form edit kategori yang dipilih.
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    // Memperbarui kategori tanpa menganggap nama kategori itu sendiri sebagai duplikat.
    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('kategori', 'nama')->ignore($kategori->id)],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategori->update($data);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // Menghapus kategori; foreign key laporan akan menjadi null.
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
```

## Tahap 17 — Customer membuat dan melihat pengaduan

**Tujuan:** membuat pengaduan dengan kategori dan foto opsional, lalu memastikan daftar hanya memuat laporan sendiri.  
**Persiapan:** migration, model, auth, dan route customer sudah tersedia.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller Customer/PengaduanController
```

Isi lengkap **`app/Http/Controllers/Customer/PengaduanController.php`**:

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    // Menampilkan hanya laporan customer yang sedang login.
    public function index()
    {
        $pengaduans = Pengaduan::where('user_id', Auth::id())
            ->with('kategori')
            ->latest()
            ->paginate(10);

        return view('pengaduan.customer-index', compact('pengaduans'));
    }

    // Menyediakan kategori aktif untuk pilihan form.
    public function create()
    {
        return view('pengaduan.create', ['kategoris' => Kategori::orderBy('nama')->get()]);
    }

    // Memvalidasi input; pemilik dan status ditetapkan oleh server, bukan form.
    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => ['required', 'exists:kategori,id'],
            'pengaduan' => ['required', 'string', 'min:10'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            // File berada pada storage/app/public/foto; path disimpan di database.
            $data['foto'] = $request->file('foto')->store('foto', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'menunggu';
        Pengaduan::create($data);

        return redirect()->route('customer.pengaduan.index')->with('success', 'Pengaduan berhasil dikirim.');
    }
}
```

## Tahap 18 — Daftar pengaduan dan perubahan status oleh admin/petugas

**Tujuan:** admin/petugas membaca semua laporan dan mengubah status hanya ke salah satu dari tiga status yang sah.  
**Persiapan:** route admin dan petugas menunjuk controller yang sama tetapi tetap dilindungi role masing-masing.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan make:controller PengaduanController
```

Isi lengkap **`app/Http/Controllers/PengaduanController.php`**:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    // Admin dan petugas melihat semua laporan; prefix menentukan link form status.
    public function index(Request $request)
    {
        $prefix = $request->user()->role;
        $pengaduans = Pengaduan::with(['user', 'kategori'])->latest()->paginate(10);

        return view('pengaduan.manage', compact('pengaduans', 'prefix'));
    }

    // Status dibatasi agar nilai yang tidak dikenal tidak masuk ke database.
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai'],
        ]);

        $pengaduan->update($data);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }
}
```

## Tahap 19 — Membuat symbolic link untuk foto

**Tujuan:** membuat file di disk `public` dapat dibaca melalui URL lokal.  
**Persiapan:** storage project tersedia; tidak perlu membuat folder foto manual.  
**Terminal:** CMD, root project.

```bat
cd /d D:\AplikasiPengaduan
php artisan storage:link
```

Laravel menghubungkan `public/storage` ke `storage/app/public`. Simpan path foto relatif seperti `foto/nama-acak.png`; tampilkan dengan `Storage::url($pengaduan->foto)`. Jangan menerima nama file/path tujuan dari input user.

## Uji tahap

Setelah view pada bab 6 selesai dan akun/kategori tersedia, uji alur end-to-end pada bab 7. Sebelum itu, verifikasi sintaks route:

```bat
cd /d D:\AplikasiPengaduan
php artisan route:list
```

**Hasil yang diharapkan:** customer hanya dapat mengakses `/customer/pengaduan`; admin dan petugas masing-masing memakai URL `/admin/pengaduan` atau `/petugas/pengaduan`. Tidak ada form yang menentukan `user_id` atau status awal sendiri.

**Error umum:**  
- Tidak ada pilihan kategori: admin perlu membuat kategori atau seeder pada bab 7 belum dijalankan.  
- Foto berhasil tersimpan tetapi tidak terlihat: jalankan `php artisan storage:link`, periksa `foto` path dan gunakan `Storage::url()`.  
- Gambar ditolak: hanya JPG/JPEG/PNG maksimal 2 MB yang diterima; sesuaikan ukuran dan tipe file.  
- Customer melihat laporan user lain: periksa query `where('user_id', Auth::id())` pada index dan dashboard customer.

**Checklist:** [ ] kategori bisa CRUD oleh admin [ ] form pengaduan memakai kolom yang sama dengan migration [ ] validasi foto aktif [ ] `user_id` berasal dari sesi login [ ] status awal selalu menunggu [ ] hanya admin/petugas bisa mengubah status.

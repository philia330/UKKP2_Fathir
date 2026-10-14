<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller yang menangani proses pengaduan milik customer.
 *
 * Customer hanya bisa menambah dan melihat pengaduan yang dibuatnya sendiri,
 * sehingga data antar pelanggan tidak tercampur.
 */
class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan milik customer yang sedang login.
     *
     * Query difilter berdasarkan user_id agar customer hanya melihat
     * pengaduannya sendiri dan tidak dapat melihat laporan orang lain.
     */
    public function index()
    {
        $pengaduans = Pengaduan::where('user_id', Auth::id())
            ->with('kategori')
            ->latest()
            ->paginate(10);

        return view('pengaduan.customer.index', compact('pengaduans'));
    }

    /**
     * Menampilkan form untuk membuat pengaduan baru.
     *
     * Data kategori diambil dari database lalu ditampilkan dalam dropdown
     * supaya customer bisa memilih jenis pengaduan yang sesuai.
     */
    public function create()
    {
        // Mengambil semua kategori untuk dropdown pilihan pengaduan.
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pengaduan.customer.create', compact('kategoris'));
    }

    /**
     * Menyimpan pengaduan baru dari customer.
     *
     * user_id diambil dari akun yang sedang login, bukan dari input form,
     * supaya customer tidak bisa membuat pengaduan atas nama user lain.
     */
    public function store(Request $request)
    {
        // Validasi data dari form agar isian sesuai aturan.
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'pengaduan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Upload foto bukti jika user memilih file gambar.
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto', 'public');
            $validated['foto'] = $path;
        }

        // Menambahkan user_id otomatis dari customer yang sedang login.
        $validated['user_id'] = Auth::id();

        Pengaduan::create($validated);

        return redirect()
            ->route('customer.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dibuat.');
    }
}
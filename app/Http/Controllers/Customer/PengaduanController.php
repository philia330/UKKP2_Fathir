<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan milik customer yang sedang login.
     *
     * Customer hanya bisa melihat pengaduan miliknya sendiri,
     * karena query difilter berdasarkan user_id yang sedang login.
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
     */
    public function create()
    {
        // Mengambil semua kategori untuk dropdown
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pengaduan.customer.create', compact('kategoris'));
    }

    /**
     * Menyimpan pengaduan baru dari customer.
     *
     * user_id diambil dari user yang sedang login (bukan dari input form),
     * supaya customer tidak bisa membuat pengaduan atas nama customer lain.
     */
    public function store(Request $request)
    {
        // Validasi data dari form
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'pengaduan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Upload foto jika ada, simpan ke storage/app/public/foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto', 'public');
            $validated['foto'] = $path;
        }

        // Menghubungkan pengaduan dengan user yang sedang login
        $validated['user_id'] = Auth::id();

        Pengaduan::create($validated);

        return redirect()
            ->route('customer.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dibuat.');
    }
}
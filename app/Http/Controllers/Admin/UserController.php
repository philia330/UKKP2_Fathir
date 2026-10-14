<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk mengelola data user pada sistem.
 *
 * Admin memanfaatkan controller ini untuk melihat seluruh user,
 * menambah user baru, mengubah data, dan menghapus akun.
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user dalam sistem.
     *
     * Data yang ditampilkan meliputi admin, petugas, dan customer.
     * Penggunaannya menggunakan pagination agar tampilan tidak terlalu panjang.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk menambah user baru.
     *
     * Form ini dipakai admin saat ingin membuat akun baru
     * dengan role tertentu sesuai kebutuhan sistem.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru ke database.
     *
     * Data yang masuk divalidasi terlebih dahulu agar email unik,
     * nomor telepon sesuai format, dan password sesuai ketentuan.
     * Setelah itu password di-hash agar aman sebelum disimpan.
     */
    public function store(Request $request)
    {
        // Validasi data input agar tidak ada data kosong atau format salah.
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,petugas,customer',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu user khusus.
     *
     * Laravel akan otomatis menangkap model User berdasarkan ID yang dikirim
     * melalui URL, lalu menampilkan datanya di halaman detail.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form edit data user.
     *
     * Data user yang dipilih dimasukkan ke form supaya admin bisa
     * mengubah nama, email, nomor telepon, role, dan password jika diperlukan.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data user yang dipilih.
     *
     * Password bersifat opsional. Jika kolom password diisi,
     * maka password lama diganti dengan password baru yang sudah di-hash.
     * Jika tidak diisi, password lama tetap dipertahankan.
     */
    public function update(Request $request, User $user)
    {
        // Validasi data input, dengan pengecualian email milik user yang sedang diedit.
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,petugas,customer',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_telp = $validated['no_telp'] ?? null;
        $user->role = $validated['role'];

        // Update password hanya jika admin mengisi field password.
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus user dari database.
     *
     * Setelah data user dihapus, user tersebut tidak akan bisa login lagi.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
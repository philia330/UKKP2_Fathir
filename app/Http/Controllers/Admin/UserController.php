<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user (admin, petugas, customer).
     */
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk menambah user baru.
     *
     * Admin dapat membuat user dengan role apa saja.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru ke database.
     *
     * Admin adalah satu-satunya role yang boleh
     * memilih role user secara bebas (admin/petugas/customer).
     */
    public function store(Request $request)
    {
        // Validasi data input
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
     * Menampilkan detail satu user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data user.
     *
     * Password bersifat opsional saat edit.
     * Jika dikosongkan, password lama tetap dipakai.
     */
    public function update(Request $request, User $user)
    {
        // Validasi data input, email unik kecuali milik user ini sendiri
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

        // Update password hanya jika diisi
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
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
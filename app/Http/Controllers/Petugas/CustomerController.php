<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller khusus untuk kebutuhan petugas dalam mengelola customer.
 *
 * Petugas tidak bisa mengakses data admin atau mengubah role user,
 * tetapi masih dapat melihat dan menambahkan customer baru.
 */
class CustomerController extends Controller
{
    /**
     * Menampilkan daftar customer yang terdaftar di sistem.
     *
     * Query hanya mengambil user dengan role customer agar petugas tidak
     * melihat data admin atau petugas lain.
     */
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Menampilkan form pendaftaran customer baru oleh petugas.
     *
     * Proses ini biasanya dipakai ketika customer belum memiliki akun
     * atau petugas ingin membantu pembuatan akun.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Menyimpan customer baru ke database.
     *
     * Semua akun yang dibuat dari sini otomatis diberi role customer,
     * karena petugas tidak diperkenankan membuat akun admin atau petugas.
     */
    public function store(Request $request)
    {
        // Validasi data input agar email unik dan password aman.
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        // Membuat customer baru dengan role customer.
        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('petugas.customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }
}
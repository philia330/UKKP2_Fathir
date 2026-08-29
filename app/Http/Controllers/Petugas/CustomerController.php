<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar customer.
     *
     * Petugas hanya dapat melihat daftar customer,
     * bukan admin atau petugas lain.
     */
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Menampilkan form untuk membuat customer baru.
     *
     * Petugas hanya dapat membuat customer,
     * bukan admin atau petugas.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Menyimpan customer baru.
     *
     * Role otomatis diset menjadi 'customer' karena
     * petugas hanya boleh membuat customer.
     */
    public function store(Request $request)
    {
        // Validasi data input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|regex:/^[0-9]+$/|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        // Membuat customer baru dengan role 'customer'
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
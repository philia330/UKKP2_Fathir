<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Controller pengelola profil untuk semua role.
 *
 * Kita tidak menerima ID dari URL untuk menampilkan profil. Sebaliknya,
 * aplikasi selalu mengambil data dari user yang sedang login. Ini penting
 * agar setiap role hanya bisa melihat profil dirinya sendiri.
 */
class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profile untuk Admin.
     *
     * Data diambil langsung dari user yang sedang login,
     * bukan dari parameter ID, supaya user lain tidak
     * bisa dilihat lewat manipulasi URL.
     */
    public function adminProfile()
    {
        $user = Auth::user();

        return view('profile.admin', compact('user'));
    }

    /**
     * Menampilkan halaman profile untuk Petugas.
     *
     * Petugas melihat data akun mereka sendiri tanpa bisa mengakses profil user lain.
     */
    public function petugasProfile()
    {
        $user = Auth::user();

        return view('profile.petugas', compact('user'));
    }

    /**
     * Menampilkan halaman profile untuk Customer.
     *
     * Customer hanya bisa melihat profile miliknya sendiri,
     * karena data selalu berasal dari user yang sedang login, bukan
     * dari ID yang bisa diketik bebas di URL.
     */
    public function customerProfile()
    {
        $user = Auth::user();

        return view('profile.customer', compact('user'));
    }
}
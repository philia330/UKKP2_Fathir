<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller yang menangani tampilan dashboard berdasarkan role user.
 *
 * Setiap role memiliki dashboard berbeda agar admin, petugas, dan customer
 * hanya melihat informasi yang relevan dengan tugas mereka masing-masing.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Admin.
     *
     * Di sini admin melihat jumlah user, jumlah customer, jumlah petugas,
     * dan total pengaduan yang masuk ke sistem.
     */
    public function adminDashboard()
    {
        // Menghitung jumlah seluruh user berdasarkan role.
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalPetugas = User::where('role', 'petugas')->count();
        $totalPengaduan = Pengaduan::count();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalCustomers',
            'totalPetugas',
            'totalPengaduan'
        ));
    }

    /**
     * Menampilkan dashboard untuk Petugas.
     *
     * Petugas melihat jumlah pengaduan berdasarkan status dan daftar pengaduan
     * terbaru yang sedang ditangani atau menunggu respon.
     */
    public function petugasDashboard()
    {
        // Menghitung pengaduan berdasarkan status.
        $totalPengaduan = Pengaduan::count();
        $menunggu = Pengaduan::where('status', 'menunggu')->count();
        $diproses = Pengaduan::where('status', 'diproses')->count();
        $selesai = Pengaduan::where('status', 'selesai')->count();

        // Mengambil 5 pengaduan terbaru untuk ditampilkan di dashboard.
        $recentPengaduan = Pengaduan::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.petugas', compact(
            'totalPengaduan',
            'menunggu',
            'diproses',
            'selesai',
            'recentPengaduan'
        ));
    }

    /**
     * Menampilkan dashboard untuk Customer.
     *
     * Customer hanya melihat statistik pengaduan miliknya sendiri, bukan data
     * pengaduan semua user. Ini menjaga privasi masing-masing pelanggan.
     */
    public function customerDashboard()
    {
        // Menghitung pengaduan customer yang sedang login.
        $totalPengaduan = Pengaduan::where('user_id', Auth::id())->count();
        $menunggu = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'menunggu')
            ->count();
        $diproses = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'diproses')
            ->count();
        $selesai = Pengaduan::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->count();

        // Mengambil pengaduan terbaru milik customer yang sedang login.
        $recentPengaduan = Pengaduan::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.customer', compact(
            'totalPengaduan',
            'menunggu',
            'diproses',
            'selesai',
            'recentPengaduan'
        ));
    }
}

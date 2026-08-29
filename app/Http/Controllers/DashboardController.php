<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Admin.
     *
     * Menampilkan statistik jumlah user, customer, petugas,
     * dan pengaduan dalam bentuk card.
     */
    public function adminDashboard()
    {
        // Menghitung jumlah user berdasarkan role
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
     * Menampilkan statistik pengaduan dan pengaduan terbaru.
     */
    public function petugasDashboard()
    {
        // Menghitung pengaduan berdasarkan status
        $totalPengaduan = Pengaduan::count();
        $menunggu = Pengaduan::where('status', 'menunggu')->count();
        $diproses = Pengaduan::where('status', 'diproses')->count();
        $selesai = Pengaduan::where('status', 'selesai')->count();

        // Mengambil pengaduan terbaru
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
     * Menampilkan statistik pengaduan milik customer yang sedang login.
     */
    public function customerDashboard()
    {
        // Menghitung pengaduan customer yang sedang login
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

        // Mengambil pengaduan terbaru customer
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

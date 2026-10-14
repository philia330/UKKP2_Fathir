<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk membatasi akses berdasarkan role user.
 *
 * File ini berperan sebagai pengaman akses di level server, bukan hanya sekadar
 * menyembunyikan menu di frontend. Artinya, walaupun user tahu URL, mereka tetap
 * tidak bisa masuk jika role mereka tidak sesuai dengan route yang dituju.
 */
class RoleMiddleware
{
    /**
     * Menangani request masuk sebelum controller dipanggil.
     *
     * Parameter $role datang dari route middleware, misalnya `role:admin`,
     * `role:petugas`, atau `role:customer`.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Jika user belum login, arahkan ke halaman login.
        // Ini memastikan user harus login terlebih dahulu sebelum masuk ke area terproteksi.
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah role user sesuai dengan role yang diizinkan pada route tersebut.
        // Contoh: admin tidak bisa membuka halaman petugas, dan sebaliknya.
        if (Auth::user()->role !== $role) {
            // Jika tidak cocok, stop request dan kirim error 403 Forbidden.
            // Ini mencegah akses tidak sah lewat URL atau manipulasi route.
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Jika semua pengecekan lolos, lanjutkan request ke controller berikutnya.
        return $next($request);
    }
}

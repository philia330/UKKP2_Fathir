<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses login user.
     *
     * Validasi email dan password, kemudian mengarahkan
     * user ke halaman dashboard sesuai role mereka.
     */
    public function login(Request $request)
    {
        // Validasi data login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba login dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // Arahkan user sesuai role
            return $this->redirectBasedOnRole(Auth::user());
        }

        // Login gagal, kembali ke halaman login dengan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman registrasi customer.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Meregistrasi customer baru.
     *
     * Role otomatis设置为 'customer' karena
     * registrasi hanya untuk customer.
     */
    public function register(Request $request)
    {
        // Validasi data registrasi
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        // Membuat user baru dengan role 'customer'
        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
            'role' => 'customer',
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }

    /**
     * Logout user.
     *
     * Menggunakan method POST untuk keamanan.
     */
    public function logout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerasi CSRF token
        $request->session()->regenerateToken();

        // Redirect ke halaman login
        return redirect()
            ->route('login')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Mengarahkan user ke dashboard sesuai role.
     */
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'petugas':
                return redirect()->route('petugas.dashboard');
            case 'customer':
                return redirect()->route('customer.dashboard');
            default:
                return redirect()->route('login');
        }
    }
}

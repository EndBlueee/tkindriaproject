<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ── Tampil Halaman Login ──────────────────────────────────────────

    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        // Ambil tahun ajaran yang aktif
        $tahunAjaran = TahunAjaran::aktif();

        // Ambil data gelombang dari tahun ajaran tersebut, diurutkan berdasarkan tanggal buka
        $gelombangs = $tahunAjaran ? $tahunAjaran->gelombang()->orderBy('tanggal_buka')->get() : collect();

        // Kirim data ke view
        return view('auth.login', [
            'gelombangs' => $gelombangs
        ]);
    }

    // ── Proses Login ──────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string',
            'password' => 'required|string',
        ], [
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Login pakai nomor_hp bukan email
        $credentials = [
            'nomor_hp' => $request->nomor_hp,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Cek apakah akun aktif
            if (! Auth::user()->is_aktif) {
                Auth::logout();
                return back()->withErrors([
                    'nomor_hp' => 'Akun Anda telah dinonaktifkan. Silakan hubungi sekolah.',
                ]);
            }

            return $this->redirectByRole();
        }

        return back()->withErrors([
            'nomor_hp' => 'Nomor HP atau password salah.',
        ])->withInput($request->only('nomor_hp'));
    }

      // ── Tampil Halaman Register ───────────────────────────────────────
 
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
 
        return view('auth.register');
    }
 
    // ── Proses Register ───────────────────────────────────────────────
 
    public function register(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:100',
            'nomor_hp'              => 'required|string|max:15|unique:users,nomor_hp',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'nama.required'                  => 'Nama wajib diisi.',
            'nomor_hp.required'              => 'Nomor HP wajib diisi.',
            'nomor_hp.unique'                => 'Nomor HP sudah terdaftar.',
            'password.required'              => 'Password wajib diisi.',
            'password.min'                   => 'Password minimal 6 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
        ]);
 
        // Buat akun orang tua
        $user = User::create([
            'nama'     => $request->nama,
            'nomor_hp' => $request->nomor_hp,
            'password' => $request->password,
            'role'     => 'orangtua',
            'is_aktif' => true,
        ]);
 
 
        return redirect()->route('orangtua.dashboard.index')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->nama . '!');
    }

    // ── Logout ────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil keluar.');
    }

    // ── Helper: Redirect Sesuai Role ──────────────────────────────────

    private function redirectByRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard.index');
        }

        return redirect()->route('orangtua.dashboard.index');
    }
}
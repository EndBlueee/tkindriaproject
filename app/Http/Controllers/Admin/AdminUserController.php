<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // ── List Semua User ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status aktif
        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        // Cari by nama atau nomor HP
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('nomor_hp', 'like', '%' . $request->cari . '%');
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    // ── Form Tambah User ──────────────────────────────────────────────
    public function create()
    {
        return view('admin.user.create');
    }

    // ── Simpan User Baru ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'nomor_hp' => 'required|string|max:15|unique:users,nomor_hp',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,orangtua',
        ], [
            'nama.required'     => 'Nama wajib diisi.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.unique'   => 'Nomor HP sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
        ]);

        User::create([
            'nama'     => $request->nama,
            'nomor_hp' => $request->nomor_hp,
            'password' => $request->password,
            'role'     => $request->role,
            'is_aktif' => true,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User ' . $request->nama . ' berhasil ditambahkan.');
    }

    // ── Form Edit User ────────────────────────────────────────────────
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    // ── Update User ───────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'nomor_hp' => 'required|string|max:15|unique:users,nomor_hp,' . $user->id,
            'password' => 'nullable|string|min:6',
        ], [
            'nama.required'     => 'Nama wajib diisi.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.unique'   => 'Nomor HP sudah dipakai user lain.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'nama'     => $request->nama,
            'nomor_hp' => $request->nomor_hp,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('admin.user.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    // ── Aktifkan / Nonaktifkan User ───────────────────────────────────
    public function toggleAktif(User $user)
    {
        // Tidak boleh nonaktifkan diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_aktif' => ! $user->is_aktif]);

        $status = $user->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', 'Akun ' . $user->nama . ' berhasil ' . $status . '.');
    }
}
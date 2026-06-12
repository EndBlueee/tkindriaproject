<?php

namespace App\Http\Controllers\OrangTua;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrangTuaProfileController extends Controller
{
    public function index()
    {
        return view('orangtua.profile.index');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'nomor_hp' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'password_lama' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'password_lama.required_with' => 'Password lama wajib diisi jika Anda ingin mengubah password.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->nama = $request->nama;
        $user->nomor_hp = $request->nomor_hp;

        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama salah.']);
            }
            $user->password = $request->password; // Passwords akan otomatis di-hash karena `$casts = ['password' => 'hashed']` ada di model User
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
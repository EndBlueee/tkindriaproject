<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gelombang;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminGelombangController extends Controller
{
     public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();
 
        $gelombang = Gelombang::with('tahunAjaran')
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->orderBy('urutan')
            ->get();
 
        return view('admin.gelombang.index', compact('gelombang', 'tahunAjaran'));
    }

    // ── Form Tambah ───────────────────────────────────────────────────
    public function create()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('created_at')->get();
 
        return view('admin.gelombang.create', compact('tahunAjarans'));
    }

     // ── Simpan Gelombang Baru ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nama'            => 'required|string|max:50',
            'tanggal_buka'    => 'required|date',
            'tanggal_tutup'   => 'required|date|after:tanggal_buka',
            'diskon_persen'   => 'required|integer|min:0|max:100',
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'nama.required'            => 'Nama gelombang wajib diisi.',
            'tanggal_buka.required'    => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.required'   => 'Tanggal tutup wajib diisi.',
            'tanggal_tutup.after'      => 'Tanggal tutup harus setelah tanggal buka.',
            'diskon_persen.required'   => 'Diskon wajib diisi.',
        ]);

        // Hitung urutan otomatis
        $urutan = Gelombang::where('tahun_ajaran_id', $request->tahun_ajaran_id)->count() + 1;

        Gelombang::create([
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'nama'            => $request->nama,
            'tanggal_buka'    => $request->tanggal_buka,
            'tanggal_tutup'   => $request->tanggal_tutup,
            'diskon_persen'   => $request->diskon_persen,
            'urutan'          => $urutan,
        ]);
 
        return redirect()->route('admin.gelombang.index')
            ->with('success', $request->nama . ' berhasil ditambahkan.');
    }

      // ── Form Edit ─────────────────────────────────────────────────────
    public function edit(Gelombang $gelombang)
    {
        $tahunAjarans = TahunAjaran::orderByDesc('created_at')->get();
 
        return view('admin.gelombang.edit', compact('gelombang', 'tahunAjarans'));
    }

     // ── Update Gelombang ──────────────────────────────────────────────
 
    public function update(Request $request, Gelombang $gelombang)
    {
        $request->validate([
            'nama'          => 'required|string|max:50',
            'tanggal_buka'  => 'required|date',
            'tanggal_tutup' => 'required|date|after:tanggal_buka',
            'diskon_persen' => 'required|integer|min:0|max:100',
        ], [
            'nama.required'          => 'Nama gelombang wajib diisi.',
            'tanggal_buka.required'  => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.required' => 'Tanggal tutup wajib diisi.',
            'tanggal_tutup.after'    => 'Tanggal tutup harus setelah tanggal buka.',
            'diskon_persen.required' => 'Diskon wajib diisi.',
        ]);

        $gelombang->update([
            'nama'          => $request->nama,
            'tanggal_buka'  => $request->tanggal_buka,
            'tanggal_tutup' => $request->tanggal_tutup,
            'diskon_persen' => $request->diskon_persen,
        ]);
 
        return redirect()->route('admin.gelombang.index')
            ->with('success', $gelombang->nama . ' berhasil diperbarui.');
    }

    // ── Hapus Gelombang ───────────────────────────────────────────────
    public function destroy(Gelombang $gelombang)
    {
        $nama = $gelombang->nama;
        $gelombang->delete();
 
        return redirect()->route('admin.gelombang.index')
            ->with('success', $nama . ' berhasil dihapus.');
    }
}

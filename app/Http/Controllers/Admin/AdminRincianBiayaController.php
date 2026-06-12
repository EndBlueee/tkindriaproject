<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RincianBiaya;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminRincianBiayaController extends Controller
{
    // ── List Semua Rincian Biaya ──────────────────────────────────────
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();
 
        $rincians = RincianBiaya::where('tahun_ajaran_id', $tahunAjaran?->id)
            ->orderBy('urutan')
            ->get();
 
        return view('admin.rincian-biaya.index', compact('rincians', 'tahunAjaran'));
    }

    // ── Form Tambah ───────────────────────────────────────────────────
    public function create()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('created_at')->get();
 
        return view('admin.rincian-biaya.create', compact('tahunAjarans'));
    }
 
    // ── Simpan Rincian Biaya Baru ─────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nama_komponen'   => 'required|string|max:100',
            'nominal'         => 'required|integer|min:0',
            'keterangan'      => 'nullable|string|max:200',
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'nama_komponen.required'   => 'Nama komponen wajib diisi.',
            'nominal.required'         => 'Nominal wajib diisi.',
        ]);
 
        // Hitung urutan otomatis
        $urutan = RincianBiaya::where('tahun_ajaran_id', $request->tahun_ajaran_id)->count() + 1;
 
        RincianBiaya::create([
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'nama_komponen'   => $request->nama_komponen,
            'nominal'         => $request->nominal,
            'keterangan'      => $request->keterangan,
            'urutan'          => $urutan,
        ]);
 
        return redirect()->route('admin.rincian-biaya.index')
            ->with('success', $request->nama_komponen . ' berhasil ditambahkan.');
    }

     // ── Form Edit ─────────────────────────────────────────────────────
    public function edit(RincianBiaya $rincianBiaya)
    {
        return view('admin.rincian-biaya.edit', compact('rincianBiaya'));
    }
 
    // ── Update Rincian Biaya ──────────────────────────────────────────
    public function update(Request $request, RincianBiaya $rincianBiaya)
    {
        $request->validate([
            'nama_komponen' => 'required|string|max:100',
            'nominal'       => 'required|integer|min:0',
            'keterangan'    => 'nullable|string|max:200',
        ], [
            'nama_komponen.required' => 'Nama komponen wajib diisi.',
            'nominal.required'       => 'Nominal wajib diisi.',
        ]);
 
        $rincianBiaya->update([
            'nama_komponen' => $request->nama_komponen,
            'nominal'       => $request->nominal,
            'keterangan'    => $request->keterangan,
        ]);
 
        return redirect()->route('admin.rincian-biaya.index')
            ->with('success', $rincianBiaya->nama_komponen . ' berhasil diperbarui.');
    }

     // ── Hapus Rincian Biaya ───────────────────────────────────────────
    public function destroy(RincianBiaya $rincianBiaya)
    {
        $nama = $rincianBiaya->nama_komponen;
        $rincianBiaya->delete();
 
        return redirect()->route('admin.rincian-biaya.index')
            ->with('success', $nama . ' berhasil dihapus.');
    }
}

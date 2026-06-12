<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersyaratanDokumen;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminPersyaratanDokumenController extends Controller
{
    // ── List Semua Persyaratan ────────────────────────────────────────
    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();
 
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran?->id)
            ->orderBy('urutan')
            ->get();
 
        return view('admin.persyaratan-dokumen.index', compact('persyaratans', 'tahunAjaran'));
    }

    // ── Form Tambah ───────────────────────────────────────────────────
    public function create()
    {
        $tahunAjarans = TahunAjaran::orderByDesc('created_at')->get();
 
        return view('admin.persyaratan-dokumen.create', compact('tahunAjarans'));
    }

     // ── Simpan Persyaratan Baru ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nama_dokumen'    => 'required|string|max:100',
            'format_file'     => 'required|string|max:50',
            'maks_ukuran_kb'  => 'required|integer|min:100',
            'is_wajib'        => 'required|in:1,0',
            'keterangan'      => 'nullable|string|max:200',
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'nama_dokumen.required'    => 'Nama dokumen wajib diisi.',
            'format_file.required'     => 'Format file wajib diisi.',
            'maks_ukuran_kb.required'  => 'Maksimal ukuran file wajib diisi.',
            'maks_ukuran_kb.min'       => 'Ukuran minimal 100 KB.',
            'is_wajib.required'        => 'Status wajib harus dipilih.',
        ]);
 
        $urutan = PersyaratanDokumen::where('tahun_ajaran_id', $request->tahun_ajaran_id)->count() + 1;
 
        PersyaratanDokumen::create([
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'nama_dokumen'    => $request->nama_dokumen,
            'format_file'     => $request->format_file,
            'maks_ukuran_kb'  => $request->maks_ukuran_kb,
            'is_wajib'        => $request->is_wajib,
            'keterangan'      => $request->keterangan,
            'urutan'          => $urutan,
        ]);
 
        return redirect()->route('admin.persyaratan-dokumen.index')
            ->with('success', $request->nama_dokumen . ' berhasil ditambahkan.');
    }

    // ── Form Edit ─────────────────────────────────────────────────────
    public function edit(PersyaratanDokumen $persyaratanDokumen)
    {
        return view('admin.persyaratan-dokumen.edit', compact('persyaratanDokumen'));
    }

    // ── Update Persyaratan ────────────────────────────────────────────
    public function update(Request $request, PersyaratanDokumen $persyaratanDokumen)
    {
        $request->validate([
            'nama_dokumen'   => 'required|string|max:100',
            'format_file'    => 'required|string|max:50',
            'maks_ukuran_kb' => 'required|integer|min:100',
            'is_wajib'       => 'required|in:1,0',
            'keterangan'     => 'nullable|string|max:200',
        ], [
            'nama_dokumen.required'   => 'Nama dokumen wajib diisi.',
            'format_file.required'    => 'Format file wajib diisi.',
            'maks_ukuran_kb.required' => 'Maksimal ukuran file wajib diisi.',
            'maks_ukuran_kb.min'      => 'Ukuran minimal 100 KB.',
            'is_wajib.required'       => 'Status wajib harus dipilih.',
        ]);
 
        $persyaratanDokumen->update([
            'nama_dokumen'   => $request->nama_dokumen,
            'format_file'    => $request->format_file,
            'maks_ukuran_kb' => $request->maks_ukuran_kb,
            'is_wajib'       => $request->is_wajib,
            'keterangan'     => $request->keterangan,
        ]);
 
        return redirect()->route('admin.persyaratan-dokumen.index')
            ->with('success', $persyaratanDokumen->nama_dokumen . ' berhasil diperbarui.');
    }

     // ── Hapus Persyaratan ─────────────────────────────────────────────
    public function destroy(PersyaratanDokumen $persyaratanDokumen)
    {
        $nama = $persyaratanDokumen->nama_dokumen;
        $persyaratanDokumen->delete();
 
        return redirect()->route('admin.persyaratan-dokumen.index')
            ->with('success', $nama . ' berhasil dihapus.');
    }
}

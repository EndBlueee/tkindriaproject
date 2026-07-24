<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminTahunAjaranController extends Controller
{
    // ── List Semua Tahun Ajaran ───────────────────────────────────────
    public function index()
    {
        $tahunAjarans = TahunAjaran::with(['gelombang', 'rincianBiaya'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    // ── Form Tambah ───────────────────────────────────────────────────
    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    // ── Simpan Tahun Ajaran Baru ──────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:20|unique:tahun_ajaran,nama',
            'maks_termin' => 'required|integer|min:1|max:4',
        ], [
            'nama.required'        => 'Nama tahun ajaran wajib diisi.',
            'nama.unique'          => 'Tahun ajaran ini sudah ada.',
            'maks_termin.required' => 'Maksimal termin wajib diisi.',
        ]);

        TahunAjaran::create([
            'nama'                   => $request->nama,
            'status'                 => 'nonaktif',
            'maks_termin'            => $request->maks_termin,
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $request->nama . ' berhasil ditambahkan. Silakan tambahkan gelombang dan rincian biaya.');
    }

    // ── Form Edit ─────────────────────────────────────────────────────
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    // ── Update Tahun Ajaran ───────────────────────────────────────────
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'maks_termin' => 'required|integer|min:1|max:4',
        ], [
            'maks_termin.required' => 'Maksimal termin wajib diisi.',
        ]);

        $tahunAjaran->update([
            'maks_termin'            => $request->maks_termin,
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    // ── Hapus Tahun Ajaran ────────────────────────────────────────────
    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Tidak boleh hapus yang sedang aktif
        if ($tahunAjaran->status === 'aktif') {
            return back()->with('error', 'Tahun ajaran yang sedang aktif tidak dapat dihapus.');
        }

        if ($tahunAjaran->pendaftaran()->exists()) {
            return back()->with('error', 'Tidak dapat dihapus karena sudah ada data pendaftaran.');
        }

        if ($tahunAjaran->gelombang()->exists()) {
            return back()->with(
                'error',
                'Tidak dapat dihapus karena sudah memiliki data gelombang.'
            );
        }

        $nama = $tahunAjaran->nama;
        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $nama . ' berhasil dihapus.');
    }

    // ── Aktifkan Tahun Ajaran ─────────────────────────────────────────

    public function aktifkan(TahunAjaran $tahunAjaran)
    {
        // Nonaktifkan semua dulu
        TahunAjaran::query()->update(['status' => 'nonaktif']);

        // Aktifkan yang dipilih
        $tahunAjaran->update(['status' => 'aktif']);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran ' . $tahunAjaran->nama . ' berhasil diaktifkan.');
    }
}

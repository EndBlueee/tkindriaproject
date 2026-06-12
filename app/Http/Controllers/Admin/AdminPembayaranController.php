<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPembayaranController extends Controller
{
    // ── List Semua Pembayaran ─────────────────────────────────────────
    // Tampilkan 1 baris per pendaftaran — ambil termin terbaru yang aktif

    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        // Auto-update status kedaluwarsa
        Pembayaran::where('status', 'menunggu')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->update(['status' => 'kedaluwarsa']);

        // Query dari Pendaftaran → ambil 1 termin aktif per pendaftaran
        $query = Pendaftaran::with([
            'calonMurid',
            'gelombang',
            // Ambil 1 termin: yang menunggu dulu, jika semua lunas ambil termin terakhir
            'pembayaran' => function ($q) {
                $q->orderBy('termin_ke', 'asc');
            }
        ])
        ->whereHas('pembayaran')
        ->where('tahun_ajaran_id', $tahunAjaran?->id);

        // Filter status pembayaran
        if ($request->filled('status')) {
            $query->whereHas('pembayaran', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter metode pembayaran
        if ($request->filled('metode')) {
            $query->whereHas('pembayaran', function ($q) use ($request) {
                $q->where('metode', $request->metode);
            });
        }

        // Cari nama anak atau nomor daftar
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_daftar', 'like', '%' . $request->cari . '%')
                  ->orWhereHas('calonMurid', function ($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        $pendaftarans = $query->orderByDesc('updated_at')->paginate(15);

        return view('admin.pembayaran.index', compact('pendaftarans', 'tahunAjaran'));
    }

    // ── Form Tambah Pembayaran Offline ────────────────────────────────

    public function create()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $pendaftarans = Pendaftaran::with(['calonMurid', 'gelombang'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('jalur', 'offline')
            ->where('status', 'menunggu_pembayaran')
            ->whereDoesntHave('pembayaran')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pembayaran.create', compact('pendaftarans', 'tahunAjaran'));
    }

    // ── Simpan Pembayaran Offline ─────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'jumlah_termin'  => 'required|integer|min:1|max:4',
            'metode'         => 'required|in:tunai,transfer',
            'tanggal_bayar'  => 'required|date',
        ], [
            'pendaftaran_id.required' => 'Pendaftar wajib dipilih.',
            'jumlah_termin.required'  => 'Jenis pembayaran wajib dipilih.',
            'metode.required'         => 'Metode pembayaran wajib dipilih.',
            'tanggal_bayar.required'  => 'Tanggal bayar wajib diisi.',
        ]);

        $pendaftaran      = Pendaftaran::with('gelombang')->findOrFail($request->pendaftaran_id);
        $jumlahTermin     = (int) $request->jumlah_termin;
        $totalBiaya       = $pendaftaran->gelombang->totalBiaya();
        $nominalPerTermin = (int) ceil($totalBiaya / $jumlahTermin);

        for ($i = 1; $i <= $jumlahTermin; $i++) {
            $nominal   = ($i === $jumlahTermin)
                ? $totalBiaya - ($nominalPerTermin * ($jumlahTermin - 1))
                : $nominalPerTermin;

            $isTermin1 = ($i === 1);

            Pembayaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'metode'         => $request->metode,
                'termin_ke'      => $i,
                'total_termin'   => $jumlahTermin,
                'nominal'        => $nominal,
                'status'         => $isTermin1 ? 'lunas' : 'menunggu',
                'tanggal_bayar'  => $isTermin1 ? $request->tanggal_bayar : null,
                'dicatat_oleh'   => $isTermin1 ? Auth::id() : null,
                'order_id'       => Pembayaran::generateOrderId($pendaftaran->id, $i),
            ]);
        }

        // Update status pendaftaran
        $pendaftaran->update([
            'status' => $jumlahTermin === 1 ? 'menunggu_nis' : 'cicilan_berjalan'
        ]);

        return redirect()->route('admin.pembayaran.show', $pendaftaran)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    // ── Detail Pembayaran Per Pendaftaran ─────────────────────────────

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'calonMurid.orangTua',
            'gelombang',
            'pembayaran',
        ]);

        return view('admin.pembayaran.show', compact('pendaftaran'));
    }

    // ── Catat Pembayaran Cicilan Berikutnya ───────────────────────────

    public function catat(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'metode'        => 'required|in:tunai,transfer',
            'tanggal_bayar' => 'required|date',
        ], [
            'metode.required'        => 'Metode pembayaran wajib dipilih.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
        ]);

        $pembayaran->update([
            'metode'        => $request->metode,
            'status'        => 'lunas',
            'tanggal_bayar' => $request->tanggal_bayar,
            'dicatat_oleh'  => Auth::id(),
        ]);

        // Update status pendaftaran
        $pendaftaran = $pembayaran->pendaftaran->load('pembayaran');
        $totalTermin = $pendaftaran->pembayaran->first()?->total_termin ?? 1;
        $terminLunas = $pendaftaran->pembayaran->where('status', 'lunas')->count();

        if ($terminLunas >= $totalTermin) {
            $pendaftaran->update(['status' => 'menunggu_nis']);
        } elseif ($terminLunas > 0) {
            $pendaftaran->update(['status' => 'cicilan_berjalan']);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
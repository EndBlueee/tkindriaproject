<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrangTuaPembayaranController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    // ── Index — Invoice + Pilih Jenis + Pilih Metode ──────────────────

    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        // Auto-update pembayaran yang sudah kedaluwarsa di background
        Pembayaran::where('status', 'menunggu')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->update(['status' => 'kedaluwarsa']);

        // Jika user klik "Ganti Metode", set session untuk mengingat state ini
        if ($request->has('ganti')) {
            $request->session()->put('sedang_ganti_metode', true);
        }

        // Cek apakah ada pendaftaran
        $pendaftaran = null;
        if ($tahunAjaran) {
            $pendaftaran = Pendaftaran::with(['calonMurid', 'gelombang', 'pembayaran'])
                ->where('user_id', Auth::id())
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->first();
        }

        // Kondisi 1 — Belum ada pendaftaran
        if (! $pendaftaran) {
            return view('orangtua.pembayaran.index', [
                'kondisi'     => 'belum_daftar',
                'tahunAjaran' => $tahunAjaran,
                'pendaftaran' => null,
                'gelombang'   => null,
                'rincianBiaya'=> collect(),
                'daftarMetode'=> [],
                'sudahAdaTagihan' => false,
                'semuaLunas'  => false,
            ]);
        }

        // Kondisi 2 — Belum status menunggu pembayaran
        if (! in_array($pendaftaran->status, [
            'menunggu_pembayaran', 'cicilan_berjalan', 'menunggu_nis', 'aktif'
        ])) {
            return view('orangtua.pembayaran.index', [
                'kondisi'     => 'belum_bayar',
                'tahunAjaran' => $tahunAjaran,
                'pendaftaran' => $pendaftaran,
                'gelombang'   => null,
                'rincianBiaya'=> collect(),
                'daftarMetode'=> [],
                'sudahAdaTagihan' => false,
                'semuaLunas'  => false,
            ]);
        }

        $gelombang       = $pendaftaran->gelombang;
        $rincianBiaya    = $tahunAjaran->rincianBiaya;
        $daftarMetode    = MidtransService::daftarMetode();
        $sudahAdaTagihan = $pendaftaran->pembayaran->count() > 0;

        // Kondisi 3 — Semua termin sudah lunas
        $semuaLunas = $sudahAdaTagihan &&
            $pendaftaran->pembayaran->where('status', '!=', 'lunas')->count() === 0;

        // Jika semua sudah lunas, langsung arahkan ke halaman selesai (tidak tampil di index)
        if ($semuaLunas) {
            $pembayaranTerakhir = $pendaftaran->pembayaran->where('status', 'lunas')->sortByDesc('termin_ke')->first();
            return redirect()->route('orangtua.pembayaran.selesai', [
                'pembayaran' => $pembayaranTerakhir,
                'status'     => 'success'
            ]);
        }

        // Jika ada tagihan yang sedang menunggu → redirect ke halaman bayar
        // Kecuali jika orang tua sedang dalam proses ganti metode (ada session)
        if ($sudahAdaTagihan && ! $semuaLunas && ! $request->session()->get('sedang_ganti_metode')) {
            $terminBelumBayar = $pendaftaran->pembayaran
                ->whereIn('status', ['menunggu', 'kedaluwarsa'])
                ->sortBy('termin_ke')
                ->first();

            // Redirect jika kode bayar ada (termasuk yang sudah kedaluwarsa, agar ortu melihat notif merah)
            if ($terminBelumBayar && $terminBelumBayar->kode_bayar) {
                return redirect()->route('orangtua.pembayaran.bayar', $terminBelumBayar);
            }
        }

        return view('orangtua.pembayaran.index', compact(
            'pendaftaran',
            'tahunAjaran',
            'gelombang',
            'rincianBiaya',
            'daftarMetode',
            'sudahAdaTagihan',
            'semuaLunas'
        ) + ['kondisi' => 'aktif']);
    }

    // ── Proses Pilih → Buat Tagihan → Buat Transaksi Midtrans ────────

    public function pilih(Request $request)
    {
        $request->validate([
            'jumlah_termin' => 'required_without:sudah_ada|integer|min:1|max:4',
            'metode'        => 'required|in:bca_va,bni_va,bri_va,mandiri_va,permata_va,alfamart,indomaret',
        ], [
            'jumlah_termin.required_without' => 'Jenis pembayaran wajib dipilih.',
            'metode.required'                => 'Metode pembayaran wajib dipilih.',
        ]);

        $tahunAjaran = TahunAjaran::aktif();

        $pendaftaran = Pendaftaran::with(['gelombang', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->firstOrFail();

        // Jika sudah ada tagihan → ganti metode saja
        if ($pendaftaran->pembayaran->count() > 0) {
            $terminBelumBayar = $pendaftaran->pembayaran
                ->whereIn('status', ['menunggu', 'kedaluwarsa'])
                ->sortBy('termin_ke')
                ->first();

            if ($terminBelumBayar) {
                return $this->prosesTransaksi($terminBelumBayar, $request->metode);
            }

            return redirect()->route('orangtua.pembayaran.index');
        }

        // Buat semua tagihan
        $jumlahTermin     = (int) $request->jumlah_termin;
        $totalBiaya       = $pendaftaran->gelombang->totalBiaya();
        $nominalPerTermin = (int) ceil($totalBiaya / $jumlahTermin);

        for ($i = 1; $i <= $jumlahTermin; $i++) {
            $nominal = ($i === $jumlahTermin)
                ? $totalBiaya - ($nominalPerTermin * ($jumlahTermin - 1))
                : $nominalPerTermin;

            Pembayaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'metode'         => $request->metode,
                'termin_ke'      => $i,
                'total_termin'   => $jumlahTermin,
                'nominal'        => $nominal,
                'status'         => 'menunggu',
                'order_id'       => Pembayaran::generateOrderId($pendaftaran->id, $i),
            ]);
        }

        // Ambil termin 1
        $terminPertama = Pembayaran::where('pendaftaran_id', $pendaftaran->id)
            ->where('termin_ke', 1)
            ->first();

        return $this->prosesTransaksi($terminPertama, $request->metode);
    }

    // ── Halaman Bayar — Tampil Kode Pembayaran ────────────────────────

    public function bayar(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pendaftaran.calonMurid.orangTua', 'pendaftaran.gelombang']);

        if ($pembayaran->pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        // Auto-update jika kedaluwarsa
        if ($pembayaran->status === 'menunggu' && $pembayaran->expired_at && now()->isAfter($pembayaran->expired_at)) {
            $pembayaran->update(['status' => 'kedaluwarsa']);
        }

        return view('orangtua.pembayaran.bayar', compact('pembayaran'));
    }

    // ── Halaman Selesai ───────────────────────────────────────────────

    public function selesai(Pembayaran $pembayaran, Request $request)
    {
        $pembayaran->load(['pendaftaran.calonMurid', 'pendaftaran.gelombang', 'pendaftaran.pembayaran']);

        if ($pembayaran->pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        $status = $request->query('status', 'pending');

        return view('orangtua.pembayaran.selesai', compact('pembayaran', 'status'));
    }

    // ── Status JSON untuk polling otomatis ───────────────────────────

    public function statusJson(Pembayaran $pembayaran)
    {
        if ($pembayaran->pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pembayaran->status === 'menunggu') {
            // Cek lokal dulu agar update langsung realtime tanpa tunggu webhook
            if ($pembayaran->expired_at && now()->isAfter($pembayaran->expired_at)) {
                $pembayaran->update(['status' => 'kedaluwarsa']);
            } else {
                try {
                    \Midtrans\Config::$serverKey    = config('midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('midtrans.is_production');

                    $status = (object) \Midtrans\Transaction::status($pembayaran->order_id);

                    if (in_array($status->transaction_status, ['capture', 'settlement'])) {
                        $this->midtrans->tandaiLunas($pembayaran, $status->transaction_id);
                        $pembayaran->refresh();
                    } elseif (in_array($status->transaction_status, ['expire', 'cancel', 'deny'])) {
                        $pembayaran->update(['status' => 'kedaluwarsa']);
                        $pembayaran->refresh();
                    }
                } catch (\Exception) {
                    // abaikan, kembalikan status dari DB
                }
            }
        }

        return response()->json([
            'status'      => $pembayaran->status,
            'selesai_url' => route('orangtua.pembayaran.selesai', [
                'pembayaran' => $pembayaran->id,
                'status'     => 'success',
            ]),
        ]);
    }

    // ── Cek Status ke Midtrans ────────────────────────────────────────

    public function cekStatus(Pembayaran $pembayaran)
    {
        if ($pembayaran->pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        // Cek lokal
        if ($pembayaran->status === 'menunggu' && $pembayaran->expired_at && now()->isAfter($pembayaran->expired_at)) {
            $pembayaran->update(['status' => 'kedaluwarsa']);
            return back()->with('error', 'Pembayaran telah kedaluwarsa. Silakan buat transaksi baru.');
        }

        try {
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            $status = (object) \Midtrans\Transaction::status($pembayaran->order_id);

            if (in_array($status->transaction_status, ['capture', 'settlement'])) {
                $this->midtrans->tandaiLunas($pembayaran, $status->transaction_id);
                return redirect()->route('orangtua.pembayaran.selesai', [
                    'pembayaran' => $pembayaran->id,
                    'status'     => 'success',
                ]);
            } elseif (in_array($status->transaction_status, ['expire', 'cancel', 'deny'])) {
                $pembayaran->update(['status' => 'kedaluwarsa']);
                return back()->with('error', 'Pembayaran telah kedaluwarsa. Silakan buat transaksi baru.');
            }

            return back()->with('info', 'Pembayaran belum diterima. Silakan selesaikan pembayaran terlebih dahulu.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal cek status: ' . $e->getMessage());
        }
    }

    // ── Private: Proses Transaksi ke Midtrans ─────────────────────────

    private function prosesTransaksi(Pembayaran $pembayaran, string $metode): \Illuminate\Http\RedirectResponse
    {
        try {
            // Hapus session 'sedang_ganti_metode' karena transaksi baru akan dibuat
            request()->session()->forget('sedang_ganti_metode');

            // Generate order_id baru agar tidak konflik di Midtrans
            // Order_id lama tidak bisa dipakai ulang
            $pembayaran->update([
                'order_id' => Pembayaran::generateOrderId($pembayaran->pendaftaran_id, $pembayaran->termin_ke),
                'status'   => 'menunggu',
            ]);

            $this->midtrans->buatTransaksi($pembayaran, $metode);

            return redirect()->route('orangtua.pembayaran.bayar', $pembayaran)
                ->with('success', 'Transaksi berhasil dibuat. Silakan selesaikan pembayaran.');
        } catch (\Exception $e) {
            return redirect()->route('orangtua.pembayaran.index')
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransService
{
    const METODE_MAP = [
        'bca_va'     => 'BCA Virtual Account',
        'bni_va'     => 'BNI Virtual Account',
        'bri_va'     => 'BRI Virtual Account',
        'mandiri_va' => 'Mandiri Virtual Account',
        'permata_va' => 'Permata Virtual Account',
        'alfamart'   => 'Alfamart',
        'indomaret'  => 'Indomaret',
    ];

    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    // ── Buat Transaksi Core API ───────────────────────────────────────

    public function buatTransaksi(Pembayaran $pembayaran, string $metode): array
    {
        $pendaftaran = $pembayaran->pendaftaran->load(['calonMurid.orangTua']);
        $ortu        = $pendaftaran->calonMurid->orangTua;

        $params = [
            'transaction_details' => [
                'order_id'     => $pembayaran->order_id,
                'gross_amount' => $pembayaran->nominal,
            ],
            'customer_details' => [
                'first_name' => $ortu->nama_penanggung_jawab ?? 'Orang Tua',
                'phone'      => $ortu->nomor_hp,
            ],
            'item_details' => [
                [
                    'id'       => 'TERMIN-' . $pembayaran->termin_ke,
                    'price'    => $pembayaran->nominal,
                    'quantity' => 1,
                    'name'     => $this->namaItem($pembayaran, $pendaftaran->calonMurid->nama_lengkap),
                ],
            ],
            'custom_expiry' => [
                'expiry_duration' => 2,
                'unit'            => 'minute',
            ],
        ];

        // Tambah parameter spesifik per metode
        $params = array_merge($params, $this->paramMetode($metode));

        // Kirim ke Midtrans Core API
        $response = CoreApi::charge($params);

        // Ambil kode bayar dari response
        $kodeBayar = $this->ambilKodeBayar($response, $metode);

        // Simpan ke database
        $pembayaran->update([
            'metode'     => $metode,
            'kode_bayar' => $kodeBayar,
            'trx_id'     => $response->transaction_id ?? null,
            'expired_at' => isset($response->expiry_time)
                ? \Carbon\Carbon::parse($response->expiry_time)
                : now()->addMinutes(2),
        ]);

        return [
            'kode_bayar'  => $kodeBayar,
            'metode'      => $metode,
            'expired_at'  => $pembayaran->expired_at,
            'response'    => $response,
        ];
    }

    // ── Parameter Per Metode ──────────────────────────────────────────

    private function paramMetode(string $metode): array
    {
        return match ($metode) {
            'bca_va' => [
                'payment_type'  => 'bank_transfer',
                'bank_transfer' => ['bank' => 'bca'],
            ],
            'bni_va' => [
                'payment_type'  => 'bank_transfer',
                'bank_transfer' => ['bank' => 'bni'],
            ],
            'bri_va' => [
                'payment_type'  => 'bank_transfer',
                'bank_transfer' => ['bank' => 'bri'],
            ],
            'mandiri_va' => [
                'payment_type' => 'echannel',
                'echannel'     => [
                    'bill_info1' => 'PMB TK Indria',
                    'bill_info2' => 'Pembayaran Pendaftaran',
                ],
            ],
            'permata_va' => [
                'payment_type'  => 'bank_transfer',
                'bank_transfer' => ['bank' => 'permata'],
            ],
            'alfamart' => [
                'payment_type' => 'cstore',
                'cstore'       => ['store' => 'alfamart'],
            ],
            'indomaret' => [
                'payment_type' => 'cstore',
                'cstore'       => ['store' => 'indomaret'],
            ],
            default => [],
        };
    }

    // ── Ambil Kode Bayar dari Response ────────────────────────────────

    private function ambilKodeBayar(object $response, string $metode): string
    {
        return match ($metode) {
            'bca_va', 'bni_va', 'bri_va', 'permata_va' =>
                $response->va_numbers[0]->va_number ?? '',
            'mandiri_va' =>
                $response->bill_key ?? '',
            'alfamart', 'indomaret' =>
                $response->payment_code ?? '',
            default => '',
        };
    }

    // ── Handle Notifikasi Webhook ─────────────────────────────────────

    public function handleNotifikasi(array $notif): void
    {
        $orderId           = $notif['order_id'];
        $statusCode        = $notif['status_code'];
        $grossAmount       = $notif['gross_amount'];
        $transactionStatus = $notif['transaction_status'];
        $fraudStatus       = $notif['fraud_status'] ?? null;

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();
        if (! $pembayaran) return;

        // Verifikasi signature
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
        if ($signatureKey !== ($notif['signature_key'] ?? '')) return;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($fraudStatus === null || $fraudStatus === 'accept') {
                $this->tandaiLunas($pembayaran, $notif['transaction_id']);
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
            $pembayaran->update(['status' => 'kedaluwarsa']);
        }
    }

    // ── Tandai Lunas ─────────────────────────────────────────────────

    public function tandaiLunas(Pembayaran $pembayaran, string $trxId): void
    {
        $pembayaran->update([
            'status'        => 'lunas',
            'trx_id'        => $trxId,
            'tanggal_bayar' => now(),
        ]);

        $this->updateStatusPendaftaran($pembayaran->pendaftaran);
    }

    // ── Update Status Pendaftaran ─────────────────────────────────────

    public function updateStatusPendaftaran(Pendaftaran $pendaftaran): void
    {
        $pendaftaran->load('pembayaran');

        $totalTermin = $pendaftaran->pembayaran->first()?->total_termin ?? 1;
        $terminLunas = $pendaftaran->pembayaran->where('status', 'lunas')->count();

        if ($terminLunas >= $totalTermin) {
            $pendaftaran->update(['status' => 'menunggu_nis']);
        } elseif ($terminLunas > 0) {
            $pendaftaran->update(['status' => 'cicilan_berjalan']);
        }
    }

    // ── Helper ────────────────────────────────────────────────────────

    private function namaItem(Pembayaran $pembayaran, string $namaAnak): string
    {
        if ($pembayaran->total_termin === 1) {
            return 'Pembayaran Lunas PMB - ' . $namaAnak;
        }
        return 'Cicilan ' . $pembayaran->termin_ke . '/' . $pembayaran->total_termin . ' PMB - ' . $namaAnak;
    }

    public static function daftarMetode(): array
    {
        return self::METODE_MAP;
    }
}
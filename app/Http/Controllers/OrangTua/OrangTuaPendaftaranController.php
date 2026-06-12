<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\CalonMurid;
use App\Models\Dokumen;
use App\Models\OrangTua;
use App\Models\Pendaftaran;
use App\Models\PersyaratanDokumen;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrangTuaPendaftaranController extends Controller
{
    // ── Helper: Cek Status Tiap Tahap ────────────────────────────────

    private function cekStatus(): array
    {
        $user        = Auth::user();
        $tahunAjaran = TahunAjaran::aktif();
        $calonMurid  = CalonMurid::where('user_id', $user->id)->first();
        $orangTua    = $calonMurid
            ? OrangTua::where('calon_murid_id', $calonMurid->id)->first()
            : null;

        // Cek dokumen sementara (disimpan di storage/temp)
        $dokumenTemp = [];
        $totalWajib  = 0;
        $totalUpload = 0;

        if ($tahunAjaran) {
            $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran->id)
                ->terurut()->get();

            $totalWajib = $persyaratans->where('is_wajib', true)->count();

            // Cek file temp di storage
            $filesTemp = Storage::disk('public')->files('temp/' . $user->id);

            foreach ($persyaratans as $p) {
                $adaFile = false;
                foreach ($filesTemp as $file) {
                    if (str_contains($file, 'dokumen_' . $p->id . '.')) {
                        $adaFile = true;
                        $dokumenTemp[$p->id] = $file;
                        break;
                    }
                }
                if ($adaFile && $p->is_wajib) {
                    $totalUpload++;
                }
            }
        }

        $dokumenLengkap = $totalWajib > 0 && $totalUpload >= $totalWajib;

        return [
            'murid'          => $calonMurid !== null,
            'ortu'           => $orangTua !== null,
            'dokumen'        => $dokumenLengkap,
            'calon_murid'    => $calonMurid,
            'orang_tua'      => $orangTua,
            'dokumen_temp'   => $dokumenTemp,
            'total_wajib'    => $totalWajib,
            'total_upload'   => $totalUpload,
        ];
    }

    // ── Index — Halaman Utama Formulir ────────────────────────────────

    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();
        $buka        = $tahunAjaran?->isPendaftaranBuka() ?? false;

        // Cek apakah sudah ada pendaftaran yang sudah disubmit
        if ($tahunAjaran) {
            $pendaftaran = Pendaftaran::where('user_id', Auth::id())
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->first();

            if ($pendaftaran) {
                return redirect()->route('orangtua.pendaftaran.detail');
            }
        }

        $status = $buka ? $this->cekStatus() : [];

        return view('orangtua.pendaftaran.index', compact('tahunAjaran', 'buka', 'status'));
    }

    // ── Tahap 1 — Data Calon Murid ────────────────────────────────────

    public function murid()
    {
        $this->cekSudahSubmit();

        $calonMurid = CalonMurid::where('user_id', Auth::id())->first();
        $tahap      = 'murid';

        return view('orangtua.pendaftaran.form', compact('calonMurid', 'tahap'));
    }

    public function simpanMurid(Request $request)
    {
        $this->cekSudahSubmit();

        $existingId = CalonMurid::where('user_id', Auth::id())->first()?->id ?? 0;

        $request->validate([
            'nama_lengkap'   => 'required|string|max:100',
            'nama_panggilan' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:50',
            'tanggal_lahir'  => 'required|date|before:' . now()->subYears(4)->format('Y-m-d'),
            'agama'          => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'anak_ke'        => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required'  => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'   => 'Calon murid harus berusia minimal 4 tahun.',
            'agama.required'         => 'Agama wajib dipilih.',
        ]);

        CalonMurid::updateOrCreate(
            ['user_id' => Auth::id()],
            array_merge(
                $request->only([
                    'nama_lengkap', 'nama_panggilan', 'jenis_kelamin',
                    'tempat_lahir', 'tanggal_lahir', 'agama',
                    'anak_ke', 'jumlah_saudara',
                ]),
                ['user_id' => Auth::id()]
            )
        );

        return redirect()->route('orangtua.pendaftaran.index')
            ->with('success', 'Data calon murid berhasil disimpan.');
    }

    // ── Tahap 2 — Data Orang Tua ──────────────────────────────────────

    public function orangTua()
    {
        $this->cekSudahSubmit();

        $calonMurid = CalonMurid::where('user_id', Auth::id())->first();

        if (! $calonMurid) {
            return redirect()->route('orangtua.pendaftaran.murid')
                ->with('error', 'Lengkapi data calon murid terlebih dahulu.');
        }

        $orangTua = OrangTua::where('calon_murid_id', $calonMurid->id)->first();
        $tahap    = 'ortu';

        return view('orangtua.pendaftaran.form', compact('calonMurid', 'orangTua', 'tahap'));
    }

    public function simpanOrangTua(Request $request)
    {
        $this->cekSudahSubmit();

        $calonMurid = CalonMurid::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nama_ayah'      => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'nama_ibu'       => 'nullable|string|max:100',
            'pekerjaan_ibu'  => 'nullable|string|max:50',
            'nomor_hp'       => 'required|string|max:15',
            'nomor_saudara'  => 'nullable|string|max:15',
            'alamat'         => 'required|string',
        ], [
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'alamat.required'   => 'Alamat wajib diisi.',
        ]);

        // Minimal salah satu nama harus diisi
        if (! $request->nama_ayah && ! $request->nama_ibu) {
            return back()->withErrors([
                'nama_ayah' => 'Minimal salah satu dari nama ayah atau ibu harus diisi.',
            ])->withInput();
        }

        OrangTua::updateOrCreate(
            ['calon_murid_id' => $calonMurid->id],
            array_merge(
                $request->only([
                    'nama_ayah', 'pekerjaan_ayah',
                    'nama_ibu', 'pekerjaan_ibu',
                    'nomor_hp', 'nomor_saudara',
                    'alamat',
                ]),
                ['calon_murid_id' => $calonMurid->id]
            )
        );

        return redirect()->route('orangtua.pendaftaran.index')
            ->with('success', 'Data orang tua berhasil disimpan.');
    }

    // ── Tahap 3 — Upload Dokumen ──────────────────────────────────────

    public function dokumen()
    {
        $this->cekSudahSubmit();

        $calonMurid = CalonMurid::where('user_id', Auth::id())->first();

        if (! $calonMurid) {
            return redirect()->route('orangtua.pendaftaran.murid')
                ->with('error', 'Lengkapi data calon murid terlebih dahulu.');
        }

        $tahunAjaran  = TahunAjaran::aktif();
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran?->id)
            ->terurut()->get();

        // Cek file temp yang sudah diupload
        $dokumenTemp = [];
        foreach ($persyaratans as $p) {
            $files = Storage::disk('public')->files('temp/' . Auth::id());
            foreach ($files as $file) {
                if (str_contains($file, 'dokumen_' . $p->id)) {
                    $dokumenTemp[$p->id] = $file;
                }
            }
        }

        $tahap = 'dokumen';

        return view('orangtua.pendaftaran.form', compact(
            'calonMurid', 'persyaratans', 'dokumenTemp', 'tahunAjaran', 'tahap'
        ));
    }

    public function simpanDokumen(Request $request)
    {
        $this->cekSudahSubmit();

        $tahunAjaran  = TahunAjaran::aktif();
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran?->id)
            ->terurut()->get();

        // Validasi dokumen wajib
        $rules    = [];
        $messages = [];

        foreach ($persyaratans as $p) {
            $key = 'dokumen_' . $p->id;

            // Cek apakah sudah ada file temp
            $sudahAda = false;
            $files    = Storage::disk('public')->files('temp/' . Auth::id());
            foreach ($files as $file) {
                if (str_contains($file, 'dokumen_' . $p->id)) {
                    $sudahAda = true;
                    break;
                }
            }

            if ($p->is_wajib && ! $sudahAda) {
                $rules[$key]                  = ['required', 'file', 'mimes:' . $p->format_file, 'max:' . $p->maks_ukuran_kb];
                $messages[$key . '.required'] = $p->nama_dokumen . ' wajib diupload.';
                $messages[$key . '.mimes']    = $p->nama_dokumen . ' harus format ' . $p->format_file_tampil . '.';
                $messages[$key . '.max']      = $p->nama_dokumen . ' maksimal ' . $p->maks_ukuran_format . '.';
            } elseif ($request->hasFile($key)) {
                $rules[$key] = ['nullable', 'file', 'mimes:' . $p->format_file, 'max:' . $p->maks_ukuran_kb];
            }
        }

        $request->validate($rules, $messages);

        // Simpan file ke folder temp
        foreach ($persyaratans as $p) {
            $key = 'dokumen_' . $p->id;

            if ($request->hasFile($key)) {
                $file     = $request->file($key);
                $tempPath = 'temp/' . Auth::id() . '/dokumen_' . $p->id . '.' . $file->getClientOriginalExtension();

                // Hapus file temp lama jika ada
                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->delete($tempPath);
                }

                // Simpan file baru ke temp
                Storage::disk('public')->putFileAs(
                    'temp/' . Auth::id(),
                    $file,
                    'dokumen_' . $p->id . '.' . $file->getClientOriginalExtension()
                );
            }
        }

        return redirect()->route('orangtua.pendaftaran.index')
            ->with('success', 'Dokumen berhasil disimpan.');
    }

    // ── Submit Final ──────────────────────────────────────────────────

    public function submit(Request $request)
    {
        $status = $this->cekStatus();

        if (! $status['murid'] || ! $status['ortu'] || ! $status['dokumen']) {
            return redirect()->route('orangtua.pendaftaran.index')
                ->with('error', 'Lengkapi semua tahap sebelum submit.');
        }

        $tahunAjaran  = TahunAjaran::aktif();
        $gelombang    = $tahunAjaran->gelombangAktif();
        $calonMurid   = $status['calon_murid'];
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran->id)
            ->terurut()->get();

        DB::transaction(function () use ($calonMurid, $tahunAjaran, $gelombang, $persyaratans, $status) {

            // Buat pendaftaran
            $pendaftaran = Pendaftaran::create([
                'nomor_daftar'    => Pendaftaran::generateNomorDaftar(),
                'user_id'         => Auth::id(),
                'calon_murid_id'  => $calonMurid->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id'    => $gelombang?->id,
                'jalur'           => 'online',
                'status'          => 'menunggu_verifikasi',
                'tgl_submit'      => now(),
            ]);

            // Pindah dokumen dari temp ke permanen
            foreach ($persyaratans as $p) {
                $files = Storage::disk('public')->files('temp/' . Auth::id());
                foreach ($files as $file) {
                    if (str_contains($file, 'dokumen_' . $p->id)) {
                        $ext         = pathinfo($file, PATHINFO_EXTENSION);
                        $namaFile    = $p->nama_dokumen . '.' . $ext;
                        $pathPermanen = 'dokumen/' . $pendaftaran->nomor_daftar . '/' . $namaFile;

                        Storage::disk('public')->move($file, $pathPermanen);

                        Dokumen::create([
                            'pendaftaran_id'    => $pendaftaran->id,
                            'persyaratan_id'    => $p->id,
                            'nama_file'         => $namaFile,
                            'path_file'         => $pathPermanen,
                            'tipe_file'         => $ext,
                            'ukuran_file'       => round(Storage::disk('public')->size($pathPermanen) / 1024),
                            'status_verifikasi' => 'menunggu',
                            'uploaded_at'       => now(),
                        ]);

                        break;
                    }
                }
            }

            // Hapus folder temp
            Storage::disk('public')->deleteDirectory('temp/' . Auth::id());
        });

        return redirect()->route('orangtua.pendaftaran.detail')
            ->with('success', 'Pendaftaran berhasil disubmit! Silakan tunggu verifikasi dari admin.');
    }

    // ── Detail Pendaftaran (Read Only) ────────────────────────────────

    public function detail()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $pendaftaran = Pendaftaran::with([
            'calonMurid.orangTua',
            'gelombang',
            'dokumen.persyaratan',
        ])
        ->where('user_id', Auth::id())
        ->where('tahun_ajaran_id', $tahunAjaran?->id)
        ->first();

        if (! $pendaftaran) {
            return redirect()->route('orangtua.pendaftaran.index');
        }

        return view('orangtua.pendaftaran.detail', compact('pendaftaran'));
    }

    // ── Revisi Dokumen ────────────────────────────────────────────────

    public function revisi()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $pendaftaran = Pendaftaran::where('user_id', Auth::id())
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('status', 'perlu_revisi')
            ->first();

        if (! $pendaftaran) {
            return redirect()->route('orangtua.pendaftaran.detail');
        }

        $persyaratans   = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran->id)
            ->terurut()->get();
        $dokumenExisting = $pendaftaran->dokumen->keyBy('persyaratan_id');

        return view('orangtua.pendaftaran.revisi', compact(
            'pendaftaran', 'persyaratans', 'dokumenExisting'
        ));
    }

    public function simpanRevisi(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $pendaftaran = Pendaftaran::where('user_id', Auth::id())
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('status', 'perlu_revisi')
            ->firstOrFail();

        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran->id)
            ->terurut()->get();

        // Upload dokumen baru jika ada
        foreach ($persyaratans as $p) {
            $key = 'dokumen_' . $p->id;

            if ($request->hasFile($key)) {
                $file = $request->file($key);

                // Hapus dokumen lama
                $old = Dokumen::where('pendaftaran_id', $pendaftaran->id)
                    ->where('persyaratan_id', $p->id)->first();

                if ($old) {
                    Storage::disk('public')->delete($old->path_file);
                    $old->delete();
                }

                $ext         = $file->getClientOriginalExtension();
                $namaFile    = $p->nama_dokumen . '.' . $ext;
                $path        = 'dokumen/' . $pendaftaran->nomor_daftar . '/' . $namaFile;

                Storage::disk('public')->putFileAs(
                    'dokumen/' . $pendaftaran->nomor_daftar,
                    $file,
                    $namaFile
                );

                Dokumen::create([
                    'pendaftaran_id'    => $pendaftaran->id,
                    'persyaratan_id'    => $p->id,
                    'nama_file'         => $namaFile,
                    'path_file'         => $path,
                    'tipe_file'         => $ext,
                    'ukuran_file'       => round($file->getSize() / 1024),
                    'status_verifikasi' => 'menunggu',
                    'uploaded_at'       => now(),
                ]);
            }
        }

        // Update status kembali ke menunggu_verifikasi
        $pendaftaran->update(['status' => 'menunggu_verifikasi', 'catatan_admin' => null]);

        return redirect()->route('orangtua.pendaftaran.detail')
            ->with('success', 'Dokumen berhasil diupload. Menunggu verifikasi ulang dari admin.');
    }

    // ── Private Helper ────────────────────────────────────────────────

    // Cek apakah sudah submit — jika ya redirect ke detail
    private function cekSudahSubmit()
    {
        $tahunAjaran = TahunAjaran::aktif();
        if (! $tahunAjaran) return;

        $pendaftaran = Pendaftaran::where('user_id', Auth::id())
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();

        if ($pendaftaran) {
            redirect()->route('orangtua.pendaftaran.detail')->send();
            exit;
        }
    }
}
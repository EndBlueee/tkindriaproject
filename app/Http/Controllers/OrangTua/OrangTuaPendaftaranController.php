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
use Illuminate\Support\Str;

class OrangTuaPendaftaranController extends Controller
{
    // ── Helper: Ambil Calon Murid Aktif ───────────────────────────────

    private function calonMuridAktif(): ?CalonMurid
    {
        $userId = Auth::id();

        /*
         * Jika user sedang membuat anak baru,
         * jangan ambil anak yang sudah ada.
         *
         * Formulir harus dalam kondisi kosong.
         */
        if (session('adding_new_child', false)) {
            return null;
        }

        /*
         * Jika sudah ada anak aktif di session,
         * gunakan anak tersebut.
         */
        if (session()->has('active_calon_murid_id')) {

            $activeId = session('active_calon_murid_id');

            if ($activeId) {

                $calonMurid = CalonMurid::where('id', $activeId)
                    ->where('user_id', $userId)
                    ->first();

                if ($calonMurid) {
                    return $calonMurid;
                }
            }

            session()->forget('active_calon_murid_id');
        }

        /*
         * Jika belum ada anak aktif dan user tidak sedang
         * menambah anak baru, ambil anak terakhir sebagai default.
         */
        $calonMurid = CalonMurid::where('user_id', $userId)
            ->latest('id')
            ->first();

        if ($calonMurid) {

            session([
                'active_calon_murid_id' => $calonMurid->id
            ]);
        }

        return $calonMurid;
    }


    // ── Helper: Folder Temp Dokumen ───────────────────────────────────

    private function tempDir(): ?string
    {
        $calonMuridId = session('active_calon_murid_id');

        if (! $calonMuridId) {
            return null;
        }

        return 'temp/' . Auth::id() . '/' . $calonMuridId;
    }


    // ── Helper: Nama File Dokumen Aman ────────────────────────────────

    private function namaFileDokumen(
        PersyaratanDokumen $persyaratan,
        string $extension
    ): string {

        /*
         * Ambil nama dokumen dari database.
         */
        $nama = $persyaratan->nama_dokumen ?? 'dokumen';

        /*
         * Bersihkan whitespace tersembunyi / non-breaking space.
         *
         * U+00A0 = No-Break Space
         * U+2007 = Figure Space
         * U+202F = Narrow No-Break Space
         */
        $nama = preg_replace(
            '/[\x{00A0}\x{2007}\x{202F}]/u',
            ' ',
            $nama
        );

        /*
         * Bersihkan spasi awal dan akhir.
         */
        $nama = trim($nama);

        /*
         * Hilangkan titik dan spasi pada bagian akhir.
         */
        $nama = rtrim($nama, " .");

        /*
         * Ubah menjadi nama filesystem yang aman.
         *
         * Contoh:
         * KTP Orang Tua .
         *
         * menjadi:
         * ktp-orang-tua
         */
        $nama = Str::slug($nama);

        /*
         * Fallback jika nama kosong.
         */
        if ($nama === '') {
            $nama = 'dokumen';
        }

        /*
         * ID persyaratan membuat nama file unik.
         *
         * Contoh:
         * 3-ktp-orang-tua.pdf
         */
        return $persyaratan->id
            . '-'
            . $nama
            . '.'
            . strtolower($extension);
    }


    // ── Helper: Cek Status Tiap Tahap ─────────────────────────────────

    private function cekStatus(): array
    {
        $tahunAjaran = TahunAjaran::aktif();
        $calonMurid  = $this->calonMuridAktif();

        $orangTua = $calonMurid
            ? OrangTua::where(
                'calon_murid_id',
                $calonMurid->id
            )->first()
            : null;

        $dokumenTemp = [];
        $totalWajib  = 0;
        $totalUpload = 0;

        if ($tahunAjaran) {

            $persyaratans = PersyaratanDokumen::where(
                'tahun_ajaran_id',
                $tahunAjaran->id
            )
                ->terurut()
                ->get();

            $totalWajib = $persyaratans
                ->where('is_wajib', true)
                ->count();

            $tempDir = $this->tempDir();

            $filesTemp = $tempDir
                ? Storage::disk('public')->files($tempDir)
                : [];

            foreach ($persyaratans as $p) {

                $adaFile = false;

                foreach ($filesTemp as $file) {

                    if (
                        str_contains(
                            basename($file),
                            'dokumen_' . $p->id . '.'
                        )
                    ) {

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

        $dokumenLengkap = $totalWajib > 0
            && $totalUpload >= $totalWajib;

        return [
            'murid'        => $calonMurid !== null,
            'ortu'         => $orangTua !== null,
            'dokumen'      => $dokumenLengkap,
            'calon_murid'  => $calonMurid,
            'orang_tua'    => $orangTua,
            'dokumen_temp' => $dokumenTemp,
            'total_wajib'  => $totalWajib,
            'total_upload' => $totalUpload,
        ];
    }


    // ── Index — Halaman Utama Formulir ────────────────────────────────

    public function index()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $buka = $tahunAjaran?->isPendaftaranBuka() ?? false;

        $pendaftaran = null;

        $calonMurid = $this->calonMuridAktif();

        /*
         * Cek pendaftaran milik anak yang sedang aktif.
         */
        if ($tahunAjaran && $calonMurid) {

            $pendaftaran = Pendaftaran::where(
                'user_id',
                Auth::id()
            )
                ->where(
                    'calon_murid_id',
                    $calonMurid->id
                )
                ->where(
                    'tahun_ajaran_id',
                    $tahunAjaran->id
                )
                ->first();

            if ($pendaftaran) {

                return redirect()
                    ->route('orangtua.pendaftaran.detail');
            }
        }

        $status = $buka
            ? $this->cekStatus()
            : [];

        return view(
            'orangtua.pendaftaran.index',
            compact(
                'tahunAjaran',
                'buka',
                'status'
            )
        );
    }


    // ── Tahap 1 — Data Calon Murid ────────────────────────────────────

    public function murid()
    {
        $this->cekSudahSubmit();

        $calonMurid = $this->calonMuridAktif();

        $tahap = 'murid';

        return view(
            'orangtua.pendaftaran.form',
            compact(
                'calonMurid',
                'tahap'
            )
        );
    }


    public function simpanMurid(Request $request)
    {
        $this->cekSudahSubmit();

        $request->validate(
            [
                'nama_lengkap'   => 'required|string|max:100',
                'nama_panggilan' => 'nullable|string|max:50',
                'jenis_kelamin'  => 'required|in:L,P',
                'tempat_lahir'   => 'required|string|max:50',
                'tanggal_lahir'  => 'required|date|before:' . now()->subYears(4)->format('Y-m-d'),
                'agama'          => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                'anak_ke'        => 'nullable|integer|min:1',
                'jumlah_saudara' => 'nullable|integer|min:0',
            ],
            [
                'nama_lengkap.required'   => 'Nama lengkap wajib diisi.',
                'jenis_kelamin.required'  => 'Jenis kelamin wajib dipilih.',
                'tempat_lahir.required'  => 'Tempat lahir wajib diisi.',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tanggal_lahir.before'    => 'Calon murid harus berusia minimal 4 tahun.',
                'agama.required'         => 'Agama wajib dipilih.',
            ]
        );

        $calonMurid = $this->calonMuridAktif();

        $data = array_merge(
            $request->only([
                'nama_lengkap',
                'nama_panggilan',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'anak_ke',
                'jumlah_saudara',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        /*
         * Jika ada anak aktif → update anak tersebut.
         */
        if ($calonMurid) {

            $calonMurid->update($data);
        } else {

            /*
             * Jika tidak ada anak aktif →
             * buat anak baru.
             */
            $calonMurid = CalonMurid::create($data);
        }

        /*
         * Jadikan anak yang baru disimpan sebagai anak aktif.
         */
        session([
            'active_calon_murid_id' => $calonMurid->id,
            'adding_new_child'      => false,
        ]);

        return redirect()
            ->route('orangtua.pendaftaran.index')
            ->with(
                'success',
                'Data calon murid berhasil disimpan.'
            );
    }


    // ── Tahap 2 — Data Orang Tua ──────────────────────────────────────

    public function orangTua()
    {
        $this->cekSudahSubmit();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.murid')
                ->with(
                    'error',
                    'Lengkapi data calon murid terlebih dahulu.'
                );
        }

        $orangTua = OrangTua::where(
            'calon_murid_id',
            $calonMurid->id
        )->first();

        $tahap = 'ortu';

        return view(
            'orangtua.pendaftaran.form',
            compact(
                'calonMurid',
                'orangTua',
                'tahap'
            )
        );
    }


    public function simpanOrangTua(Request $request)
    {
        $this->cekSudahSubmit();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.murid')
                ->with(
                    'error',
                    'Lengkapi data calon murid terlebih dahulu.'
                );
        }

        $request->validate(
            [
                'nama_ayah'      => 'nullable|string|max:100',
                'pekerjaan_ayah' => 'nullable|string|max:50',
                'nama_ibu'       => 'nullable|string|max:100',
                'pekerjaan_ibu'  => 'nullable|string|max:50',
                'nomor_hp'       => 'required|string|max:15',
                'nomor_saudara'  => 'nullable|string|max:15',
                'alamat'         => 'required|string',
            ],
            [
                'nomor_hp.required' => 'Nomor HP wajib diisi.',
                'alamat.required'   => 'Alamat wajib diisi.',
            ]
        );

        if (
            ! $request->nama_ayah
            && ! $request->nama_ibu
        ) {

            return back()
                ->withErrors([
                    'nama_ayah' =>
                    'Minimal salah satu dari nama ayah atau ibu harus diisi.',
                ])
                ->withInput();
        }

        OrangTua::updateOrCreate(
            [
                'calon_murid_id' => $calonMurid->id
            ],
            array_merge(
                $request->only([
                    'nama_ayah',
                    'pekerjaan_ayah',
                    'nama_ibu',
                    'pekerjaan_ibu',
                    'nomor_hp',
                    'nomor_saudara',
                    'alamat',
                ]),
                [
                    'calon_murid_id' => $calonMurid->id
                ]
            )
        );

        return redirect()
            ->route('orangtua.pendaftaran.index')
            ->with(
                'success',
                'Data orang tua berhasil disimpan.'
            );
    }


    // ── Tahap 3 — Upload Dokumen ──────────────────────────────────────

    public function dokumen()
    {
        $this->cekSudahSubmit();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.murid')
                ->with(
                    'error',
                    'Lengkapi data calon murid terlebih dahulu.'
                );
        }

        $tahunAjaran = TahunAjaran::aktif();

        $persyaratans = PersyaratanDokumen::where(
            'tahun_ajaran_id',
            $tahunAjaran?->id
        )
            ->terurut()
            ->get();

        $dokumenTemp = [];

        $tempDir = $this->tempDir();

        $filesTemp = $tempDir
            ? Storage::disk('public')->files($tempDir)
            : [];

        foreach ($persyaratans as $p) {

            foreach ($filesTemp as $file) {

                if (
                    str_contains(
                        basename($file),
                        'dokumen_' . $p->id
                    )
                ) {

                    $dokumenTemp[$p->id] = $file;

                    break;
                }
            }
        }

        $tahap = 'dokumen';

        return view(
            'orangtua.pendaftaran.form',
            compact(
                'calonMurid',
                'persyaratans',
                'dokumenTemp',
                'tahunAjaran',
                'tahap'
            )
        );
    }


    public function simpanDokumen(Request $request)
    {
        $this->cekSudahSubmit();

        $tahunAjaran = TahunAjaran::aktif();

        if (! $tahunAjaran) {

            return redirect()
                ->route('orangtua.pendaftaran.index')
                ->with(
                    'error',
                    'Tahun ajaran aktif tidak tersedia.'
                );
        }

        $persyaratans = PersyaratanDokumen::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->terurut()
            ->get();

        $tempDir = $this->tempDir();

        if (! $tempDir) {

            return redirect()
                ->route('orangtua.pendaftaran.murid')
                ->with(
                    'error',
                    'Lengkapi data calon murid terlebih dahulu.'
                );
        }

        /*
         * Pastikan folder temporary tersedia.
         */
        Storage::disk('public')->makeDirectory($tempDir);

        $rules = [];
        $messages = [];

        foreach ($persyaratans as $p) {

            $key = 'dokumen_' . $p->id;

            $sudahAda = false;

            $files = Storage::disk('public')->files($tempDir);

            foreach ($files as $file) {

                if (
                    str_contains(
                        basename($file),
                        'dokumen_' . $p->id
                    )
                ) {

                    $sudahAda = true;

                    break;
                }
            }

            /*
             * Dokumen wajib.
             */
            if (
                $p->is_wajib
                && ! $sudahAda
            ) {

                $rules[$key] = [
                    'required',
                    'file',
                    'mimes:' . $p->format_file,
                    'max:' . $p->maks_ukuran_kb
                ];

                $messages[$key . '.required'] =
                    $p->nama_dokumen .
                    ' wajib diupload.';

                $messages[$key . '.mimes'] =
                    $p->nama_dokumen .
                    ' harus format ' .
                    $p->format_file_tampil .
                    '.';

                $messages[$key . '.max'] =
                    $p->nama_dokumen .
                    ' maksimal ' .
                    $p->maks_ukuran_format .
                    '.';

            } elseif ($request->hasFile($key)) {

                $rules[$key] = [
                    'nullable',
                    'file',
                    'mimes:' . $p->format_file,
                    'max:' . $p->maks_ukuran_kb
                ];
            }
        }

        $request->validate(
            $rules,
            $messages
        );

        foreach ($persyaratans as $p) {

            $key = 'dokumen_' . $p->id;

            if (! $request->hasFile($key)) {
                continue;
            }

            $file = $request->file($key);

            if (! $file->isValid()) {
                continue;
            }

            /*
             * Nama file temporary sengaja tetap:
             *
             * dokumen_1.pdf
             * dokumen_2.jpg
             *
             * supaya mudah dicocokkan saat submit.
             */
            $namaFile =
                'dokumen_' .
                $p->id .
                '.' .
                strtolower(
                    $file->getClientOriginalExtension()
                );

            $tempPath =
                $tempDir .
                '/' .
                $namaFile;

            /*
             * Hapus file lama jika ada.
             */
            if (
                Storage::disk('public')
                    ->exists($tempPath)
            ) {

                Storage::disk('public')
                    ->delete($tempPath);
            }

            /*
             * Simpan file baru.
             */
            Storage::disk('public')->putFileAs(
                $tempDir,
                $file,
                $namaFile
            );
        }

        return redirect()
            ->route('orangtua.pendaftaran.index')
            ->with(
                'success',
                'Dokumen berhasil disimpan.'
            );
    }


    // ── Submit Final ──────────────────────────────────────────────────

    public function submit(Request $request)
    {
        $this->cekSudahSubmit();

        $status = $this->cekStatus();

        if (
            ! $status['murid']
            || ! $status['ortu']
            || ! $status['dokumen']
        ) {

            return redirect()
                ->route('orangtua.pendaftaran.index')
                ->with(
                    'error',
                    'Lengkapi semua tahap sebelum submit.'
                );
        }

        $tahunAjaran = TahunAjaran::aktif();

        $gelombang = $tahunAjaran?->gelombangAktif();

        $calonMurid = $status['calon_murid'];

        if (! $tahunAjaran) {

            return redirect()
                ->route('orangtua.pendaftaran.index')
                ->with(
                    'error',
                    'Tahun ajaran aktif tidak tersedia.'
                );
        }

        if (! $gelombang) {

            return redirect()
                ->route('orangtua.pendaftaran.index')
                ->with(
                    'error',
                    'Gelombang pendaftaran aktif tidak tersedia.'
                );
        }

        $persyaratans = PersyaratanDokumen::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->terurut()
            ->get();

        $tempDir = $this->tempDir();

        if (! $tempDir) {

            return redirect()
                ->route('orangtua.pendaftaran.murid')
                ->with(
                    'error',
                    'Data calon murid aktif tidak ditemukan.'
                );
        }

        /*
         * Pastikan temporary directory memang ada.
         */
        if (
            ! Storage::disk('public')
                ->exists($tempDir)
        ) {

            return redirect()
                ->route('orangtua.pendaftaran.dokumen')
                ->with(
                    'error',
                    'Dokumen persyaratan belum ditemukan.'
                );
        }

        DB::transaction(function () use (
            $calonMurid,
            $tahunAjaran,
            $gelombang,
            $persyaratans,
            $tempDir
        ) {

            // Buat pendaftaran untuk anak yang sedang aktif
            $pendaftaran = Pendaftaran::create([
                'nomor_daftar'   => Pendaftaran::generateNomorDaftar(),
                'user_id'        => Auth::id(),
                'calon_murid_id' => $calonMurid->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id'   => $gelombang->id,
                'jalur'          => 'online',
                'status'         => 'menunggu_verifikasi',
                'tgl_submit'     => now(),
            ]);

            /*
             * Folder permanen berdasarkan nomor pendaftaran.
             *
             * Contoh:
             * dokumen/PMB-2026-001
             */
            $folderPermanen =
                'dokumen/' .
                $pendaftaran->nomor_daftar;

            /*
             * PENTING:
             * Buat folder sebelum move().
             */
            Storage::disk('public')
                ->makeDirectory($folderPermanen);

            $files = Storage::disk('public')
                ->files($tempDir);

            foreach ($persyaratans as $p) {

                foreach ($files as $file) {

                    /*
                     * Cocokkan hanya nama file temporary.
                     *
                     * Contoh:
                     * dokumen_3.pdf
                     */
                    if (
                        str_contains(
                            basename($file),
                            'dokumen_' . $p->id
                        )
                    ) {

                        $ext = strtolower(
                            pathinfo(
                                $file,
                                PATHINFO_EXTENSION
                            )
                        );

                        /*
                         * Gunakan helper agar nama file aman.
                         *
                         * Contoh database:
                         * KTP Orang Tua .
                         *
                         * hasil:
                         * 3-ktp-orang-tua.pdf
                         */
                        $namaFile =
                            $this->namaFileDokumen(
                                $p,
                                $ext
                            );

                        $pathPermanen =
                            $folderPermanen .
                            '/' .
                            $namaFile;

                        /*
                         * Hapus file tujuan jika kebetulan sudah ada.
                         */
                        if (
                            Storage::disk('public')
                                ->exists($pathPermanen)
                        ) {

                            Storage::disk('public')
                                ->delete($pathPermanen);
                        }

                        /*
                         * Pindahkan file temporary
                         * ke folder permanen.
                         */
                        $berhasil =
                            Storage::disk('public')
                            ->move(
                                $file,
                                $pathPermanen
                            );

                        /*
                         * Pastikan move benar-benar berhasil.
                         */
                        if (! $berhasil) {

                            throw new \RuntimeException(
                                'Gagal memindahkan dokumen: ' .
                                $namaFile
                            );
                        }

                        /*
                         * Ambil ukuran file setelah dipindahkan.
                         */
                        $ukuranFile = 0;

                        if (
                            Storage::disk('public')
                                ->exists($pathPermanen)
                        ) {

                            $ukuranFile = round(
                                Storage::disk('public')
                                    ->size($pathPermanen) / 1024
                            );
                        }

                        /*
                         * Simpan data dokumen ke database.
                         */
                        Dokumen::create([
                            'pendaftaran_id' =>
                            $pendaftaran->id,

                            'persyaratan_id' =>
                            $p->id,

                            'nama_file' =>
                            $namaFile,

                            'path_file' =>
                            $pathPermanen,

                            'tipe_file' =>
                            $ext,

                            'ukuran_file' =>
                            $ukuranFile,

                            'status_verifikasi' =>
                            'menunggu',

                            'uploaded_at' =>
                            now(),
                        ]);

                        break;
                    }
                }
            }

            /*
             * Hapus folder temporary setelah
             * semua dokumen berhasil dipindahkan.
             */
            if (
                Storage::disk('public')
                    ->exists($tempDir)
            ) {

                Storage::disk('public')
                    ->deleteDirectory($tempDir);
            }
        });

        return redirect()
            ->route('orangtua.pendaftaran.detail')
            ->with(
                'success',
                'Pendaftaran berhasil disubmit! Silakan tunggu verifikasi dari admin.'
            );
    }


    // ── Detail Pendaftaran ────────────────────────────────────────────

    public function detail()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.index');
        }

        $pendaftaran = Pendaftaran::with([
            'calonMurid.orangTua',
            'gelombang',
            'dokumen.persyaratan',
        ])
            ->where(
                'user_id',
                Auth::id()
            )
            ->where(
                'calon_murid_id',
                $calonMurid->id
            )
            ->where(
                'tahun_ajaran_id',
                $tahunAjaran?->id
            )
            ->first();

        if (! $pendaftaran) {

            return redirect()
                ->route('orangtua.pendaftaran.index');
        }

        return view(
            'orangtua.pendaftaran.detail',
            compact(
                'pendaftaran'
            )
        );
    }


    // ── Revisi Dokumen ────────────────────────────────────────────────

    public function revisi()
    {
        $tahunAjaran = TahunAjaran::aktif();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.index');
        }

        $pendaftaran = Pendaftaran::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'calon_murid_id',
                $calonMurid->id
            )
            ->where(
                'tahun_ajaran_id',
                $tahunAjaran?->id
            )
            ->where(
                'status',
                'perlu_revisi'
            )
            ->first();

        if (! $pendaftaran) {

            return redirect()
                ->route('orangtua.pendaftaran.detail');
        }

        $persyaratans = PersyaratanDokumen::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->terurut()
            ->get();

        $dokumenExisting =
            $pendaftaran
                ->dokumen
                ->keyBy('persyaratan_id');

        return view(
            'orangtua.pendaftaran.revisi',
            compact(
                'pendaftaran',
                'persyaratans',
                'dokumenExisting'
            )
        );
    }


    public function simpanRevisi(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {

            return redirect()
                ->route('orangtua.pendaftaran.index');
        }

        if (! $tahunAjaran) {

            return redirect()
                ->route('orangtua.pendaftaran.index')
                ->with(
                    'error',
                    'Tahun ajaran aktif tidak tersedia.'
                );
        }

        $pendaftaran = Pendaftaran::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'calon_murid_id',
                $calonMurid->id
            )
            ->where(
                'tahun_ajaran_id',
                $tahunAjaran->id
            )
            ->where(
                'status',
                'perlu_revisi'
            )
            ->firstOrFail();

        $persyaratans = PersyaratanDokumen::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->terurut()
            ->get();

        foreach ($persyaratans as $p) {

            $key = 'dokumen_' . $p->id;

            /*
             * Kalau file untuk persyaratan ini tidak diganti,
             * lanjut ke persyaratan berikutnya.
             */
            if (! $request->hasFile($key)) {
                continue;
            }

            $file = $request->file($key);

            if (! $file->isValid()) {
                continue;
            }

            /*
             * Cari dokumen lama.
             */
            $old = Dokumen::where(
                'pendaftaran_id',
                $pendaftaran->id
            )
                ->where(
                    'persyaratan_id',
                    $p->id
                )
                ->first();

            /*
             * Hapus file lama dari storage.
             */
            if ($old) {

                if (
                    $old->path_file
                    && Storage::disk('public')
                        ->exists($old->path_file)
                ) {

                    Storage::disk('public')
                        ->delete(
                            $old->path_file
                        );
                }

                $old->delete();
            }

            $ext = strtolower(
                $file->getClientOriginalExtension()
            );

            /*
             * Gunakan helper yang sama dengan submit().
             *
             * Contoh:
             * KTP Orang Tua .
             *
             * menjadi:
             * 3-ktp-orang-tua.pdf
             */
            $namaFile =
                $this->namaFileDokumen(
                    $p,
                    $ext
                );

            $folderPermanen =
                'dokumen/' .
                $pendaftaran->nomor_daftar;

            /*
             * Pastikan folder tersedia.
             */
            Storage::disk('public')
                ->makeDirectory($folderPermanen);

            $path =
                $folderPermanen .
                '/' .
                $namaFile;

            /*
             * Hapus file tujuan jika ada.
             */
            if (
                Storage::disk('public')
                    ->exists($path)
            ) {

                Storage::disk('public')
                    ->delete($path);
            }

            /*
             * Simpan file revisi.
             */
            Storage::disk('public')->putFileAs(
                $folderPermanen,
                $file,
                $namaFile
            );

            /*
             * Simpan record dokumen baru.
             */
            Dokumen::create([
                'pendaftaran_id' =>
                $pendaftaran->id,

                'persyaratan_id' =>
                $p->id,

                'nama_file' =>
                $namaFile,

                'path_file' =>
                $path,

                'tipe_file' =>
                $ext,

                'ukuran_file' =>
                round(
                    $file->getSize() / 1024
                ),

                'status_verifikasi' =>
                'menunggu',

                'uploaded_at' =>
                now(),
            ]);
        }

        /*
         * Setelah revisi dikirim kembali,
         * status kembali menunggu verifikasi.
         */
        $pendaftaran->update([
            'status' => 'menunggu_verifikasi',
            'catatan_admin' => null,
        ]);

        return redirect()
            ->route('orangtua.pendaftaran.detail')
            ->with(
                'success',
                'Dokumen berhasil diupload. Menunggu verifikasi ulang dari admin.'
            );
    }


    // ── Private Helper: Cek Sudah Submit ──────────────────────────────

    private function cekSudahSubmit()
    {
        $tahunAjaran = TahunAjaran::aktif();

        if (! $tahunAjaran) {
            return;
        }

        $calonMurid = $this->calonMuridAktif();

        if (! $calonMurid) {
            return;
        }

        /*
         * Cek hanya pendaftaran milik anak aktif.
         */
        $pendaftaran = Pendaftaran::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'calon_murid_id',
                $calonMurid->id
            )
            ->where(
                'tahun_ajaran_id',
                $tahunAjaran->id
            )
            ->first();

        if ($pendaftaran) {

            redirect()
                ->route('orangtua.pendaftaran.detail')
                ->send();

            exit;
        }
    }
}
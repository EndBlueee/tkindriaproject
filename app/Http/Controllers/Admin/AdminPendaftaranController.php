<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonMurid;
use App\Models\Dokumen;
use App\Models\OrangTua;
use App\Models\Pendaftaran;
use App\Models\PersyaratanDokumen;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        $query = Pendaftaran::with(['calonMurid', 'gelombang', 'user'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jalur')) {
            $query->where('jalur', $request->jalur);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_daftar', 'like', '%' . $request->cari . '%')
                  ->orWhereHas('calonMurid', function ($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        $pendaftarans = $query->orderByDesc('tgl_submit')->paginate(15);

        return view('admin.pendaftaran.index', compact('pendaftarans', 'tahunAjaran'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'calonMurid.orangTua',
            'gelombang',
            'tahunAjaran',
            'dokumen.persyaratan',
            'diprosesOleh',
        ]);

        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function create()
    {
        $tahunAjaran  = TahunAjaran::aktif();

        if (! $tahunAjaran) {
            return redirect()->route('admin.pendaftaran.index')
                ->with('error', 'Tidak dapat menambah pendaftaran. Belum ada Tahun Ajaran yang aktif.');
        }

        $gelombang    = $tahunAjaran?->gelombang;
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $tahunAjaran?->id)
            ->terurut()->get();

        return view('admin.pendaftaran.create', compact('tahunAjaran', 'gelombang', 'persyaratans'));
    }

    public function store(Request $request)
    {
        $tahunAjaran = TahunAjaran::aktif();

        if (! $tahunAjaran) {
            return redirect()->route('admin.pendaftaran.index')
                ->with('error', 'Gagal menyimpan pendaftaran. Belum ada Tahun Ajaran yang aktif.');
        }

        $request->validate([
            // Info pendaftaran
            'gelombang_id'  => 'required|exists:gelombang,id',

            // Data calon murid — tanpa NIK
            'nama_lengkap'   => 'required|string|max:100',
            'nama_panggilan' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:50',
            'tanggal_lahir'  => 'required|date',
            'agama'          => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'anak_ke'        => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',

            // Data orang tua
            'nama_ayah'      => 'nullable|string|max:100',
            'nama_ibu'       => 'nullable|string|max:100',
            'nomor_hp_ortu'  => 'required|string|max:15',
            'nomor_saudara'  => 'nullable|string|max:15',
            'alamat'         => 'required|string',
        ], [
            'gelombang_id.required'  => 'Gelombang wajib dipilih.',
            'nama_lengkap.required'  => 'Nama lengkap anak wajib diisi.',
            'nomor_hp_ortu.required' => 'Nomor HP orang tua wajib diisi.',
            'alamat.required'        => 'Alamat wajib diisi.',
        ]);

        if (! $request->nama_ayah && ! $request->nama_ibu) {
            return back()->withErrors([
                'nama_ayah' => 'Minimal salah satu dari nama ayah atau ibu harus diisi.',
            ])->withInput();
        }

        DB::transaction(function () use ($request, $tahunAjaran) {

            // 1. Buat calon murid — tanpa NIK, tanpa akun (offline)
            $calonMurid = CalonMurid::create([
                'user_id'        => null,
                'nama_lengkap'   => $request->nama_lengkap,
                'nama_panggilan' => $request->nama_panggilan,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tempat_lahir'   => $request->tempat_lahir,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'agama'          => $request->agama,
                'anak_ke'        => $request->anak_ke,
                'jumlah_saudara' => $request->jumlah_saudara,
            ]);

            // 2. Buat data orang tua
            OrangTua::create([
                'calon_murid_id' => $calonMurid->id,
                'nama_ayah'      => $request->nama_ayah,
                'pekerjaan_ayah' => $request->pekerjaan_ayah,
                'nama_ibu'       => $request->nama_ibu,
                'pekerjaan_ibu'  => $request->pekerjaan_ibu,
                'nomor_hp'       => $request->nomor_hp_ortu,
                'nomor_saudara'  => $request->nomor_saudara,
                'alamat'         => $request->alamat,
            ]);

            // 3. Buat pendaftaran — user_id null karena offline (tidak punya akun)
            $pendaftaran = Pendaftaran::create([
                'nomor_daftar'    => Pendaftaran::generateNomorDaftar(),
                'user_id'         => null,
                'calon_murid_id'  => $calonMurid->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'gelombang_id'    => $request->gelombang_id,
                'jalur'           => 'offline',
                'status'          => 'menunggu_pembayaran',
                'tgl_submit'      => now(),
                'tgl_verifikasi'  => now(),
                'diproses_oleh'   => auth()->id(),
            ]);
        });

        return redirect()->route('admin.pendaftaran.index')
            ->with('success', 'Pendaftaran berhasil ditambahkan.');
    }

    // ── Edit Data Murid & Orang Tua ───────────────────────────────────

    public function edit(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['calonMurid.orangTua']);

        return view('admin.pendaftaran.edit', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $persyaratans = PersyaratanDokumen::where('tahun_ajaran_id', $pendaftaran->tahun_ajaran_id)->get();

        $rules = [
            // Data calon murid
            'nama_lengkap'   => 'required|string|max:100',
            'nama_panggilan' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:50',
            'tanggal_lahir'  => 'required|date',
            'agama'          => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'anak_ke'        => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',

            // Data orang tua
            'nama_ayah'     => 'nullable|string|max:100',
            'nama_ibu'      => 'nullable|string|max:100',
            'nomor_hp_ortu' => 'required|string|max:15',
            'nomor_saudara' => 'nullable|string|max:15',
            'alamat'        => 'required|string',
        ];

        $messages = [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'nomor_hp_ortu.required' => 'Nomor HP wajib diisi.',
            'alamat.required'        => 'Alamat wajib diisi.',
        ];

        foreach ($persyaratans as $p) {
            $key = 'dokumen_' . $p->id;
            if ($request->hasFile($key)) {
                $rules[$key] = ['nullable', 'file', 'mimes:' . $p->format_file, 'max:' . $p->maks_ukuran_kb];
                $messages[$key . '.mimes'] = $p->nama_dokumen . ' harus format ' . $p->format_file_tampil . '.';
                $messages[$key . '.max'] = $p->nama_dokumen . ' maksimal ' . $p->maks_ukuran_format . '.';
            }
        }

        $request->validate($rules, $messages);

        if (! $request->nama_ayah && ! $request->nama_ibu) {
            return back()->withErrors([
                'nama_ayah' => 'Minimal salah satu dari nama ayah atau ibu harus diisi.',
            ])->withInput();
        }

        // Update calon murid
        $pendaftaran->calonMurid->update([
            'nama_lengkap'   => $request->nama_lengkap,
            'nama_panggilan' => $request->nama_panggilan,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'tempat_lahir'   => $request->tempat_lahir,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'agama'          => $request->agama,
            'anak_ke'        => $request->anak_ke,
            'jumlah_saudara' => $request->jumlah_saudara,
        ]);

        // Update orang tua
        $pendaftaran->calonMurid->orangTua->update([
            'nama_ayah'      => $request->nama_ayah,
            'pekerjaan_ayah' => $request->pekerjaan_ayah,
            'nama_ibu'       => $request->nama_ibu,
            'pekerjaan_ibu'  => $request->pekerjaan_ibu,
            'nomor_hp'       => $request->nomor_hp_ortu,
            'nomor_saudara'  => $request->nomor_saudara,
            'alamat'         => $request->alamat,
        ]);

        // Update & Upload dokumen jika ada yang dipilih
        foreach ($persyaratans as $p) {
            $key = 'dokumen_' . $p->id;
            
            if ($request->hasFile($key)) {
                $file = $request->file($key);

                // Hapus dokumen lama jika ada
                $old = Dokumen::where('pendaftaran_id', $pendaftaran->id)
                    ->where('persyaratan_id', $p->id)
                    ->first();

                if ($old) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($old->path_file);
                    $old->delete();
                }

                $path = $file->store('dokumen/' . $pendaftaran->nomor_daftar, 'public');

                Dokumen::create([
                    'pendaftaran_id'    => $pendaftaran->id,
                    'persyaratan_id'    => $p->id,
                    'nama_file'         => $file->getClientOriginalName(),
                    'path_file'         => $path,
                    'tipe_file'         => $file->getClientOriginalExtension(),
                    'ukuran_file'       => round($file->getSize() / 1024),
                    'status_verifikasi' => 'diterima',
                    'uploaded_at'       => now(),
                ]);
            }
        }

        return redirect()->route('admin.pendaftaran.show', $pendaftaran)
            ->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function verifikasi(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Status pendaftaran tidak sesuai.');
        }

        $pendaftaran->update([
            'status'         => 'menunggu_pembayaran',
            'tgl_verifikasi' => now(),
            'diproses_oleh'  => auth()->id(),
            'catatan_admin'  => null,
        ]);

        $pendaftaran->dokumen()->update(['status_verifikasi' => 'diterima']);

        return back()->with('success', 'Dokumen berhasil diverifikasi. Pendaftaran siap melakukan pembayaran.');
    }

    public function revisi(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Status pendaftaran tidak sesuai.');
        }

        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Catatan revisi wajib diisi.',
        ]);

        $pendaftaran->update([
            'status'        => 'perlu_revisi',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Pendaftaran dikembalikan untuk revisi dokumen.');
    }
}
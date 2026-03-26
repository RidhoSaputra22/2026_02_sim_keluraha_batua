<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\DestinasiWisata;
use App\Models\DokumenPublik;
use App\Models\Faskes;
use App\Models\JenisUsaha;
use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\LayananSurat;
use App\Models\PegawaiStaff;
use App\Models\Penduduk;
use App\Models\PengaduanWarga;
use App\Models\Rw;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuestController extends Controller
{
    public function welcome()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $kelurahan = Kelurahan::first();
        $totalPenduduk = Penduduk::count();
        $totalKK = Keluarga::count();
        $totalUmkm = Umkm::count();
        $totalRw = Rw::count();
        $layananUnggulan = LayananSurat::active()->withCount('persyaratans')->ordered()->take(4)->get();
        $beritaTerkini = Berita::latestPublished()->take(3)->get();
        $dokumenPublik = DokumenPublik::latestPublished()->take(4)->get();
        $destinasiUnggulan = DestinasiWisata::published()
            ->where('is_featured', true)
            ->ordered()
            ->take(3)
            ->get();

        return view('guest.welcome', compact(
            'kelurahan',
            'totalPenduduk',
            'totalKK',
            'totalUmkm',
            'totalRw',
            'layananUnggulan',
            'beritaTerkini',
            'dokumenPublik',
            'destinasiUnggulan',
        ));
    }

    public function profil()
    {
        $kelurahan = Kelurahan::first();
        $totalPenduduk = Penduduk::count();
        $totalKK = Keluarga::count();
        $totalUmkm = Umkm::count();
        $totalRw = Rw::count();
        $pegawai = PegawaiStaff::orderBy('no_urut')->get();

        return view('guest.profil', compact(
            'kelurahan',
            'totalPenduduk',
            'totalKK',
            'totalUmkm',
            'totalRw',
            'pegawai',
        ));
    }

    public function dataKelurahan()
    {
        $totalPenduduk = Penduduk::count();
        $totalKK = Keluarga::count();
        $totalLakiLaki = Penduduk::where('jenis_kelamin', 'Laki-laki')->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', 'Perempuan')->count();
        $totalRw = Rw::count();
        $totalFaskes = Faskes::count();
        $totalSekolah = Sekolah::count();
        $totalTempatIbadah = TempatIbadah::count();

        return view('guest.data_kelurahan', compact(
            'totalPenduduk',
            'totalKK',
            'totalLakiLaki',
            'totalPerempuan',
            'totalRw',
            'totalFaskes',
            'totalSekolah',
            'totalTempatIbadah',
        ));
    }

    public function cekData()
    {
        return view('guest.cek_data');
    }

    public function cekDataSearch()
    {
        $validated = request()->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
        ]);

        $penduduk = Penduduk::where('nik', $validated['nik'])->first();

        if (! $penduduk) {
            return back()->with('error', 'Data dengan NIK tersebut tidak ditemukan.')->withInput();
        }

        return back()->with('result', [
            'nik' => $penduduk->nik,
            'nama' => $penduduk->nama,
            'alamat' => $penduduk->alamat,
            'jenis_kelamin' => $penduduk->jenis_kelamin,
            'agama' => $penduduk->agama,
            'status_kawin' => $penduduk->status_kawin,
            'rt' => $penduduk->rt?->nomor,
            'rw' => $penduduk->rt?->rw?->nomor,
        ])->withInput();
    }

    public function suratOnline(Request $request)
    {
        $query = LayananSurat::with('persyaratans')->active()->ordered();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('biaya', 'like', "%{$search}%");
            });
        }

        $layananSurat = $query->get();

        return view('guest.surat_online', [
            'layananSurat' => $layananSurat,
            'activeLayananSlug' => $request->get('layanan'),
        ]);
    }

    public function publikasi(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $beritaQuery = Berita::latestPublished();
        $dokumenQuery = DokumenPublik::latestPublished();

        if ($search !== '') {
            $beritaQuery->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%");
            });

            $dokumenQuery->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($kategoriBerita = $request->get('kategori')) {
            $beritaQuery->where('kategori', $kategoriBerita);
        }

        if ($kategoriDokumen = $request->get('dokumen_kategori')) {
            $dokumenQuery->where('kategori', $kategoriDokumen);
        }

        $highlightQuery = clone $beritaQuery;
        $highlightBerita = (clone $highlightQuery)->where('is_featured', true)->first()
            ?? (clone $highlightQuery)->first();

        $berita = $beritaQuery->paginate(6, ['*'], 'berita_page')->withQueryString();
        $dokumenPublik = $dokumenQuery->paginate(8, ['*'], 'dokumen_page')->withQueryString();

        $kategoriBeritaCounts = Berita::published()
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        $kategoriDokumenCounts = DokumenPublik::published()
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        return view('guest.publikasi', compact(
            'berita',
            'dokumenPublik',
            'highlightBerita',
            'kategoriBeritaCounts',
            'kategoriDokumenCounts',
            'search',
        ));
    }

    public function showBerita(Berita $berita)
    {
        abort_unless(
            $berita->is_published && $berita->published_at !== null && $berita->published_at->lte(now()),
            404
        );

        $relatedBerita = Berita::latestPublished()
            ->where('id', '!=', $berita->id)
            ->where('kategori', $berita->kategori)
            ->take(3)
            ->get();

        return view('guest.berita_show', compact('berita', 'relatedBerita'));
    }

    public function downloadDokumenPublik(DokumenPublik $dokumenPublik)
    {
        abort_unless(
            $dokumenPublik->is_published
                && $dokumenPublik->published_at !== null
                && $dokumenPublik->published_at->lte(now()),
            404
        );

        abort_unless($dokumenPublik->file_path && Storage::disk('public')->exists($dokumenPublik->file_path), 404);

        return Storage::disk('public')->download(
            $dokumenPublik->file_path,
            basename($dokumenPublik->file_path)
        );
    }

    public function parawisata(Request $request)
    {
        $query = DestinasiWisata::published()->ordered();

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('ringkasan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        $destinasiWisata = $query->paginate(9)->withQueryString();
        $destinasiUnggulan = DestinasiWisata::published()
            ->where('is_featured', true)
            ->ordered()
            ->take(3)
            ->get();

        return view('guest.parawisata', compact('destinasiWisata', 'destinasiUnggulan'));
    }

    public function umkm(Request $request)
    {
        $query = Umkm::with(['jenisUsaha', 'rt.rw'])->latest('id');

        if ($search = trim((string) $request->get('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_ukm', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('sektor_umkm', 'like', "%{$search}%");
            });
        }

        if ($jenisUsahaId = $request->get('jenis_usaha_id')) {
            $query->where('jenis_usaha_id', $jenisUsahaId);
        }

        if ($sektor = $request->get('sektor_umkm')) {
            $query->where('sektor_umkm', $sektor);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $umkmList = $query->paginate(9)->withQueryString();
        $jenisUsahaList = JenisUsaha::withCount('umkms')->orderBy('nama')->get();
        $topJenisUsaha = JenisUsaha::withCount('umkms')->orderByDesc('umkms_count')->take(3)->get();
        $totalUmkm = Umkm::count();
        $totalUmkmAktif = Umkm::where('status', 'aktif')->count();
        $sektorList = Umkm::query()
            ->whereNotNull('sektor_umkm')
            ->distinct()
            ->orderBy('sektor_umkm')
            ->pluck('sektor_umkm');

        return view('guest.umkm', compact(
            'umkmList',
            'jenisUsahaList',
            'topJenisUsaha',
            'totalUmkm',
            'totalUmkmAktif',
            'sektorList',
        ));
    }

    public function pengaduan()
    {
        return view('guest.pengaduan');
    }

    public function storePengaduan(Request $request)
    {
        $validated = $request->validate([
            'subjek' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:' . implode(',', array_keys(PengaduanWarga::kategoriOptions()))],
            'isi_laporan' => ['required', 'string'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['required', 'string', 'max:30'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('website/pengaduan', 'public');
        }

        $pengaduan = PengaduanWarga::create($validated);

        return redirect()->route('guest.pengaduan')
            ->with('success', 'Pengaduan Anda berhasil dikirim.')
            ->with('kode_pengaduan', $pengaduan->kode_pengaduan);
    }

    public function kontak()
    {
        $kelurahan = Kelurahan::first();

        return view('guest.kontak', compact('kelurahan'));
    }
}

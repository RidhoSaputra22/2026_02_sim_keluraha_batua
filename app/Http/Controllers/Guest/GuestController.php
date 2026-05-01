<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Berita;
use App\Models\DestinasiWisata;
use App\Models\DokumenPublik;
use App\Models\Faskes;
use App\Models\JenisUsaha;
use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\Kontrakan;
use App\Models\LayananSurat;
use App\Models\PegawaiStaff;
use App\Models\Penduduk;
use App\Models\PengaduanWarga;
use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function globalSearch(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim((string) ($validated['q'] ?? ''));
        $allResults = collect();

        if ($search !== '') {
            $allResults = collect(array_merge(
                $this->searchGuestQuickLinks($search),
                $this->searchGuestLayananSurat($search),
                $this->searchGuestBerita($search),
                $this->searchGuestDokumenPublik($search),
                $this->searchGuestDestinasiWisata($search),
                $this->searchGuestUmkm($search),
            ));
        }

        $resultGroups = $allResults->groupBy('category');
        $summaryCards = $resultGroups->map(function ($items, $category) {
            return [
                'title' => $category,
                'count' => $items->count(),
                'icon' => $items->first()['icon'] ?? 'search',
            ];
        })->values();

        $results = $this->paginateGuestCollection($allResults->values(), $request, 10);

        return view('guest.search', [
            'search' => $search,
            'results' => $results,
            'summaryCards' => $summaryCards,
            'popularKeywords' => $this->guestPopularKeywords(),
            'shortcutLinks' => $this->guestShortcutLinks(),
            'searchScopes' => $this->guestSearchScopes(),
        ]);
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
        $kelurahan = Kelurahan::first();
        $totalPenduduk = Penduduk::count();
        $totalKK = Keluarga::count();
        $totalLakiLaki = Penduduk::whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count();
        $totalPerempuan = Penduduk::whereIn('jenis_kelamin', ['P', 'Perempuan'])->count();
        $totalRw = Rw::count();
        $totalRt = Rt::count();
        $totalUmkm = Umkm::count();
        $totalFaskes = Faskes::count();
        $totalSekolah = Sekolah::count();
        $totalTempatIbadah = TempatIbadah::count();
        $totalLayanan = LayananSurat::active()->count();
        $totalDokumen = DokumenPublik::published()->count();
        $totalBerita = Berita::published()->count();
        $avgAnggotaKeluarga = round((float) Keluarga::avg('jumlah_anggota_keluarga'), 1);

        $komposisiGender = collect([
            [
                'label' => 'Laki-laki',
                'value' => $totalLakiLaki,
                'color' => 'bg-slate-900',
            ],
            [
                'label' => 'Perempuan',
                'value' => $totalPerempuan,
                'color' => 'bg-primary',
            ],
        ])->filter(fn (array $item) => $item['value'] > 0)
            ->map(fn (array $item) => [
                ...$item,
                'percentage' => $totalPenduduk > 0 ? (int) round(($item['value'] / $totalPenduduk) * 100) : 0,
            ])
            ->values();

        $komposisiAgama = Penduduk::query()
            ->get(['agama'])
            ->groupBy(fn (Penduduk $penduduk) => filled($penduduk->agama) ? $penduduk->agama : 'Belum diisi')
            ->map(function ($items, string $label) use ($totalPenduduk) {
                $total = $items->count();

                return [
                    'label' => $label,
                    'value' => $total,
                    'percentage' => $totalPenduduk > 0 ? (int) round(($total / $totalPenduduk) * 100) : 0,
                ];
            })
            ->sortByDesc('value')
            ->take(3)
            ->values();

        $statusPenduduk = Penduduk::query()
            ->get(['status_data'])
            ->groupBy(fn (Penduduk $penduduk) => filled($penduduk->status_data) ? $penduduk->status_data : 'Belum diisi')
            ->map(function ($items, string $label) use ($totalPenduduk) {
                $total = $items->count();

                return [
                    'label' => $label,
                    'value' => $total,
                    'percentage' => $totalPenduduk > 0 ? (int) round(($total / $totalPenduduk) * 100) : 0,
                ];
            })
            ->sortByDesc('value')
            ->values();

        $rwHighlights = Rw::query()
            ->leftJoin('rts', 'rws.id', '=', 'rts.rw_id')
            ->leftJoin('penduduks', 'rts.id', '=', 'penduduks.rt_id')
            ->selectRaw('rws.id, rws.nomor, COUNT(DISTINCT rts.id) as total_rt, COUNT(penduduks.id) as total_penduduk')
            ->groupBy('rws.id', 'rws.nomor')
            ->orderByDesc('total_penduduk')
            ->orderBy('rws.nomor')
            ->get()
            ->map(fn ($rw) => [
                'label' => 'RW '.str_pad((string) $rw->nomor, 2, '0', STR_PAD_LEFT),
                'nomor' => (int) $rw->nomor,
                'total_rt' => (int) $rw->total_rt,
                'total_penduduk' => (int) $rw->total_penduduk,
            ]);

        $chartRwHighlights = $rwHighlights->take(6)->values();
        $chartRwMax = max(1, (int) $chartRwHighlights->max('total_penduduk'));

        $usiaPenduduk = Penduduk::query()
            ->get(['nik'])
            ->map(fn (Penduduk $penduduk) => $this->parsePendudukBirthDateFromNik($penduduk->nik)?->age)
            ->filter(fn ($umur) => is_int($umur))
            ->values();

        $sebaranUmur = collect($this->guestAgeRanges())
            ->map(function (array $range) use ($usiaPenduduk, $totalPenduduk) {
                $total = $usiaPenduduk
                    ->filter(function (int $umur) use ($range) {
                        if ($umur < $range['min']) {
                            return false;
                        }

                        return $range['max'] === null || $umur <= $range['max'];
                    })
                    ->count();

                return [
                    ...$range,
                    'value' => $total,
                    'percentage' => $totalPenduduk > 0 ? (int) round(($total / $totalPenduduk) * 100) : 0,
                ];
            })
            ->values();

        $chartUmurMax = max(1, (int) $sebaranUmur->max('value'));
        $cakupanDataUmur = $usiaPenduduk->count();
        $kelompokUmurTerbesar = $sebaranUmur->sortByDesc('value')->first();
        $usiaProduktif = $usiaPenduduk->filter(fn (int $umur) => $umur >= 18 && $umur <= 59)->count();
        $usiaLansia = $usiaPenduduk->filter(fn (int $umur) => $umur >= 60)->count();

        $layananUnggulan = LayananSurat::active()
            ->withCount('persyaratans')
            ->ordered()
            ->take(4)
            ->get();

        $dokumenPublik = DokumenPublik::latestPublished()->take(3)->get();
        $laporanPublik = DokumenPublik::latestPublished()
            ->where('kategori', 'laporan')
            ->first();

        return view('guest.data_kelurahan', compact(
            'kelurahan',
            'totalPenduduk',
            'totalKK',
            'totalLakiLaki',
            'totalPerempuan',
            'totalRw',
            'totalRt',
            'totalUmkm',
            'totalFaskes',
            'totalSekolah',
            'totalTempatIbadah',
            'totalLayanan',
            'totalDokumen',
            'totalBerita',
            'avgAnggotaKeluarga',
            'komposisiGender',
            'komposisiAgama',
            'statusPenduduk',
            'rwHighlights',
            'chartRwHighlights',
            'chartRwMax',
            'sebaranUmur',
            'chartUmurMax',
            'cakupanDataUmur',
            'kelompokUmurTerbesar',
            'usiaProduktif',
            'usiaLansia',
            'layananUnggulan',
            'dokumenPublik',
            'laporanPublik',
        ));
    }

    public function dataKelurahanMap(): JsonResponse
    {
        $kelurahan = Kelurahan::first();
        $layers = collect($this->guestActiveMapLayers());
        $kelurahanGeojson = $layers->firstWhere('layer_type', 'kelurahan')['geojson'] ?? $this->emptyGuestFeatureCollection('kelurahan');
        $rwGeojson = $layers->firstWhere('layer_type', 'rw')['geojson'] ?? $this->emptyGuestFeatureCollection('rw');

        if ($layers->filter(fn (array $layer) => ($layer['feature_count'] ?? 0) > 0)->isEmpty()) {
            return response()->json([
                'message' => 'Data peta kelurahan belum tersedia.',
            ], 404);
        }

        return response()->json([
            'kelurahan' => [
                'profile' => [
                    'nama' => $kelurahan?->nama,
                    'luas_area' => $kelurahan?->luas_area,
                    'alamat_kantor' => $kelurahan?->alamat_kantor,
                ],
                'geojson' => $kelurahanGeojson,
            ],
            'rw' => [
                'geojson' => $rwGeojson,
            ],
            'layers' => $layers->values()->all(),
            'summary' => [
                'total_penduduk' => Penduduk::count(),
                'total_kk' => Keluarga::count(),
                'total_rw' => Rw::count(),
                'total_rt' => Rt::count(),
                'total_umkm' => Umkm::count(),
                'laki_laki' => Penduduk::whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count(),
                'perempuan' => Penduduk::whereIn('jenis_kelamin', ['P', 'Perempuan'])->count(),
            ],
            'meta' => [
                'updated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function cekData()
    {
        return view('guest.cek_data', [
            'totalPenduduk' => Penduduk::count(),
            'totalKK' => Keluarga::count(),
            'totalRw' => Rw::count(),
        ]);
    }

    public function cekDataSearch()
    {
        $validated = request()->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/'],
        ]);

        $penduduk = Penduduk::with(['keluarga', 'rt.rw'])
            ->where('nik', $validated['nik'])
            ->first();

        if (! $penduduk) {
            return back()->with('error', 'Data dengan NIK tersebut tidak ditemukan.')->withInput();
        }

        return back()->with('result', [
            'nik' => $this->maskSensitiveNumber($penduduk->nik),
            'nama' => $this->maskSensitiveWords($penduduk->nama),
            'no_kk' => $this->maskSensitiveNumber($penduduk->keluarga?->no_kk ?: '-'),
            'alamat' => $this->maskSensitiveWords($penduduk->alamat ?: '-', 2),
            'jenis_kelamin' => $this->formatPendudukJenisKelamin($penduduk->jenis_kelamin),
            'agama' => $this->formatPendudukText($penduduk->agama),
            'status_kawin' => $this->formatPendudukText($penduduk->status_kawin),
            'pendidikan' => $this->formatPendudukText($penduduk->pendidikan),
            'pekerjaan' => $this->formatPendudukText($penduduk->pekerjaan),
            'status_data' => $this->formatPendudukStatus($penduduk->status_data),
            'rt' => $penduduk->rt?->nomor ?: '-',
            'rw' => $penduduk->rt?->rw?->nomor ?: '-',
        ])->withInput()->withFragment('cek-data-result');
    }

    public function administrasi(Request $request)
    {
        $search = trim((string) $request->get('q'));
        $query = LayananSurat::withCount('persyaratans')->active()->ordered();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('biaya', 'like', "%{$search}%")
                    ->orWhere('estimasi_layanan', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%");

                $builder->orWhereHas('persyaratans', function (Builder $relation) use ($search) {
                    $relation->where('nama', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            });
        }

        $layananSurat = $query->paginate(8)->withQueryString();

        return view('guest.administrasi', [
            'layananSurat' => $layananSurat,
            'search' => $search,
        ]);
    }

    public function showAdministrasi(LayananSurat $layananSurat)
    {
        abort_unless($layananSurat->is_active, 404);

        $layananSurat->load('persyaratans');

        $layananLainnya = LayananSurat::query()
            ->active()
            ->ordered()
            ->whereKeyNot($layananSurat->getKey())
            ->take(4)
            ->get();

        return view('guest.administrasi_show', [
            'layananSurat' => $layananSurat,
            'layananLainnya' => $layananLainnya,
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

        if ($relatedBerita->count() < 3) {
            $tambahanBerita = Berita::latestPublished()
                ->where('id', '!=', $berita->id)
                ->whereNotIn('id', $relatedBerita->pluck('id'))
                ->take(3 - $relatedBerita->count())
                ->get();

            $relatedBerita = $relatedBerita->concat($tambahanBerita)->values();
        }

        return view('guest.berita_show', compact('berita', 'relatedBerita'));
    }

    public function showDokumenPublik(DokumenPublik $dokumenPublik)
    {
        $this->assertDokumenPublikCanBeAccessed($dokumenPublik);

        $previewMode = $this->resolveDokumenPublikPreviewMode($dokumenPublik);
        $fileExtension = strtoupper(pathinfo((string) $dokumenPublik->file_path, PATHINFO_EXTENSION) ?: 'file');
        $fileSizeLabel = $this->formatFileSize($dokumenPublik->file_size);
        $relatedDokumen = DokumenPublik::latestPublished()
            ->whereKeyNot($dokumenPublik->getKey())
            ->where('kategori', $dokumenPublik->kategori)
            ->take(3)
            ->get();

        if ($relatedDokumen->count() < 3) {
            $tambahanDokumen = DokumenPublik::latestPublished()
                ->whereKeyNot($dokumenPublik->getKey())
                ->whereNotIn('id', $relatedDokumen->pluck('id'))
                ->take(3 - $relatedDokumen->count())
                ->get();

            $relatedDokumen = $relatedDokumen->concat($tambahanDokumen)->values();
        }

        return view('guest.dokumen_show', [
            'dokumenPublik' => $dokumenPublik,
            'previewMode' => $previewMode,
            'fileExtension' => $fileExtension,
            'fileSizeLabel' => $fileSizeLabel,
            'relatedDokumen' => $relatedDokumen,
        ]);
    }

    public function previewDokumenPublik(DokumenPublik $dokumenPublik)
    {
        $absolutePath = $this->dokumenPublikAbsolutePath($dokumenPublik);

        return response()->file($absolutePath, array_filter([
            'Content-Type' => $dokumenPublik->mime_type,
        ]));
    }

    public function downloadDokumenPublik(DokumenPublik $dokumenPublik)
    {
        $absolutePath = $this->dokumenPublikAbsolutePath($dokumenPublik);

        return response()->download(
            $absolutePath,
            basename((string) $dokumenPublik->file_path),
            array_filter([
                'Content-Type' => $dokumenPublik->mime_type,
            ])
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
            'kategori' => ['required', 'in:'.implode(',', array_keys(PengaduanWarga::kategoriOptions()))],
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

    private function applyGuestLike(Builder $query, array $columns, string $search): void
    {
        $normalizedSearch = Str::lower(trim($search));
        $words = array_values(array_filter(
            array_map(static fn (string $word) => Str::lower($word), preg_split('/\s+/', trim($search)) ?: [])
        ));

        $query->where(function (Builder $outer) use ($columns, $normalizedSearch, $words) {
            foreach ($columns as $column) {
                $this->applyGuestCaseInsensitiveLike($outer, $column, $normalizedSearch, 'or');
            }

            if (count($words) > 1) {
                foreach ($columns as $column) {
                    $outer->orWhere(function (Builder $inner) use ($column, $words) {
                        foreach ($words as $word) {
                            $this->applyGuestCaseInsensitiveLike($inner, $column, $word);
                        }
                    });
                }
            }
        });
    }

    private function applyGuestCaseInsensitiveLike(
        Builder $query,
        string $column,
        string $value,
        string $boolean = 'and'
    ): void {
        $wrappedColumn = $query->getQuery()->getGrammar()->wrap($column);
        $method = $boolean === 'or' ? 'orWhereRaw' : 'whereRaw';

        $query->{$method}("LOWER({$wrappedColumn}) LIKE ?", ['%'.Str::lower($value).'%']);
    }

    private function matchesGuestSearchText(string $haystack, string $search): bool
    {
        $normalizedHaystack = Str::of($haystack)->lower()->squish()->value();
        $normalizedSearch = Str::of($search)->lower()->squish()->value();

        if ($normalizedSearch === '') {
            return false;
        }

        if (Str::contains($normalizedHaystack, $normalizedSearch)) {
            return true;
        }

        $words = array_values(array_filter(explode(' ', $normalizedSearch)));

        if (count($words) <= 1) {
            return false;
        }

        foreach ($words as $word) {
            if (! Str::contains($normalizedHaystack, $word)) {
                return false;
            }
        }

        return true;
    }

    private function searchGuestQuickLinks(string $search): array
    {
        return collect($this->guestShortcutLinks())
            ->filter(function (array $item) use ($search) {
                $haystack = implode(' ', array_merge(
                    [$item['title'], $item['subtitle'], $item['description']],
                    $item['keywords'] ?? []
                ));

                return $this->matchesGuestSearchText($haystack, $search);
            })
            ->map(function (array $item) {
                unset($item['keywords']);

                return $item;
            })
            ->values()
            ->all();
    }

    private function searchGuestLayananSurat(string $search, ?int $limit = null): array
    {
        $query = LayananSurat::query()
            ->withCount('persyaratans')
            ->active()
            ->ordered();

        $query->where(function (Builder $builder) use ($search) {
            $this->applyGuestLike($builder, ['nama', 'deskripsi', 'biaya', 'catatan', 'estimasi_layanan'], $search);

            $builder->orWhereHas('persyaratans', function (Builder $relation) use ($search) {
                $this->applyGuestLike($relation, ['nama', 'keterangan'], $search);
            });
        });

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (LayananSurat $item) {
                $subtitle = collect([
                    $item->estimasi_layanan ? 'Estimasi '.$item->estimasi_layanan : null,
                    $item->biaya ? 'Biaya '.$item->biaya : null,
                ])->filter()->implode(' • ');

                return [
                    'category' => 'Layanan Surat',
                    'icon' => $item->icon ?: 'description',
                    'title' => $item->nama,
                    'subtitle' => $subtitle !== '' ? $subtitle : 'Layanan administrasi kelurahan',
                    'description' => Str::limit(
                        $item->deskripsi ?: $item->catatan ?: "Tersedia {$item->persyaratans_count} persyaratan layanan.",
                        140
                    ),
                    'url' => route('guest.administrasi.show', $item),
                    'action_label' => 'Lihat Detail',
                ];
            })
            ->toArray();
    }

    private function searchGuestBerita(string $search, ?int $limit = null): array
    {
        $query = Berita::latestPublished();
        $this->applyGuestLike($query, ['judul', 'ringkasan', 'isi', 'kategori'], $search);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (Berita $item) {
                return [
                    'category' => 'Berita & Pengumuman',
                    'icon' => 'article',
                    'title' => $item->judul,
                    'subtitle' => collect([
                        Berita::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori),
                        $item->published_at?->translatedFormat('d M Y'),
                    ])->filter()->implode(' • '),
                    'description' => Str::limit($item->ringkasan ?: strip_tags($item->isi), 140),
                    'url' => route('guest.berita.show', $item),
                    'action_label' => 'Baca Berita',
                ];
            })
            ->toArray();
    }

    private function searchGuestDokumenPublik(string $search, ?int $limit = null): array
    {
        $query = DokumenPublik::latestPublished();
        $this->applyGuestLike($query, ['judul', 'deskripsi', 'kategori'], $search);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (DokumenPublik $item) {
                return [
                    'category' => 'Dokumen Publik',
                    'icon' => 'description',
                    'title' => $item->judul,
                    'subtitle' => collect([
                        DokumenPublik::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori),
                        $item->published_at?->translatedFormat('d M Y'),
                    ])->filter()->implode(' • '),
                    'description' => Str::limit(
                        $item->deskripsi ?: 'Dokumen publik ini tersedia untuk dilihat pada halaman publikasi atau diunduh langsung.',
                        140
                    ),
                    'url' => route('guest.dokumen.show', $item),
                    'action_label' => 'Lihat Dokumen',
                    'secondary_url' => route('guest.publikasi.download', $item),
                    'secondary_label' => 'Unduh',
                ];
            })
            ->toArray();
    }

    private function dokumenPublikAbsolutePath(DokumenPublik $dokumenPublik): string
    {
        $this->assertDokumenPublikCanBeAccessed($dokumenPublik);

        return Storage::disk('public')->path($dokumenPublik->file_path);
    }

    private function assertDokumenPublikCanBeAccessed(DokumenPublik $dokumenPublik): void
    {
        abort_unless(
            $dokumenPublik->is_published
                && $dokumenPublik->published_at !== null
                && $dokumenPublik->published_at->lte(now()),
            404
        );

        abort_unless(
            filled($dokumenPublik->file_path) && Storage::disk('public')->exists($dokumenPublik->file_path),
            404
        );
    }

    private function resolveDokumenPublikPreviewMode(DokumenPublik $dokumenPublik): string
    {
        $mimeType = Str::lower((string) ($dokumenPublik->mime_type ?: Storage::disk('public')->mimeType($dokumenPublik->file_path)));
        $extension = Str::lower(pathinfo((string) $dokumenPublik->file_path, PATHINFO_EXTENSION));

        if (Str::startsWith($mimeType, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return 'image';
        }

        if (
            in_array($mimeType, ['application/pdf', 'text/plain', 'text/csv'], true)
            || in_array($extension, ['pdf', 'txt', 'csv'], true)
        ) {
            return 'iframe';
        }

        return 'unsupported';
    }

    private function formatFileSize(?int $bytes): ?string
    {
        if (! $bytes || $bytes < 1) {
            return null;
        }

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 2) . ' MB';
        }

        return number_format($bytes / 1024, 1) . ' KB';
    }

    private function searchGuestDestinasiWisata(string $search, ?int $limit = null): array
    {
        $query = DestinasiWisata::published()->ordered();
        $this->applyGuestLike($query, ['nama', 'ringkasan', 'deskripsi', 'alamat', 'kategori'], $search);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (DestinasiWisata $item) {
                return [
                    'category' => 'Pariwisata & Rekomendasi',
                    'icon' => 'place',
                    'title' => $item->nama,
                    'subtitle' => collect([
                        DestinasiWisata::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori),
                        $item->alamat ? Str::limit($item->alamat, 70) : null,
                    ])->filter()->implode(' • '),
                    'description' => Str::limit($item->ringkasan ?: strip_tags((string) $item->deskripsi), 140),
                    'url' => route('guest.parawisata', array_filter([
                        'q' => $item->nama,
                        'kategori' => $item->kategori,
                    ])),
                    'action_label' => 'Lihat Destinasi',
                    'secondary_url' => $item->maps_url,
                    'secondary_label' => $item->maps_url ? 'Buka Maps' : null,
                ];
            })
            ->toArray();
    }

    private function searchGuestUmkm(string $search, ?int $limit = null): array
    {
        $query = Umkm::query()
            ->with(['jenisUsaha', 'rt.rw'])
            ->latest('id');

        $this->applyGuestLike(
            $query,
            ['nama_ukm', 'nama_pemilik', 'alamat', 'sektor_umkm', 'nik_pemilik', 'status'],
            $search
        );

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(function (Umkm $item) {
                $status = $item->status ? ucfirst(str_replace('_', ' ', $item->status)) : null;

                return [
                    'category' => 'UMKM',
                    'icon' => 'storefront',
                    'title' => $item->nama_ukm ?: 'Usaha Warga',
                    'subtitle' => collect([
                        $item->jenisUsaha?->nama,
                        $item->nama_pemilik ? 'Pemilik: '.$item->nama_pemilik : null,
                    ])->filter()->implode(' • '),
                    'description' => Str::limit(collect([
                        $item->sektor_umkm,
                        $item->alamat,
                        $status ? 'Status: '.$status : null,
                    ])->filter()->implode(' • '), 140),
                    'url' => route('guest.umkm', ['q' => $item->nama_ukm]),
                    'action_label' => 'Lihat Direktori',
                    'secondary_url' => $item->no_hp ? 'https://wa.me/'.preg_replace('/\D+/', '', $item->no_hp) : null,
                    'secondary_label' => $item->no_hp ? 'WhatsApp' : null,
                ];
            })
            ->toArray();
    }

    private function paginateGuestCollection(
        Collection $items,
        Request $request,
        int $perPage = 10,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);
        $query = $request->query();

        unset($query[$pageName]);

        return (new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
            ]
        ))->appends($query);
    }

    private function guestPopularKeywords(): array
    {
        return [
            ['label' => 'Cek KTP', 'query' => 'cek ktp'],
            ['label' => 'Izin Usaha', 'query' => 'izin usaha'],
            ['label' => 'Surat Domisili', 'query' => 'surat domisili'],
            ['label' => 'UMKM', 'query' => 'umkm'],
            ['label' => 'Wisata', 'query' => 'wisata'],
        ];
    }

    private function guestShortcutLinks(): array
    {
        return [
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'person_search',
                'title' => 'Cek Data Kependudukan',
                'subtitle' => 'Pencarian berbasis NIK warga',
                'description' => 'Gunakan halaman ini untuk cek data KTP, NIK, KK, dan validasi identitas warga.',
                'url' => route('guest.cek-data'),
                'action_label' => 'Buka Cek Data',
                'keywords' => ['cek data', 'cek ktp', 'ktp', 'nik', 'kk', 'kependudukan', 'penduduk', 'warga'],
            ],
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'description',
                'title' => 'Surat Online & Persyaratan',
                'subtitle' => 'Layanan domisili, usaha, dan administrasi',
                'description' => 'Cari persyaratan resmi surat domisili, surat usaha, dan layanan administrasi kelurahan.',
                'url' => route('guest.administrasi'),
                'action_label' => 'Buka Surat Online',
                'keywords' => ['surat', 'surat online', 'surat domisili', 'izin usaha', 'pelayanan', 'persyaratan'],
            ],
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'newspaper',
                'title' => 'Publikasi & Dokumen Publik',
                'subtitle' => 'Berita, pengumuman, formulir, dan regulasi',
                'description' => 'Temukan berita, pengumuman, formulir, dan dokumen resmi yang dipublikasikan kelurahan.',
                'url' => route('guest.publikasi'),
                'action_label' => 'Lihat Publikasi',
                'keywords' => ['publikasi', 'berita', 'pengumuman', 'dokumen', 'formulir', 'regulasi'],
            ],

            [
                'category' => 'Navigasi Cepat',
                'icon' => 'storefront',
                'title' => 'Direktori UMKM Kelurahan',
                'subtitle' => 'Cari usaha lokal berdasarkan nama dan sektor',
                'description' => 'Temukan UMKM warga, jenis usaha, sektor ekonomi, dan kontak pemilik usaha lokal.',
                'url' => route('guest.umkm'),
                'action_label' => 'Buka Direktori',
                'keywords' => ['umkm', 'usaha', 'dagang', 'warung', 'toko', 'izin usaha', 'wirausaha'],
            ],
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'place',
                'title' => 'Pariwisata & Rekomendasi Lokal',
                'subtitle' => 'Wisata, kuliner, budaya, dan ruang terbuka',
                'description' => 'Jelajahi destinasi lokal, kuliner, budaya, ruang terbuka, dan rekomendasi publik lainnya.',
                'url' => route('guest.parawisata'),
                'action_label' => 'Lihat Pariwisata',
                'keywords' => ['wisata', 'kuliner', 'budaya', 'ruang terbuka', 'pariwisata', 'tempat'],
            ],
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'call',
                'title' => 'Kontak dan Jam Layanan',
                'subtitle' => 'Alamat kantor, telepon, email, dan jam buka',
                'description' => 'Lihat informasi kontak resmi, alamat kelurahan, email, telepon, dan jam layanan.',
                'url' => route('guest.kontak'),
                'action_label' => 'Lihat Kontak',
                'keywords' => ['kontak', 'alamat', 'telepon', 'email', 'jam layanan', 'hubungi'],
            ],
            [
                'category' => 'Navigasi Cepat',
                'icon' => 'campaign',
                'title' => 'Pengaduan Warga',
                'subtitle' => 'Sampaikan keluhan dan aspirasi',
                'description' => 'Gunakan kanal pengaduan untuk laporan fasilitas umum, keamanan, atau layanan warga.',
                'url' => route('guest.pengaduan'),
                'action_label' => 'Buka Pengaduan',
                'keywords' => ['pengaduan', 'lapor', 'laporan', 'keluhan', 'aspirasi', 'aduan'],
            ],
        ];
    }

    private function guestKelurahanGeojsonCollection(): array
    {
        $collection = $this->emptyGuestFeatureCollection('kelurahan');
        $kelLayer = PetaLayer::where('slug', PetaLayer::LAYER_BATAS_KELURAHAN)->first();

        if (! $kelLayer) {
            return $collection;
        }

        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');
        $rows = DB::select(
            "SELECT plp.id, plp.nama, plp.kelurahan_id, {$geojsonSelect}
             FROM peta_layer_polygons plp
             WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
             ORDER BY plp.id",
            [$kelLayer->id]
        );

        if (empty($rows)) {
            return $collection;
        }

        $collection['features'] = collect($rows)
            ->map(fn ($row) => [
                'type' => 'Feature',
                'properties' => [
                    'id' => $row->kelurahan_id ?? $row->id,
                    'nama' => $row->nama,
                ],
                'geometry' => json_decode($row->geojson, true),
            ])
            ->values()
            ->all();

        return $collection;
    }

    private function guestRwGeojsonCollection(): array
    {
        $collection = $this->emptyGuestFeatureCollection('rw');
        $rwLayer = PetaLayer::where('slug', PetaLayer::LAYER_WILAYAH_RW)->first();

        if (! $rwLayer) {
            return $collection;
        }

        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');
        $rows = DB::select(
            "SELECT plp.id, plp.nama, plp.warna, plp.rw_id, {$geojsonSelect}
             FROM peta_layer_polygons plp
             WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
             ORDER BY plp.nama",
            [$rwLayer->id]
        );

        if (empty($rows)) {
            return $collection;
        }

        $rwStats = $this->guestRwMapStats();

        $collection['features'] = collect($rows)
            ->map(function ($row, int $index) use ($rwStats) {
                $stats = $rwStats[(int) ($row->rw_id ?? 0)] ?? [];
                $label = $stats['label'] ?? $row->nama ?? 'RW';

                return [
                    'type' => 'Feature',
                    'properties' => array_merge(
                        [
                            'id' => $index + 1,
                            'label' => $label,
                            'rw' => $label,
                            'nomor' => $stats['nomor'] ?? null,
                            'warna' => $row->warna ?: '#E4121B',
                            'polygon_id' => $row->id,
                            'rw_id' => $row->rw_id,
                        ],
                        $stats
                    ),
                    'geometry' => json_decode($row->geojson, true),
                ];
            })
            ->values()
            ->all();

        return $collection;
    }

    private function guestRwMapStats(): array
    {
        return Rw::with([
            'rts',
            'pengurus' => function ($query) {
                $query->whereHas('jabatan', function (Builder $jabatanQuery) {
                    $jabatanQuery->where('nama', 'Ketua RW');
                })->with('penduduk');
            },
        ])
            ->orderBy('nomor')
            ->get()
            ->mapWithKeys(function (Rw $rw) {
                $rtIds = $rw->rts->pluck('id')->all();
                $label = 'RW '.str_pad((string) $rw->nomor, 2, '0', STR_PAD_LEFT);
                $ketua = $rw->pengurus->first()?->penduduk?->nama ?? '-';
                $luasArea = $rw->luas_area
                    ? number_format((float) $rw->luas_area, 2, ',', '.').' m²'
                    : '-';

                return [
                    $rw->id => [
                        'nomor' => (int) $rw->nomor,
                        'label' => $label,
                        'total_penduduk' => Penduduk::whereIn('rt_id', $rtIds)->count(),
                        'total_kk' => Keluarga::whereIn('rt_id', $rtIds)->count(),
                        'total_rt' => count($rtIds),
                        'total_umkm' => Umkm::whereIn('rt_id', $rtIds)->count(),
                        'laki_laki' => Penduduk::whereIn('rt_id', $rtIds)->whereIn('jenis_kelamin', ['L', 'Laki-laki'])->count(),
                        'perempuan' => Penduduk::whereIn('rt_id', $rtIds)->whereIn('jenis_kelamin', ['P', 'Perempuan'])->count(),
                        'profil_rw' => [
                            'foto' => $rw->foto,
                            'luas_area' => $luasArea,
                            'no_telp' => $rw->no_telp,
                            'alamat_sekretariat' => $rw->alamat_sekretariat,
                            'deskripsi' => $rw->deskripsi,
                            'ketua' => $ketua,
                        ],
                    ],
                ];
            })
            ->all();
    }

    private function emptyGuestFeatureCollection(string $name): array
    {
        return [
            'type' => 'FeatureCollection',
            'name' => $name,
            'crs' => [
                'type' => 'name',
                'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'],
            ],
            'features' => [],
        ];
    }

    private function guestActiveMapLayers(): array
    {
        return PetaLayer::active()
            ->ordered()
            ->get()
            ->map(function (PetaLayer $layer) {
                $layerType = match ($layer->slug) {
                    PetaLayer::LAYER_BATAS_KELURAHAN => 'kelurahan',
                    PetaLayer::LAYER_WILAYAH_RW => 'rw',
                    default => 'custom',
                };

                $geojson = match ($layerType) {
                    'kelurahan' => $this->guestKelurahanGeojsonCollection(),
                    'rw' => $this->guestRwGeojsonCollection(),
                    default => $layer->jenis === PetaLayer::JENIS_POINT
                        ? $this->guestPointLayerCollection($layer)
                        : $this->guestLayerFeatureCollection($layer),
                };

                return [
                    'id' => $layer->id,
                    'nama' => $layer->nama,
                    'slug' => $layer->slug,
                    'deskripsi' => $layer->deskripsi,
                    'jenis' => $layer->jenis,
                    'warna' => $layer->warna,
                    'fill_opacity' => $layer->fill_opacity,
                    'stroke_width' => $layer->stroke_width,
                    'pattern_type' => $layer->pattern_type,
                    'sort_order' => $layer->sort_order,
                    'layer_type' => $layerType,
                    'feature_count' => count($geojson['features'] ?? []),
                    'geojson' => $geojson,
                ];
            })
            ->values()
            ->all();
    }

    private function guestLayerFeatureCollection(PetaLayer $layer): array
    {
        $collection = $this->emptyGuestFeatureCollection($layer->slug);
        $geojsonSelect = PetaLayerPolygon::geojsonSelectExpression('plp.polygon');
        $rows = DB::select(
            "SELECT plp.id, plp.nama, plp.deskripsi, plp.warna, {$geojsonSelect}
             FROM peta_layer_polygons plp
             WHERE plp.peta_layer_id = ? AND plp.polygon IS NOT NULL
             ORDER BY plp.sort_order, plp.id",
            [$layer->id]
        );

        if (empty($rows)) {
            return $collection;
        }

        $collection['features'] = collect($rows)
            ->map(fn ($row) => [
                'type' => 'Feature',
                'properties' => [
                    'id' => $row->id,
                    'nama' => $row->nama,
                    'deskripsi' => $row->deskripsi,
                    'warna' => $row->warna ?: $layer->warna,
                ],
                'geometry' => json_decode($row->geojson, true),
            ])
            ->values()
            ->all();

        return $collection;
    }

    private function guestPointLayerCollection(PetaLayer $layer): array
    {
        $collection = $this->emptyGuestFeatureCollection($layer->slug);

        $features = match ($layer->slug) {
            PetaLayer::LAYER_SEKOLAH => Sekolah::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama_sekolah')
                ->get()
                ->map(fn (Sekolah $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama_sekolah,
                    description: collect([$item->jenjang, $item->status, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Sekolah',
                        'jenjang' => $item->jenjang,
                        'status' => $item->status,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            PetaLayer::LAYER_FASKES => Faskes::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama_rs')
                ->get()
                ->map(fn (Faskes $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama_rs,
                    description: collect([$item->jenis, $item->kelas, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Fasilitas Kesehatan',
                        'jenis' => $item->jenis,
                        'kelas' => $item->kelas,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            PetaLayer::LAYER_TEMPAT_IBADAH => TempatIbadah::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama')
                ->get()
                ->map(fn (TempatIbadah $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama,
                    description: collect([$item->tempat_ibadah, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Tempat Ibadah',
                        'jenis' => $item->tempat_ibadah,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            PetaLayer::LAYER_KONTRAKAN_KOST => Kontrakan::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama')
                ->get()
                ->map(fn (Kontrakan $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama ?: 'Kontrakan & Kost',
                    description: collect([$item->jenis_unit, $item->pemilik, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Kontrakan & Kost',
                        'jenis' => $item->jenis_unit,
                        'pemilik' => $item->pemilik,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            PetaLayer::LAYER_ASRAMA => Asrama::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama')
                ->get()
                ->map(fn (Asrama $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama,
                    description: collect([$item->jenis, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Asrama',
                        'jenis' => $item->jenis,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            PetaLayer::LAYER_DATA_USAHA => Umkm::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama_ukm')
                ->get()
                ->map(fn (Umkm $item) => $this->guestPointFeature(
                    layer: $layer,
                    id: $item->id,
                    name: $item->nama_ukm ?: 'UMKM Warga',
                    description: collect([$item->nama_pemilik, $item->sektor_umkm, $item->alamat])->filter()->implode(' • '),
                    latitude: $item->latitude,
                    longitude: $item->longitude,
                    extraProperties: [
                        'kategori' => 'Data Usaha',
                        'pemilik' => $item->nama_pemilik,
                        'sektor' => $item->sektor_umkm,
                        'alamat' => $item->alamat,
                    ],
                ))
                ->filter()
                ->values()
                ->all(),

            default => [],
        };

        $collection['features'] = $features;

        return $collection;
    }

    private function guestPointFeature(
        PetaLayer $layer,
        int|string $id,
        string $name,
        ?string $description,
        mixed $latitude,
        mixed $longitude,
        array $extraProperties = []
    ): ?array {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        return [
            'type' => 'Feature',
            'properties' => array_merge(
                [
                    'id' => $id,
                    'nama' => $name,
                    'deskripsi' => $description,
                    'warna' => $layer->warna ?: '#E4121B',
                    'layer_slug' => $layer->slug,
                    'layer_name' => $layer->nama,
                ],
                $extraProperties
            ),
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [(float) $longitude, (float) $latitude],
            ],
        ];
    }

    private function guestAgeRanges(): array
    {
        return [
            [
                'label' => '0-5',
                'title' => 'Balita',
                'min' => 0,
                'max' => 5,
            ],
            [
                'label' => '6-12',
                'title' => 'Anak',
                'min' => 6,
                'max' => 12,
            ],
            [
                'label' => '13-17',
                'title' => 'Remaja',
                'min' => 13,
                'max' => 17,
            ],
            [
                'label' => '18-35',
                'title' => 'Dewasa Awal',
                'min' => 18,
                'max' => 35,
            ],
            [
                'label' => '36-59',
                'title' => 'Dewasa',
                'min' => 36,
                'max' => 59,
            ],
            [
                'label' => '60+',
                'title' => 'Lansia',
                'min' => 60,
                'max' => null,
            ],
        ];
    }

    private function parsePendudukBirthDateFromNik(?string $nik): ?Carbon
    {
        $nik = preg_replace('/\D+/', '', (string) $nik);

        if (strlen($nik) < 12) {
            return null;
        }

        $tanggalSegment = substr($nik, 6, 6);
        $hari = (int) substr($tanggalSegment, 0, 2);
        $bulan = (int) substr($tanggalSegment, 2, 2);
        $tahun = (int) substr($tanggalSegment, 4, 2);

        if ($hari > 40) {
            $hari -= 40;
        }

        $tahunPenuh = $tahun > (int) now()->format('y')
            ? 1900 + $tahun
            : 2000 + $tahun;

        if (! checkdate($bulan, $hari, $tahunPenuh)) {
            return null;
        }

        return Carbon::create($tahunPenuh, $bulan, $hari)->startOfDay();
    }

    private function guestSearchScopes(): array
    {
        return [
            [
                'icon' => 'description',
                'title' => 'Layanan Surat',
                'description' => 'Persyaratan surat domisili, usaha, dan layanan administrasi warga.',
            ],
            [
                'icon' => 'article',
                'title' => 'Berita & Publikasi',
                'description' => 'Berita, pengumuman, dokumen publik, formulir, dan regulasi kelurahan.',
            ],
            [
                'icon' => 'place',
                'title' => 'Pariwisata Lokal',
                'description' => 'Destinasi, kuliner, budaya, ruang terbuka, dan rekomendasi kelurahan.',
            ],
            [
                'icon' => 'storefront',
                'title' => 'Direktori UMKM',
                'description' => 'Pencarian usaha lokal berdasarkan nama usaha, pemilik, sektor, dan lokasi.',
            ],
        ];
    }

    private function formatPendudukText(?string $value): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : '-';
    }

    private function formatPendudukJenisKelamin(?string $value): string
    {
        $normalized = Str::upper(trim((string) $value));

        return match ($normalized) {
            'L', 'LAKI-LAKI', 'LAKI LAKI' => 'Laki-laki',
            'P', 'PEREMPUAN' => 'Perempuan',
            default => $this->formatPendudukText($value),
        };
    }

    private function formatPendudukStatus(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '-';
        }

        return Str::headline(str_replace('_', ' ', $value));
    }

    private function maskSensitiveNumber(?string $value, int $visiblePrefix = 6, int $visibleSuffix = 4): string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return '-';
        }

        $length = strlen($value);

        if ($length <= ($visiblePrefix + $visibleSuffix)) {
            return str_repeat('*', max(0, $length - 2)).substr($value, -2);
        }

        return substr($value, 0, $visiblePrefix)
            .str_repeat('*', $length - $visiblePrefix - $visibleSuffix)
            .substr($value, -$visibleSuffix);
    }

    private function maskSensitiveWords(?string $value, int $visiblePrefix = 1): string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return '-';
        }

        return preg_replace_callback('/[\pL\pN]+/u', function (array $matches) use ($visiblePrefix) {
            $token = $matches[0];
            $length = mb_strlen($token);

            if ($length <= $visiblePrefix) {
                return str_repeat('*', $length);
            }

            return mb_substr($token, 0, $visiblePrefix).str_repeat('*', $length - $visiblePrefix);
        }, $value) ?? $value;
    }
}

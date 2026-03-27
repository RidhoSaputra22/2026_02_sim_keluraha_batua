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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
        $results = collect();

        if ($search !== '') {
            $results = collect(array_merge(
                $this->searchGuestQuickLinks($search),
                $this->searchGuestLayananSurat($search, 4),
                $this->searchGuestBerita($search, 4),
                $this->searchGuestDokumenPublik($search, 4),
                $this->searchGuestDestinasiWisata($search, 4),
                $this->searchGuestUmkm($search, 4),
            ));
        }

        $resultGroups = $results->groupBy('category');
        $summaryCards = $resultGroups->map(function ($items, $category) {
            return [
                'title' => $category,
                'count' => $items->count(),
                'icon' => $items->first()['icon'] ?? 'search',
            ];
        })->values();

        return view('guest.search', [
            'search' => $search,
            'results' => $results,
            'resultGroups' => $resultGroups,
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

    private function applyGuestLike(Builder $query, array $columns, string $search): void
    {
        $words = array_values(array_filter(preg_split('/\s+/', trim($search))));

        $query->where(function (Builder $outer) use ($columns, $search, $words) {
            foreach ($columns as $column) {
                $outer->orWhere($column, 'like', "%{$search}%");
            }

            if (count($words) > 1) {
                foreach ($columns as $column) {
                    $outer->orWhere(function (Builder $inner) use ($column, $words) {
                        foreach ($words as $word) {
                            $inner->where($column, 'like', "%{$word}%");
                        }
                    });
                }
            }
        });
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

    private function searchGuestLayananSurat(string $search, int $limit): array
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

        return $query->limit($limit)
            ->get()
            ->map(function (LayananSurat $item) {
                $subtitle = collect([
                    $item->estimasi_layanan ? 'Estimasi ' . $item->estimasi_layanan : null,
                    $item->biaya ? 'Biaya ' . $item->biaya : null,
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
                    'url' => route('guest.surat-online', [
                        'q' => $item->nama,
                        'layanan' => $item->slug,
                    ]),
                    'action_label' => 'Lihat Persyaratan',
                ];
            })
            ->toArray();
    }

    private function searchGuestBerita(string $search, int $limit): array
    {
        $query = Berita::latestPublished();
        $this->applyGuestLike($query, ['judul', 'ringkasan', 'isi', 'kategori'], $search);

        return $query->limit($limit)
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

    private function searchGuestDokumenPublik(string $search, int $limit): array
    {
        $query = DokumenPublik::latestPublished();
        $this->applyGuestLike($query, ['judul', 'deskripsi', 'kategori'], $search);

        return $query->limit($limit)
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
                    'url' => route('guest.publikasi', [
                        'q' => $item->judul,
                        'dokumen_kategori' => $item->kategori,
                    ]),
                    'action_label' => 'Buka Publikasi',
                    'secondary_url' => route('guest.publikasi.download', $item),
                    'secondary_label' => 'Unduh',
                ];
            })
            ->toArray();
    }

    private function searchGuestDestinasiWisata(string $search, int $limit): array
    {
        $query = DestinasiWisata::published()->ordered();
        $this->applyGuestLike($query, ['nama', 'ringkasan', 'deskripsi', 'alamat', 'kategori'], $search);

        return $query->limit($limit)
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

    private function searchGuestUmkm(string $search, int $limit): array
    {
        $query = Umkm::query()
            ->with(['jenisUsaha', 'rt.rw'])
            ->latest('id');

        $this->applyGuestLike(
            $query,
            ['nama_ukm', 'nama_pemilik', 'alamat', 'sektor_umkm', 'nik_pemilik', 'status'],
            $search
        );

        return $query->limit($limit)
            ->get()
            ->map(function (Umkm $item) {
                $status = $item->status ? ucfirst(str_replace('_', ' ', $item->status)) : null;

                return [
                    'category' => 'UMKM',
                    'icon' => 'storefront',
                    'title' => $item->nama_ukm ?: 'Usaha Warga',
                    'subtitle' => collect([
                        $item->jenisUsaha?->nama,
                        $item->nama_pemilik ? 'Pemilik: ' . $item->nama_pemilik : null,
                    ])->filter()->implode(' • '),
                    'description' => Str::limit(collect([
                        $item->sektor_umkm,
                        $item->alamat,
                        $status ? 'Status: ' . $status : null,
                    ])->filter()->implode(' • '), 140),
                    'url' => route('guest.umkm', ['q' => $item->nama_ukm]),
                    'action_label' => 'Lihat Direktori',
                    'secondary_url' => $item->no_hp ? 'https://wa.me/' . preg_replace('/\D+/', '', $item->no_hp) : null,
                    'secondary_label' => $item->no_hp ? 'WhatsApp' : null,
                ];
            })
            ->toArray();
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
                'url' => route('guest.surat-online'),
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
}

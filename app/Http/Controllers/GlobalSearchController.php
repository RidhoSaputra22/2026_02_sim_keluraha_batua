<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasWilayahScope;
use App\Models\Faskes;
use App\Models\Keluarga;
use App\Models\Kendaraan;
use App\Models\PegawaiStaff;
use App\Models\Penduduk;
use App\Models\PetugasKebersihan;
use App\Models\Role;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    use HasWilayahScope;

    /**
     * Handle global search request based on user role.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $query = $request->input('q');
        $user = $request->user();
        $role = $user->getRoleName();
        $results = [];
        $limit = 5; // max results per category

        // Admin gets access to everything — no wilayah scoping
        if ($user->isAdmin()) {
            $results = array_merge(
                $this->searchPenduduk($query, $limit),
                $this->searchKeluarga($query, $limit),
                $this->searchUsers($query, $limit),
                $this->searchPegawai($query, $limit),
                $this->searchUsaha($query, $limit),
                $this->searchFaskes($query, $limit),
                $this->searchSekolah($query, $limit),
                $this->searchTempatIbadah($query, $limit),
                $this->searchKendaraan($query, $limit),
                $this->searchPetugasKebersihan($query, $limit),
            );
        } elseif ($role === Role::RT_RW) {
            // RT/RW gets wilayah-scoped results for citizen data,
            // and unscoped results for kelurahan-level data they can access.
            $results = array_merge(
                $this->searchPenduduk($query, $limit, scoped: true),
                $this->searchKeluarga($query, $limit, scoped: true),
                $this->searchUsaha($query, $limit, scoped: true),
                $this->searchFaskes($query, $limit),
                $this->searchTempatIbadah($query, $limit),
                $this->searchSekolah($query, $limit),
                $this->searchKendaraan($query, $limit),
                $this->searchPetugasKebersihan($query, $limit),
            );
        }
        // Other roles (operator, verifikator, penandatangan) currently have
        // no dedicated search scope — return empty until those modules are built.

        return response()->json([
            'results' => $results,
            'total' => count($results),
        ]);
    }

    // ─── Search Methods ────────────────────────────────────────

    /**
     * Apply multi-word LIKE conditions to a query for the given columns.
     *
     * Single word  → standard `col LIKE '%word%'` OR across columns.
     * Multi-word   → also tries all words as AND-conditions per column so that
     *                "H Daeng" matches "H. Daeng Mattola" (phrase OR token match).
     */
    private function applyLike(Builder $q, array $columns, string $search): void
    {
        $words = array_values(array_filter(preg_split('/\s+/', trim($search))));

        $q->where(function (Builder $outer) use ($columns, $search, $words) {
            // 1) Full-phrase match on any column
            foreach ($columns as $col) {
                $outer->orWhere($col, 'like', "%{$search}%");
            }

            // 2) All-words-present match per column (handles abbreviations / punctuation)
            if (count($words) > 1) {
                foreach ($columns as $col) {
                    $outer->orWhere(function (Builder $inner) use ($col, $words) {
                        foreach ($words as $word) {
                            $inner->where($col, 'like', "%{$word}%");
                        }
                    });
                }
            }
        });
    }

    /**
     * @param  bool  $scoped  When true, applies wilayah RT/RW scoping via HasWilayahScope.
     */
    private function searchPenduduk(string $query, int $limit, bool $scoped = false): array
    {
        $q = Penduduk::query();
        $this->applyLike($q, ['nik', 'nama', 'alamat'], $query);

        if ($scoped) {
            $this->applyWilayahScope($q);
        }

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Penduduk',
                'icon' => 'users',
                'title' => $item->nama,
                'subtitle' => "NIK: {$item->nik}",
                'url' => route('kependudukan.penduduk.show', $item->id),
            ])
            ->toArray();
    }

    /**
     * @param  bool  $scoped  When true, applies wilayah RT/RW scoping via HasWilayahScope.
     */
    private function searchKeluarga(string $query, int $limit, bool $scoped = false): array
    {
        $q = Keluarga::query()
            ->where(function (Builder $outer) use ($query) {
                $words = array_values(array_filter(preg_split('/\s+/', trim($query))));

                // no_kk exact phrase
                $outer->orWhere('no_kk', 'like', "%{$query}%");

                // kepala keluarga nama — phrase match
                $outer->orWhereHas('kepalaKeluarga', fn ($r) => $r->where('nama', 'like', "%{$query}%"));

                // kepala keluarga nama — all-words match
                if (count($words) > 1) {
                    $outer->orWhereHas('kepalaKeluarga', function ($r) use ($words) {
                        foreach ($words as $word) {
                            $r->where('nama', 'like', "%{$word}%");
                        }
                    });
                }
            });

        if ($scoped) {
            $this->applyWilayahScope($q);
        }

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Keluarga',
                'icon' => 'home',
                'title' => "KK: {$item->no_kk}",
                'subtitle' => $item->kepalaKeluarga?->nama ?? '-',
                'url' => route('kependudukan.keluarga.show', $item->id),
            ])
            ->toArray();
    }

    private function searchUsers(string $query, int $limit): array
    {
        $q = User::query();
        $this->applyLike($q, ['name', 'email', 'nip'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Pengguna',
                'icon' => 'user-circle',
                'title' => $item->name,
                'subtitle' => $item->email,
                'url' => url("/admin/users/{$item->id}/edit"),
            ])
            ->toArray();
    }

    private function searchPegawai(string $query, int $limit): array
    {
        $q = PegawaiStaff::query();
        $this->applyLike($q, ['nama', 'nip', 'jabatan'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Pegawai',
                'icon' => 'briefcase',
                'title' => $item->nama,
                'subtitle' => $item->jabatan ?? ($item->nip ?? '-'),
                'url' => url("/master/pegawai/{$item->id}/edit"),
            ])
            ->toArray();
    }

    /**
     * @param  bool  $scoped  When true, applies wilayah RT/RW scoping (Umkm has rt_id).
     */
    private function searchUsaha(string $query, int $limit, bool $scoped = false): array
    {
        $q = Umkm::query();
        $this->applyLike($q, ['nama_ukm', 'nama_pemilik', 'nik_pemilik', 'alamat', 'sektor_umkm'], $query);

        if ($scoped) {
            $this->applyWilayahScope($q);
        }

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Usaha',
                'icon' => 'building-storefront',
                'title' => $item->nama_ukm ?? 'Usaha',
                'subtitle' => $item->nama_pemilik ?? '-',
                'url' => route('usaha.index'),
            ])
            ->toArray();
    }

    private function searchFaskes(string $query, int $limit): array
    {
        $q = Faskes::query();
        $this->applyLike($q, ['nama_rs', 'alamat', 'jenis'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Faskes',
                'icon' => 'heart',
                'title' => $item->nama_rs,
                'subtitle' => $item->alamat ?? '-',
                'url' => route('data-umum.faskes.index'),
            ])
            ->toArray();
    }

    private function searchSekolah(string $query, int $limit): array
    {
        $q = Sekolah::query();
        $this->applyLike($q, ['nama_sekolah', 'alamat', 'npsn'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Sekolah',
                'icon' => 'academic-cap',
                'title' => $item->nama_sekolah,
                'subtitle' => $item->alamat ?? '-',
                'url' => route('data-umum.sekolah.index'),
            ])
            ->toArray();
    }

    private function searchTempatIbadah(string $query, int $limit): array
    {
        $q = TempatIbadah::query();
        $this->applyLike($q, ['nama', 'alamat'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Tempat Ibadah',
                'icon' => 'star',
                'title' => $item->nama,
                'subtitle' => $item->alamat ?? '-',
                'url' => route('data-umum.tempat-ibadah.index'),
            ])
            ->toArray();
    }

    private function searchKendaraan(string $query, int $limit): array
    {
        $q = Kendaraan::query();
        $this->applyLike($q, ['no_polisi', 'jenis_barang', 'merek_type', 'nama_pengemudi'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Kendaraan',
                'icon' => 'truck',
                'title' => $item->merek_type ?? $item->jenis_barang ?? 'Kendaraan',
                'subtitle' => $item->no_polisi ?? '-',
                'url' => route('data-umum.kendaraan.index'),
            ])
            ->toArray();
    }

    private function searchPetugasKebersihan(string $query, int $limit): array
    {
        $q = PetugasKebersihan::query();
        $this->applyLike($q, ['nama', 'nik', 'lokasi', 'unit_kerja'], $query);

        return $q->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'category' => 'Petugas Kebersihan',
                'icon' => 'sparkles',
                'title' => $item->nama,
                'subtitle' => $item->lokasi ?? '-',
                'url' => route('data-umum.petugas-kebersihan.index'),
            ])
            ->toArray();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AgendaKegiatan;
use App\Models\Faskes;
use App\Models\Keluarga;
use App\Models\Kendaraan;
use App\Models\PegawaiStaff;
use App\Models\Penandatanganan;
use App\Models\Penduduk;
use App\Models\PetugasKebersihan;
use App\Models\Role;
use App\Models\Sekolah;
use App\Models\Surat;
use App\Models\TempatIbadah;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
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

        // Admin gets access to everything
        if ($user->isAdmin()) {
            $results = array_merge(
                $this->searchPenduduk($query, $limit),
                $this->searchKeluarga($query, $limit),
                $this->searchSurat($query, $limit),
                $this->searchUsers($query, $limit),
                $this->searchPegawai($query, $limit),
                $this->searchPenandatangan($query, $limit),
                $this->searchUsaha($query, $limit),
                $this->searchFaskes($query, $limit),
                $this->searchSekolah($query, $limit),
                $this->searchTempatIbadah($query, $limit),
                $this->searchKendaraan($query, $limit),
                $this->searchPetugasKebersihan($query, $limit),
                $this->searchAgenda($query, $limit),
            );
        } else {
            // RT/RW role gets scoped results
            $results = match ($role) {
                Role::RT_RW => array_merge(
                    $this->searchPenduduk($query, $limit),
                    $this->searchKeluarga($query, $limit),
                    $this->searchSurat($query, $limit, 'pengantar'),
                ),
                default => [],
            };
        }

        return response()->json([
            'results' => $results,
            'total' => count($results),
        ]);
    }

    // ─── Search Methods ────────────────────────────────────────

    private function searchPenduduk(string $query, int $limit): array
    {
        return Penduduk::where('nik', 'like', "%{$query}%")
            ->orWhere('nama', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Penduduk',
                'icon' => 'users',
                'title' => $item->nama,
                'subtitle' => "NIK: {$item->nik}",
                'url' => route('kependudukan.penduduk.show', $item->id),
            ])
            ->toArray();
    }

    private function searchKeluarga(string $query, int $limit): array
    {
        return Keluarga::where('no_kk', 'like', "%{$query}%")
            ->orWhereHas('kepalaKeluarga', fn($q) => $q->where('nama', 'like', "%{$query}%"))
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Keluarga',
                'icon' => 'home',
                'title' => "KK: {$item->no_kk}",
                'subtitle' => $item->kepalaKeluarga?->nama ?? '-',
                'url' => route('kependudukan.keluarga.show', $item->id),
            ])
            ->toArray();
    }

    private function searchSurat(string $query, int $limit, ?string $context = null): array
    {
        $surats = Surat::where('nomor_surat', 'like', "%{$query}%")
            ->orWhere('perihal', 'like', "%{$query}%")
            ->orWhere('nama_dalam_surat', 'like', "%{$query}%")
            ->orWhere('uraian', 'like', "%{$query}%")
            ->limit($limit)
            ->get();

        $routeName = match ($context) {
            'pengantar' => 'rtrw.pengantar.index',
            default => 'rtrw.pengantar.index', // TODO: update when persuratan routes are implemented
        };

        return $surats->map(fn($item) => [
            'category' => 'Surat',
            'icon' => 'document-text',
            'title' => $item->perihal ?? 'Surat',
            'subtitle' => $item->nomor_surat ?? 'Belum ada nomor',
            'url' => route($routeName),
        ])->toArray();
    }

    private function searchUsers(string $query, int $limit): array
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('nip', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Pengguna',
                'icon' => 'user-circle',
                'title' => $item->name,
                'subtitle' => $item->email,
                'url' => route('admin.users.edit', $item->id),
            ])
            ->toArray();
    }

    private function searchPegawai(string $query, int $limit): array
    {
        return PegawaiStaff::where('nama', 'like', "%{$query}%")
            ->orWhere('nip', 'like', "%{$query}%")
            ->orWhere('jabatan', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Pegawai',
                'icon' => 'briefcase',
                'title' => $item->nama,
                'subtitle' => $item->jabatan ?? ($item->nip ?? '-'),
                'url' => route('master.pegawai.edit', $item->id),
            ])
            ->toArray();
    }

    private function searchPenandatangan(string $query, int $limit): array
    {
        return Penandatanganan::whereHas(
            'pegawai',
            fn($q) => $q
                ->where('nama', 'like', "%{$query}%")
                ->orWhere('nip', 'like', "%{$query}%")
                ->orWhere('jabatan', 'like', "%{$query}%")
        )
            ->with('pegawai')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Penandatangan',
                'icon' => 'pencil',
                'title' => $item->pegawai?->nama ?? 'Penandatangan',
                'subtitle' => $item->pegawai?->jabatan ?? '-',
                'url' => route('master.penandatangan.edit', $item->id),
            ])
            ->toArray();
    }

    private function searchUsaha(string $query, int $limit): array
    {
        return Umkm::where('nama_ukm', 'like', "%{$query}%")
            ->orWhere('nama_pemilik', 'like', "%{$query}%")
            ->orWhere('nik_pemilik', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->orWhere('sektor_umkm', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
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
        return Faskes::where('nama_rs', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->orWhere('jenis', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
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
        return Sekolah::where('nama_sekolah', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->orWhere('npsn', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
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
        return TempatIbadah::where('nama', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
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
        return Kendaraan::where('no_polisi', 'like', "%{$query}%")
            ->orWhere('jenis_barang', 'like', "%{$query}%")
            ->orWhere('merek_type', 'like', "%{$query}%")
            ->orWhere('nama_pengemudi', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
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
        return PetugasKebersihan::where('nama', 'like', "%{$query}%")
            ->orWhere('nik', 'like', "%{$query}%")
            ->orWhere('lokasi', 'like', "%{$query}%")
            ->orWhere('unit_kerja', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Petugas Kebersihan',
                'icon' => 'sparkles',
                'title' => $item->nama,
                'subtitle' => $item->lokasi ?? '-',
                'url' => route('data-umum.petugas-kebersihan.index'),
            ])
            ->toArray();
    }

    private function searchAgenda(string $query, int $limit): array
    {
        return AgendaKegiatan::where('perihal', 'like', "%{$query}%")
            ->orWhere('lokasi', 'like', "%{$query}%")
            ->orWhere('penanggung_jawab', 'like', "%{$query}%")
            ->orWhere('keterangan', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'category' => 'Agenda',
                'icon' => 'calendar',
                'title' => $item->perihal ?? 'Agenda',
                'subtitle' => $item->lokasi ?? '-',
                'url' => route('agenda.index'),
            ])
            ->toArray();
    }
}

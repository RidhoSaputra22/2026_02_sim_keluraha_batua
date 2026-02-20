<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trait HasWilayahScope
 *
 * Digunakan pada controller yang melayani lebih dari satu role,
 * untuk membatasi data penduduk & keluarga sesuai wilayah RT/RW
 * milik user yang sedang login.
 *
 * Cara pakai: tambahkan `use HasWilayahScope;` di dalam class controller.
 */
trait HasWilayahScope
{
    /**
     * Kembalikan array RT IDs yang menjadi wilayah user yang sedang login.
     *
     * - Jika user adalah Ketua RW  → semua RT di bawah RW tersebut.
     * - Jika user adalah Ketua RT  → hanya RT yang bersangkutan.
     * - Jika wilayah_rw tidak di-set → array kosong (akses ditolak sepenuhnya).
     */
    protected function wilayahRtIds(): array
    {
        $user = auth()->user();
        $rwNomor = $user->wilayah_rw ? (int) $user->wilayah_rw : null;
        $rtNomor = $user->wilayah_rt ? (int) $user->wilayah_rt : null;

        if (! $rwNomor) {
            return [];
        }

        $rw = Rw::where('nomor', $rwNomor)->first();
        if (! $rw) {
            return [];
        }

        if (! $rtNomor) {
            // Ketua RW: akses semua RT dalam RW-nya
            return Rt::where('rw_id', $rw->id)->pluck('id')->toArray();
        }

        // Ketua RT: hanya RT miliknya
        $rt = Rt::where('rw_id', $rw->id)->where('nomor', $rtNomor)->first();

        return $rt ? [$rt->id] : [];
    }

    /**
     * Apakah user yang sedang login memiliki role rt_rw?
     */
    protected function isRtRw(): bool
    {
        return auth()->user()->hasRole(Role::RT_RW);
    }

    /**
     * Kembalikan koleksi RT yang boleh dipilih oleh user saat ini.
     *
     * RT/RW → dibatasi wilayahnya.
     * Admin/Operator → semua RT.
     */
    protected function wilayahRtList(): Collection
    {
        if ($this->isRtRw()) {
            return Rt::with('rw')
                ->whereIn('id', $this->wilayahRtIds())
                ->orderBy('nomor')
                ->get();
        }

        return Rt::with('rw')->orderBy('rw_id')->orderBy('nomor')->get();
    }

    /**
     * Kembalikan koleksi RW untuk dropdown filter.
     *
     * RT/RW → tidak memerlukan filter RW (collection kosong).
     * Admin/Operator → semua RW.
     */
    protected function wilayahRwList(): Collection
    {
        if ($this->isRtRw()) {
            return new Collection;
        }

        return Rw::orderBy('nomor')->get();
    }

    /**
     * Kembalikan aturan validasi untuk field rt_id.
     *
     * RT/RW → hanya boleh pilih RT dalam wilayahnya.
     * Lainnya → bebas pilih RT mana saja.
     *
     * @return array<int, string>
     */
    protected function rtIdRules(): array
    {
        $rules = ['nullable', 'exists:rts,id'];

        if ($this->isRtRw()) {
            $allowed = $this->wilayahRtIds();
            $rules[] = 'in:'.implode(',', $allowed ?: [0]);
        }

        return $rules;
    }

    /**
     * Pastikan Penduduk berada di wilayah RT/RW user yang login.
     * Tidak melakukan apa-apa bila user bukan RT/RW.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403)
     */
    protected function authorizeWilayah(Penduduk $penduduk): void
    {
        if ($this->isRtRw() && ! in_array($penduduk->rt_id, $this->wilayahRtIds())) {
            abort(403, 'Penduduk tidak berada di wilayah Anda.');
        }
    }

    /**
     * Terapkan scope wilayah pada Eloquent query Builder (untuk Penduduk).
     * Jika bukan RT/RW, query tidak diubah.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyWilayahScope($query)
    {
        if ($this->isRtRw()) {
            $query->whereIn('rt_id', $this->wilayahRtIds());
        }

        return $query;
    }

    /**
     * Terapkan scope wilayah melalui relasi Eloquent (misal: via 'penduduk').
     * Berguna untuk model yang tidak punya kolom rt_id langsung.
     *
     * Contoh: applyWilayahScopeViaRelation($query, 'penduduk')
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $relation  Nama relasi yang memiliki kolom rt_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyWilayahScopeViaRelation($query, string $relation)
    {
        if ($this->isRtRw()) {
            $rtIds = $this->wilayahRtIds();
            $query->whereHas($relation, fn (Builder $q) => $q->whereIn('rt_id', $rtIds));
        }

        return $query;
    }

    /**
     * Kembalikan koleksi Penduduk yang tersedia untuk dropdown.
     *
     * RT/RW → dibatasi hanya penduduk di wilayahnya.
     * Admin/Operator → semua penduduk.
     */
    protected function wilayahPendudukList(): Collection
    {
        if ($this->isRtRw()) {
            return Penduduk::whereIn('rt_id', $this->wilayahRtIds())
                ->orderBy('nama')
                ->get();
        }

        return Penduduk::orderBy('nama')->get();
    }

    /**
     * Pastikan rt_id tertentu berada dalam wilayah RT/RW user yang login.
     * Berguna untuk model dengan kolom rt_id (Keluarga, Kelahiran, dsb).
     * Tidak melakukan apa-apa bila user bukan RT/RW atau rt_id bernilai null.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403)
     */
    protected function authorizeWilayahByRtId(?int $rtId, string $message = 'Data tidak berada di wilayah Anda.'): void
    {
        if ($this->isRtRw() && $rtId !== null && ! in_array($rtId, $this->wilayahRtIds())) {
            abort(403, $message);
        }
    }
}

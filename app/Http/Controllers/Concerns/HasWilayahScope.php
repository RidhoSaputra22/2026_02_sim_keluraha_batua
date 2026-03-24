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
            return Rw::whereIn('id', $this->wilayahRwIds())
                ->orderBy('nomor')
                ->get();
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
    protected function rtIdRules(bool $required = false): array
    {
        $rules = [$required ? 'required' : 'nullable', 'exists:rts,id'];

        if ($this->isRtRw()) {
            $allowed = $this->wilayahRtIds();
            $rules[] = 'in:'.implode(',', $allowed ?: [0]);
        }

        return $rules;
    }

    /**
     * Kembalikan aturan validasi untuk field rw_id.
     *
     * RT/RW → hanya boleh pilih RW dalam wilayahnya.
     * Lainnya → bebas pilih RW mana saja.
     *
     * @return array<int, string>
     */
    protected function rwIdRules(bool $required = false): array
    {
        $rules = [$required ? 'required' : 'nullable', 'exists:rws,id'];

        if ($this->isRtRw()) {
            $allowed = $this->wilayahRwIds();
            $rules[] = 'in:' . implode(',', $allowed ?: [0]);
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
     * Kembalikan array RW IDs yang menjadi wilayah user yang sedang login.
     *
     * RT/RW → hanya RW miliknya.
     * Non-RT/RW → array kosong (tidak difilter).
     */
    protected function wilayahRwIds(): array
    {
        $user    = auth()->user();
        $rwNomor = $user->wilayah_rw ? (int) $user->wilayah_rw : null;

        if (! $rwNomor) {
            return [];
        }

        $rw = Rw::where('nomor', $rwNomor)->first();

        return $rw ? [$rw->id] : [];
    }

    /**
     * Terapkan scope wilayah berdasarkan kolom rw_id.
     * Berguna untuk model yang memiliki rw_id langsung (Faskes, TempatIbadah, dll).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyWilayahScopeByRw($query)
    {
        if ($this->isRtRw()) {
            $rwIds = $this->wilayahRwIds();
            $query->whereIn('rw_id', $rwIds);
        }

        return $query;
    }

    /**
     * Terapkan scope wilayah untuk model yang dapat berada di level RT atau RW.
     *
     * RT/RW akan melihat:
     * - data dengan rt_id di wilayah RT miliknya
     * - data level RW (rt_id null) di wilayah RW miliknya
     */
    protected function applyWilayahScopeByRtOrRw($query)
    {
        if ($this->isRtRw()) {
            $rtIds = $this->wilayahRtIds();
            $rwIds = $this->wilayahRwIds();

            $query->where(function (Builder $scopedQuery) use ($rtIds, $rwIds) {
                $hasConstraint = false;

                if (! empty($rtIds)) {
                    $scopedQuery->whereIn('rt_id', $rtIds);
                    $hasConstraint = true;
                }

                if (! empty($rwIds)) {
                    if ($hasConstraint) {
                        $scopedQuery->orWhere(function (Builder $rwQuery) use ($rwIds) {
                            $rwQuery->whereNull('rt_id')
                                ->whereIn('rw_id', $rwIds);
                        });
                    } else {
                        $scopedQuery->whereNull('rt_id')
                            ->whereIn('rw_id', $rwIds);
                    }

                    $hasConstraint = true;
                }

                if (! $hasConstraint) {
                    $scopedQuery->whereRaw('1 = 0');
                }
            });
        }

        return $query;
    }

    /**
     * Pastikan rw_id tertentu berada dalam wilayah RT/RW user yang login.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403)
     */
    protected function authorizeWilayahByRwId(?int $rwId, string $message = 'Data tidak berada di wilayah Anda.'): void
    {
        if ($this->isRtRw() && ($rwId === null || ! in_array($rwId, $this->wilayahRwIds()))) {
            abort(403, $message);
        }
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

    /**
     * Pastikan data dengan kombinasi rt_id/rw_id berada dalam wilayah RT/RW user.
     */
    protected function authorizeWilayahByRtOrRwId(?int $rtId, ?int $rwId, string $message = 'Data tidak berada di wilayah Anda.'): void
    {
        if (! $this->isRtRw()) {
            return;
        }

        if ($rtId !== null) {
            $this->authorizeWilayahByRtId($rtId, $message);

            return;
        }

        $this->authorizeWilayahByRwId($rwId, $message);
    }

    /**
     * Sinkronkan rw_id dari rt_id, dan isi default wilayah milik user RT/RW saat rw_id kosong.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function normalizeWilayahInput(array $validated, string $rtKey = 'rt_id', string $rwKey = 'rw_id'): array
    {
        $rtId = $validated[$rtKey] ?? null;
        $rwId = $validated[$rwKey] ?? null;

        if ($rtId !== null) {
            $rwId = Rt::whereKey($rtId)->value('rw_id') ?? $rwId;
        } elseif ($this->isRtRw()) {
            $rwId = $rwId ?? ($this->wilayahRwIds()[0] ?? null);
        }

        if (array_key_exists($rwKey, $validated) || $rwId !== null) {
            $validated[$rwKey] = $rwId;
        }

        return $validated;
    }
}

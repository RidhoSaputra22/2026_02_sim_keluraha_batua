<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Trait untuk sinkronisasi data latitude/longitude ke tabel peta_layer_polygons.
 *
 * Controller yang menggunakan trait ini harus menyediakan:
 * - petaLayerSlug(): string  → slug layer di tabel peta_layers
 * - petaPolygonNama(Model): string → nama polygon untuk ditampilkan di peta
 */
trait SyncsWithPetaLayer
{
    /**
     * Slug layer di peta_layers, override di controller.
     */
    abstract protected function petaLayerSlug(): string;

    /**
     * Nama yang ditampilkan untuk polygon di peta.
     */
    abstract protected function petaPolygonNama(Model $model): string;

    /**
     * Simpan/update point di peta_layer_polygons berdasarkan lat/long.
     * Dipanggil setelah store() atau update().
     */
    protected function syncPetaPolygon(Model $model, mixed $latitude = null, mixed $longitude = null): void
    {
        $latitude = $this->normalizeCoordinate($latitude ?? $model->getAttribute('latitude'));
        $longitude = $this->normalizeCoordinate($longitude ?? $model->getAttribute('longitude'));

        // Jika lat/long kosong → hapus polygon yang terkait
        if (is_null($latitude) || is_null($longitude)) {
            $this->removePetaPolygon($model);
            return;
        }

        // Pastikan nilai canonical (7 desimal) juga tersimpan di model utama
        if ($model->getAttribute('latitude') !== $latitude || $model->getAttribute('longitude') !== $longitude) {
            $model->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }

        $layer = PetaLayer::where('slug', $this->petaLayerSlug())->first();

        if (! $layer) {
            return; // Layer belum dikonfigurasi, skip
        }

        $driver = DB::getDriverName();
        $geojson = json_encode([
            'type'        => 'Point',
            'coordinates' => [(float) $longitude, (float) $latitude], // GeoJSON: [lng, lat]
        ]);

        if ($model->peta_layer_polygon_id) {
            // Update existing polygon
            $polygon = PetaLayerPolygon::find($model->peta_layer_polygon_id);

            if ($polygon) {
                $polygon->update([
                    'nama'       => $this->petaPolygonNama($model),
                    'properties' => $this->petaPolygonProperties($model),
                ]);

                if ($driver === 'pgsql') {
                    DB::statement(
                        'UPDATE peta_layer_polygons
                         SET polygon = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                         WHERE id = ?',
                        [$longitude, $latitude, $polygon->id]
                    );
                } else {
                    $polygon->update(['polygon' => $geojson]);
                }

                return;
            }
        }

        // Buat polygon baru
        $polygon = PetaLayerPolygon::create([
            'peta_layer_id' => $layer->id,
            'nama'          => $this->petaPolygonNama($model),
            'properties'    => $this->petaPolygonProperties($model),
        ]);

        if ($driver === 'pgsql') {
            DB::statement(
                'UPDATE peta_layer_polygons
                 SET polygon = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                 WHERE id = ?',
                [$longitude, $latitude, $polygon->id]
            );
        } else {
            DB::table('peta_layer_polygons')
                ->where('id', $polygon->id)
                ->update(['polygon' => $geojson]);
        }

        // Link back ke model
        $model->update(['peta_layer_polygon_id' => $polygon->id]);
    }

    /**
     * Hapus polygon terkait saat data dihapus atau lat/long dikosongkan.
     */
    protected function removePetaPolygon(Model $model): void
    {
        if ($model->peta_layer_polygon_id) {
            PetaLayerPolygon::where('id', $model->peta_layer_polygon_id)->delete();
            $model->update(['peta_layer_polygon_id' => null]);
        }
    }

    /**
     * Properties tambahan yang disimpan di polygon (override jika perlu).
     */
    protected function petaPolygonProperties(Model $model): array
    {
        return [
            'model_type' => get_class($model),
            'model_id'   => $model->id,
        ];
    }

    /**
     * Normalisasi koordinat agar konsisten dengan skala kolom decimal(10,7).
     */
    protected function normalizeCoordinate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 7, '.', '');
    }
}

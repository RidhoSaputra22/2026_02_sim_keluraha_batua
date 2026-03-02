<?php

namespace Database\Seeders;

use App\Models\PetaLayer;
use Illuminate\Database\Seeder;

class PetaLayerFasilitasSeeder extends Seeder
{
    /**
     * Seed layer peta untuk setiap jenis fasilitas.
     *
     * Layer ini digunakan untuk mengelompokkan polygon/titik
     * dari data Sekolah, Faskes, Tempat Ibadah, Kontrakan, Asrama, dan UMKM.
     */
    public function run(): void
    {
        foreach (PetaLayer::facilityLayers() as $slug => $config) {
            PetaLayer::updateOrCreate(
                ['slug' => $slug],
                [
                    'nama'         => $config['nama'],
                    'deskripsi'    => 'Layer peta untuk data ' . $config['nama'],
                    'warna'        => $config['warna'],
                    'fill_opacity' => 0.3,
                    'stroke_width' => 2.0,
                    'pattern_type' => 'solid',
                    'is_active'    => true,
                    'sort_order'   => $config['sort'],
                ]
            );
        }

        $this->command->info('✓ 6 layer peta fasilitas berhasil di-seed.');
    }
}

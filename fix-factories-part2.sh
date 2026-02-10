#!/bin/bash

# Fix remaining FK and text field issues

cd "/home/codeslayer/Desktop/PROJECT LARAVEL/2026_02_10_sim_kelurahan_batua/2026_02_sim_keluraha_batua/database/factories"

# Set NIK fields to null for FK safety (except DataPendudukFactory and PenandatangananFactory and PegawaiStaffFactory)
for file in DataWargaMiskinFactory.php DesaWismaPkkFactory.php KtpTercetakFactory.php PetugasKebersihanFactory.php RekapPenilaianRtRwFactory.php PenilaianRtRwFactory.php; do
    sed -i "s/'nik' => \$this->faker->unique()->numerify('################')/'nik' => null/g" "$file"
done

# Set no_kk to null for FK safety
sed -i "s/'no_kk' => \$this->faker->unique().numerify('################')/'no_kk' => null/g" DesaWismaPkkFactory.php

# Fix nama fields - words to name()
find . -name "*Factory.php" -exec sed -i "s/'nama' => \$this->faker->words(3, true)/'nama' => \$this->faker->name()/g" {} \;

# Fix kelurahan - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'kelurahan' => \$this->faker->words(3, true)/'kelurahan' => \$this->faker->randomElement(['Batua', 'Bangkala', 'Tamangapa', 'Antang'])/g" {} \;

# Fix kecamatan - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'kecamatan' => \$this->faker->words(3, true)/'kecamatan' => \$this->faker->randomElement(['Manggala', 'Panakkukang', 'Tamalate'])/g" {} \;

# Fix agama - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'agama' => \$this->faker->words(3, true)/'agama' => \$this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])/g" {} \;

# Fix status_kawin - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'status_kawin' => \$this->faker->words(3, true)/'status_kawin' => \$this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])/g" {} \;

# Fix pendidikan - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'pendidikan' => \$this->faker->words(3, true)/'pendidikan' => \$this->faker->randomElement(['Tidak\/Belum Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'])/g" {} \;

# Fix status_data - words to realistic value
find . -name "*Factory.php" -exec sed -i "s/'status_data' => \$this->faker->words(3, true)/'status_data' => \$this->faker->randomElement(['aktif', 'pindah', 'meninggal'])/g" {} \;

# Fix tempat_lahir - words to city
find . -name "*Factory.php" -exec sed -i "s/'tempat_lahir' => \$this->faker->words(3, true)/'tempat_lahir' => \$this->faker->city()/g" {} \;

# Fix specific fields in specific factories
sed -i "s/'no_peserta' => \$this->faker->words(3, true)/'no_peserta' => \$this->faker->numerify('##########')/g" DataWargaMiskinFactory.php
sed -i "s/'jabatan' => \$this->faker->words(3, true)/'jabatan' => \$this->faker->randomElement(['Ketua', 'Sekretaris', 'Anggota'])/g" RekapPenilaianRtRwFactory.php PetugasKebersihanFactory.php
sed -i "s/'unit_kerja' => \$this->faker->words(3, true)/'unit_kerja' => \$this->faker->randomElement(['Kebersihan Umum', 'Taman', 'Drainase'])/g" PetugasKebersihanFactory.php
sed -i "s/'pekerjaan' => \$this->faker->words(3, true)/'pekerjaan' => \$this->faker->randomElement(['Petugas Kebersihan', 'Koordinator', 'Supervisor'])/g" PetugasKebersihanFactory.php
sed -i "s/'lokasi' => \$this->faker->words(3, true)/'lokasi' => \$this->faker->randomElement(['RW 001', 'RW 002', 'RW 003', 'Pasar'])/g" PetugasKebersihanFactory.php
sed -i "s/'status' => \$this->faker->words(3, true)/'status' => \$this->faker->randomElement(['Aktif', 'Nonaktif', 'Cuti'])/g" PetugasKebersihanFactory.php

# Fix surat factories
sed -i "s/'no_surat' => \$this->faker->words(3, true)/'no_surat' => \$this->faker->numerify('###\/KEL-BTU\/##\/####')/g" *.php
sed -i "s/'jenis_surat' => \$this->faker->words(3, true)/'jenis_surat' => \$this->faker->randomElement(['SKTM', 'Domisili', 'Usaha', 'Kelahiran'])/g" *.php
sed -i "s/'sifat_surat' => \$this->faker->words(3, true)/'sifat_surat' => \$this->faker->randomElement(['Biasa', 'Segera', 'Sangat Segera'])/g" *.php
sed -i "s/'asal_surat' => \$this->faker->words(3, true)/'asal_surat' => \$this->faker->randomElement(['Kecamatan', 'Dinas', 'Internal', 'Warga'])/g" *.php
sed -i "s/'tujuan_surat' => \$this->faker->words(3, true)/'tujuan_surat' => \$this->faker->company()/g" *.php
sed -i "s/'perihal' => \$this->faker->words(3, true)/'perihal' => \$this->faker->sentence(5)/g" *.php

# Fix sekolah factories
sed -i "s/'npsn' => \$this->faker->words(3, true)/'npsn' => \$this->faker->numerify('########')/g" DetailSekolahFactory.php
sed -i "s/'nama_sekolah' => \$this->faker->words(3, true)/'nama_sekolah' => 'SD Negeri ' . \$this->faker->numberBetween(1, 100)/g" DetailSekolahFactory.php DataSekolahFactory.php
sed -i "s/'tahun_ajar' => \$this->faker->words(3, true)/'tahun_ajar' => \$this->faker->numberBetween(2020, 2026) . '\/' . (\$this->faker->numberBetween(2020, 2026) + 1)/g" DetailSekolahFactory.php
sed -i "s/'jenjang' => \$this->faker->words(3, true)/'jenjang' => \$this->faker->randomElement(['SD', 'SMP', 'SMA', 'SMK'])/g" DataSekolahFactory.php
sed -i "s/'status' => \$this->faker->words(3, true)/'status' => \$this->faker->randomElement(['Negeri', 'Swasta'])/g" DataSekolahFactory.php

# Fix tempat ibadah
sed -i "s/'tempat_ibadah' => \$this->faker->words(3, true)/'tempat_ibadah' => \$this->faker->randomElement(['Masjid', 'Musholla', 'Gereja', 'Pura'])/g" TempatIbadahFactory.php
sed -i "s/'pengurus' => \$this->faker->words(3, true)/'pengurus' => \$this->faker->name()/g" TempatIbadahFactory.php

# Fix layanan publik
sed -i "s/'nama_layanan_publik' => \$this->faker->words(3, true)/'nama_layanan_publik' => \$this->faker->randomElement(['Pelayanan Surat', 'Pelayanan KTP', 'Pelayanan KK'])/g" *.php
sed -i "s/'nama_pengguna_layanan' => \$this->faker->words(3, true)/'nama_pengguna_layanan' => \$this->faker->name()/g" *.php

# Fix IMB
sed -i "s/'nama_pemohon' => \$this->faker->words(3, true)/'nama_pemohon' => \$this->faker->name()/g" RekapImbFactory.php
sed -i "s/'status_luas_tanah' => \$this->faker->words(3, true)/'status_luas_tanah' => \$this->faker->randomElement(['Milik Sendiri', 'Sewa', 'Warisan'])/g" RekapImbFactory.php
sed -i "s/'nama_pada_surat' => \$this->faker->words(3, true)/'nama_pada_surat' => \$this->faker->name()/g" RekapImbFactory.php
sed -i "s/'penggunaan_fungsi_gedung' => \$this->faker->words(3, true)/'penggunaan_fungsi_gedung' => \$this->faker->randomElement(['Rumah Tinggal', 'Ruko', 'Kantor', 'Gudang'])/g" RekapImbFactory.php

# Fix agenda/kegiatan
sed -i "s/'hari_tanggal' => \$this->faker->words(3, true)/'hari_tanggal' => \$this->faker->dayOfWeek() . ', ' . \$this->faker->date()/g" HasilKegiatanFactory.php
sed -i "s/'agenda' => \$this->faker->words(3, true)/'agenda' => \$this->faker->sentence(5)/g" HasilKegiatanFactory.php

echo "✅ All factories fixed!"
echo "📝 Fixed: FK fields (nik, nip, no_kk), text fields, realistic values"

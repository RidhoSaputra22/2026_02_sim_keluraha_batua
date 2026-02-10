---
applyTo: '**'
---
1) Konsep Aplikasi (Gambaran Umum)
1.1 Tujuan
Membangun sistem terpusat untuk membantu kelurahan:
    • Mengelola data kependudukan & referensi wilayah (RT/RW)
    • Memproses layanan persuratan cepat, tertib, dan terdokumentasi
    • Mengelola data usaha/PKL/PK5, bantuan, agenda, dan laporan
    • Menyediakan dashboard monitoring dan audit trail untuk akuntabilitas
1.2 Prinsip Desain
    • Single source of truth: data penduduk jadi pusat (NIK sebagai kunci utama)
    • Role-based access control (hak akses per peran)
    • Audit & arsip: semua transaksi tercatat, bisa dilacak, mudah dicetak
    • Template-driven: surat berbasis template + nomor otomatis + QR/Barcode opsional
    • Integrasi siap: desain API untuk integrasi Dukcapil/SIAK (opsional)
2) Ruang Lingkup Modul (Blueprint Fitur)
A. Dashboard & Monitoring
    • Ringkasan jumlah penduduk, KK, mutasi (lahir/meninggal/pindah/datang)
    • Ringkasan layanan surat per jenis & status (draft–verifikasi–ttd–selesai)
    • Grafik layanan per RT/RW/periode
    • Notifikasi: surat menunggu verifikasi/ttd, data tidak lengkap, masa berlaku
B. Data Master
    • Wilayah: RW/RT, alamat/lingkungan
    • Pengguna & Role, hak akses per menu
    • Penandatangan (Lurah/Seklur/Kasi) + jabatan + masa berlaku
    • Referensi: jenis surat, jenis usaha, pekerjaan, pendidikan, agama, status kawin, dll
    • Template surat (docx/html) + mapping field
C. Kependudukan
    • Data penduduk (NIK, biodata, alamat, RT/RW)
    • Data KK (No KK, kepala keluarga, anggota)
    • Mutasi penduduk:
        ◦ Kelahiran, Kematian
        ◦ Datang, Pindah
        ◦ Perubahan data (pekerjaan, pendidikan, status kawin)
    • Import/export (Excel) untuk pendataan
D. Persuratan & Layanan
    • Registrasi permohonan (oleh petugas / warga via loket)
    • Verifikasi berkas & data
    • Penomoran otomatis + cetak + arsip PDF
    • Tracking status layanan
    • Riwayat surat per penduduk/KK
Contoh jenis surat:
    • SKTM, Domisili, Usaha, Pengantar Nikah, Keterangan Kelahiran/Kematian, dll
E. Data Usaha / PK5 / PKL (sesuai tampilan)
    • Pendataan usaha: nama usaha, jenis usaha, alamat, RT/RW, kontak
    • Relasi ke penduduk (pemilik)
    • Cetak surat keterangan usaha / rekomendasi (bila diperlukan)
    • Arsip & laporan rekap per RW/jenis usaha
F. PBB (opsional sesuai menu)
    • Pengecekan data objek pajak (NOP, nama, alamat, status)
    • Catatan layanan terkait PBB
­G. Agenda & Kegiatan
    • Kalender kegiatan kelurahan
    • Notulen/hasil kegiatan
    • Distribusi tugas (opsional)
H. Laporan
    • Laporan kependudukan periodik
    • Laporan layanan surat (per jenis/per RT-RW/per petugas)
    • Laporan data usaha/PK5
    • Export PDF/Excel
I. Survey Kepuasan (opsional)
    • Kuesioner per layanan
    • Skor & komentar
    • Rekap periodik
3) Aktor Sistem (Role) & Hak Akses
    1. Admin Sistem
        ◦ Kelola user, role, master data, template, pengaturan
    2. Operator Kelurahan (Petugas Loket)
        ◦ Input penduduk, input permohonan surat, cetak draft, arsip
    3. Verifikator (Kasi/Seklur)
        ◦ Validasi data/berkas, approve/reject, koreksi
    4. Penandatangan (Lurah/Pejabat)
        ◦ Tanda tangan (digital/manual), finalisasi
    5. RT/RW (opsional)
        ◦ Input/validasi pendataan warganya, pengantar, monitoring
    6. Warga (opsional portal)
        ◦ Ajukan layanan, upload berkas, cek status, unduh hasil (jika dibuka)
4) Use Case Diagram (Narasi Ringkas)
Kelompok use case:
    • Manajemen Data: kelola penduduk, KK, mutasi, data usaha
    • Layanan Persuratan: ajukan–verifikasi–penomoran–ttd–cetak–arsip
    • Administrasi Sistem: user/role, master, template, konfigurasi
    • Pelaporan: rekap, export
    • Audit: log aktivitas
5) Daftar Use Case Terstruktur (dengan ID)
5.1 Administrasi Sistem
UC-ADM-01 Login
    • Aktor: Semua user
    • Alur: input username/password → validasi → masuk dashboard
    • Output: sesi aktif, hak akses terpasang
UC-ADM-02 Kelola Pengguna
    • Aktor: Admin
    • Alur: tambah/edit/nonaktif user → set role → reset password
    • Output: user siap digunakan
UC-ADM-03 Kelola Role & Hak Akses
    • Aktor: Admin
    • Alur: buat role → mapping menu/aksi (view/add/edit/delete/print/approve)
    • Output: matriks izin
UC-ADM-04 Kelola Penandatangan
    • Aktor: Admin
    • Alur: tambah pejabat → jabatan → periode berlaku → tandatangan (opsional)
    • Output: daftar pejabat siap dipilih saat cetak surat
UC-ADM-05 Kelola Template Surat
    • Aktor: Admin
    • Alur: buat jenis surat → upload template → mapping field → uji generate
    • Output: template aktif
5.2 Kependudukan
UC-KPD-01 Tambah/Ubah Data Penduduk
    • Aktor: Operator
    • Prasyarat: NIK belum terdaftar (untuk tambah)
    • Alur utama: input NIK & biodata → validasi → simpan
    • Alternatif: NIK duplikat → tampilkan data lama → opsi update
    • Output: data penduduk tersimpan
UC-KPD-02 Kelola Kartu Keluarga
    • Aktor: Operator
    • Alur: buat KK → pilih kepala keluarga → tambah anggota (relasi penduduk) → simpan
    • Output: struktur KK
UC-KPD-03 Mutasi Datang/Pindah
    • Aktor: Operator, Verifikator (approve)
    • Alur: input detail mutasi → unggah berkas → verifikasi → finalisasi
    • Output: status penduduk berubah, jejak mutasi tercatat
UC-KPD-04 Catat Kelahiran/Kematian
    • Aktor: Operator, Verifikator
    • Output: rekap kelahiran/kematian periodik
5.3 Persuratan & Layanan
UC-SRT-01 Buat Permohonan Surat
    • Aktor: Operator / Warga (jika portal)
    • Prasyarat: penduduk terdaftar (atau dibuat saat itu)
    • Alur: pilih jenis surat → pilih penduduk/KK → isi field → upload berkas → simpan draft
    • Output: permohonan status “Draft/Menunggu Verifikasi”
UC-SRT-02 Verifikasi Permohonan
    • Aktor: Verifikator
    • Alur: buka antrean → cek data & berkas → setujui/tolak + catatan
    • Output: status “Disetujui Verifikator” / “Ditolak” / “Perlu Perbaikan”
UC-SRT-03 Penomoran Surat Otomatis
    • Aktor: Operator (setelah disetujui)
    • Alur: sistem generate nomor sesuai format + urutan + tahun → lock nomor
    • Output: nomor surat resmi
UC-SRT-04 Finalisasi & TTD
    • Aktor: Penandatangan
    • Alur: lihat daftar menunggu ttd → preview → ttd (digital/ceklist manual) → final
    • Output: surat siap cetak, status “Selesai”
UC-SRT-05 Cetak & Arsip Surat
    • Aktor: Operator
    • Alur: generate dokumen dari template → cetak → simpan PDF → arsip
    • Output: dokumen arsip + histori
UC-SRT-06 Tracking Status Layanan
    • Aktor: Operator/Verifikator/Warga
    • Output: timeline proses + siapa memproses + cap waktu
5.4 Data Usaha / PK5 / PKL
UC-USHA-01 Tambah/Ubah Data Usaha (PK5/PKL)
    • Aktor: Operator
    • Alur: input pemilik (penduduk) → isi data usaha (nama, jenis, alamat, RT/RW, telp) → simpan
    • Output: data usaha tersimpan
UC-USHA-02 Cetak Surat Keterangan Usaha
    • Aktor: Operator → Verifikator → Penandatangan
    • Output: surat usaha terbit + arsip
UC-USHA-03 Laporan Rekap Usaha
    • Aktor: Operator/Admin
    • Output: rekap per RW/jenis usaha/periode
5.5 Laporan & Audit
UC-LAP-01 Laporan Kependudukan Periodik
    • Aktor: Admin/Operator
    • Output: PDF/Excel (jumlah penduduk, KK, mutasi)
UC-LAP-02 Laporan Persuratan
    • Aktor: Admin/Operator
    • Output: rekap surat per jenis, per petugas, per RT/RW, SLA waktu proses
UC-AUD-01 Audit Log Aktivitas
    • Aktor: Admin
    • Output: log siapa mengubah apa & kapan (penting untuk akuntabilitas)
6) Struktur Data Inti (Entity Utama)
Minimal tabel/entitas:
    • users, roles, permissions
    • wilayah_rw, wilayah_rt
    • penduduk, kk, kk_anggota
    • mutasi (datang/pindah/ubah), kelahiran, kematian
    • jenis_surat, template_surat, surat, surat_berkas, surat_log_status
    • penandatangan
    • usaha (PK5/PKL), jenis_usaha
    • audit_log
7) Alur Proses Utama (End-to-End)
Layanan surat (ideal):
Registrasi → Verifikasi → Penomoran → TTD → Cetak → Arsip → Laporan
Pendataan usaha/PK5:
Input usaha → validasi → (opsional) terbit surat → arsip → rekap
8) Kebutuhan Non-Fungsional
    • Keamanan: hashing password, session timeout, role permission
    • Audit trail wajib untuk perubahan data inti
    • Backup DB otomatis + export laporan
    • Performa: pagination & search server-side (data besar)
    • Ketersediaan: bisa berjalan di server kantor / cloud
    • Standarisasi nomor surat & format template
9) Rekomendasi Tahapan Implementasi (MVP → Full)
MVP (prioritas tinggi):
    1. Login + Role
    2. Data Penduduk + KK
    3. Persuratan (jenis surat inti) + template + nomor otomatis
    4. Arsip PDF + laporan persuratan sederhana
Tahap 2:
    • Mutasi lengkap, PK5/PKL/usaha, dashboard grafik, export Excel

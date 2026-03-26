<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengaduanWarga extends Model
{
    use HasFactory;

    protected $table = 'pengaduan_wargas';

    protected $fillable = [
        'kode_pengaduan',
        'nama',
        'email',
        'no_hp',
        'kategori',
        'subjek',
        'lokasi',
        'isi_laporan',
        'lampiran',
        'status',
        'catatan_admin',
        'ditindaklanjuti_at',
    ];

    protected $casts = [
        'ditindaklanjuti_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pengaduan) {
            if (blank($pengaduan->kode_pengaduan)) {
                $pengaduan->kode_pengaduan = static::generateKodePengaduan();
            }

            if (blank($pengaduan->status)) {
                $pengaduan->status = 'baru';
            }
        });
    }

    public static function kategoriOptions(): array
    {
        return [
            'infrastruktur' => 'Infrastruktur',
            'keamanan' => 'Keamanan & Ketertiban',
            'kebersihan' => 'Kebersihan Lingkungan',
            'layanan' => 'Layanan Administrasi',
            'sosial' => 'Masalah Sosial',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'baru' => 'Baru',
            'ditinjau' => 'Ditinjau',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
        ];
    }

    public function markAsHandledIfNeeded(): void
    {
        if (in_array($this->status, ['diproses', 'selesai'], true) && blank($this->ditindaklanjuti_at)) {
            $this->forceFill(['ditindaklanjuti_at' => now()])->saveQuietly();
        }
    }

    private static function generateKodePengaduan(): string
    {
        do {
            $kode = 'ADU-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (static::where('kode_pengaduan', $kode)->exists());

        return $kode;
    }
}

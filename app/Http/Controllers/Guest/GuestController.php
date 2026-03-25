<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Faskes;
use App\Models\JenisUsaha;
use App\Models\Keluarga;
use App\Models\Kelurahan;
use App\Models\PegawaiStaff;
use App\Models\Penduduk;
use App\Models\Rw;
use App\Models\Sekolah;
use App\Models\TempatIbadah;
use App\Models\Umkm;

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

        return view('guest.welcome', compact(
            'kelurahan',
            'totalPenduduk',
            'totalKK',
            'totalUmkm',
            'totalRw',
        ));
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

    public function suratOnline()
    {
        return view('guest.surat_online');
    }

    public function publikasi()
    {
        return view('guest.publikasi');
    }

    public function parawisata()
    {
        return view('guest.parawisata');
    }

    public function umkm()
    {
        $umkmList = Umkm::with(['jenisUsaha', 'rt.rw'])->get();
        $jenisUsahaList = JenisUsaha::withCount('umkms')->get();
        $totalUmkm = $umkmList->count();

        return view('guest.umkm', compact('umkmList', 'jenisUsahaList', 'totalUmkm'));
    }

    public function pengaduan()
    {
        return view('guest.pengaduan');
    }

    public function kontak()
    {
        $kelurahan = Kelurahan::first();

        return view('guest.kontak', compact('kelurahan'));
    }
}

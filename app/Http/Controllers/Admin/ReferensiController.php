<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    public function index()
    {
        // Data referensi yang umum digunakan dalam sistem
        $referensiData = [
            [
                'title' => 'Agama',
                'description' => 'Master data agama yang diakui di Indonesia',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'count' => 6,
                'color' => 'primary',
            ],
            [
                'title' => 'Pekerjaan',
                'description' => 'Daftar jenis pekerjaan dan profesi',
                'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'count' => 0,
                'color' => 'secondary',
            ],
            [
                'title' => 'Pendidikan',
                'description' => 'Tingkat pendidikan formal',
                'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                'count' => 9,
                'color' => 'accent',
            ],
            [
                'title' => 'Status Kawin',
                'description' => 'Status perkawinan penduduk',
                'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                'count' => 4,
                'color' => 'info',
            ],
        ];

        return view('admin.referensi.index', compact('referensiData'));
    }
}

<x-layouts.app :title="'Profil Saya'">
    <x-slot:header>
        <x-layouts.page-header title="Profil Saya" description="Kelola informasi akun dan keamanan Anda" />
    </x-slot:header>

    @php
        $role = $user->getRoleName();
        $roleLabel = $user->role?->label ?? '-';
        $isStaff = $role === \App\Models\Role::ADMIN;
        $isRtRwOrWarga = $role === \App\Models\Role::RT_RW;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Profile Card --}}
        <div class="lg:col-span-1">
            <x-ui.card>
                <div class="flex flex-col items-center text-center">
                    <x-ui.avatar :name="$user->name" size="2xl" />
                    <h3 class="mt-4 text-xl font-bold">{{ $user->name }}</h3>
                    <p class="text-sm text-base-content/60">{{ $user->email }}</p>

                    <div class="badge badge-primary badge-lg mt-3">{{ $roleLabel }}</div>

                    <div class="divider"></div>

                    <div class="w-full space-y-3 text-left text-sm">
                        {{-- Status --}}
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60">Status</span>
                            @if ($user->is_active)
                                <span class="badge badge-success badge-sm gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aktif
                                </span>
                            @else
                                <span class="badge badge-error badge-sm">Nonaktif</span>
                            @endif
                        </div>

                        {{-- Phone --}}
                        @if ($user->phone)
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60">Telepon</span>
                                <span>{{ $user->phone }}</span>
                            </div>
                        @endif

                        {{-- NIP (Staff) --}}
                        @if ($isStaff && $user->nip)
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60">NIP</span>
                                <span class="font-mono text-xs">{{ $user->nip }}</span>
                            </div>
                        @endif

                        {{-- NIK (RT/RW, Warga) --}}
                        @if ($isRtRwOrWarga && $user->nik)
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60">NIK</span>
                                <span class="font-mono text-xs">{{ $user->nik }}</span>
                            </div>
                        @endif

                        {{-- Jabatan (Staff) --}}
                        @if ($isStaff && $user->jabatan)
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60">Jabatan</span>
                                <span>{{ $user->jabatan }}</span>
                            </div>
                        @endif

                        {{-- Wilayah (RT/RW) --}}
                        @if ($role === \App\Models\Role::RT_RW)
                            @if ($user->wilayah_rw)
                                <div class="flex items-center justify-between">
                                    <span class="text-base-content/60">RW</span>
                                    <span>{{ $user->wilayah_rw }}</span>
                                </div>
                            @endif
                            @if ($user->wilayah_rt)
                                <div class="flex items-center justify-between">
                                    <span class="text-base-content/60">RT</span>
                                    <span>{{ $user->wilayah_rt }}</span>
                                </div>
                            @endif
                        @endif

                        {{-- Last Login --}}
                        @if ($user->last_login_at)
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60">Login Terakhir</span>
                                <span class="text-xs">{{ $user->last_login_at->diffForHumans() }}</span>
                            </div>
                        @endif

                        {{-- Member Since --}}
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60">Terdaftar</span>
                            <span class="text-xs">{{ $user->created_at?->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Right Column: Edit Forms --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informasi Profil --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold mb-1">Informasi Profil</h3>
                <p class="text-sm text-base-content/60 mb-4">Perbarui informasi akun dan data pribadi Anda.</p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nama --}}
                        <x-ui.input name="name" label="Nama Lengkap" :value="$user->name" :error="$errors->first('name')"
                            :required="true" />

                        {{-- Email --}}
                        <x-ui.input name="email" type="email" label="Email" :value="$user->email" :error="$errors->first('email')"
                            :required="true" />

                        {{-- Telepon --}}
                        <x-ui.input name="phone" label="No. Telepon" placeholder="08xxxxxxxxxx" :value="$user->phone"
                            :error="$errors->first('phone')" />

                        {{-- NIP (Staff only) --}}
                        @if ($isStaff)
                            <x-ui.input name="nip" label="NIP" placeholder="Nomor Induk Pegawai"
                                :value="$user->nip" :error="$errors->first('nip')" />
                        @endif

                        {{-- NIK (RT/RW & Warga) --}}
                        @if ($isRtRwOrWarga)
                            <x-ui.input name="nik" label="NIK" placeholder="Nomor Induk Kependudukan"
                                :value="$user->nik" :error="$errors->first('nik')" />
                        @endif

                        {{-- Jabatan (Staff only) --}}
                        @if ($isStaff)
                            <x-ui.input name="jabatan" label="Jabatan" placeholder="Jabatan di kelurahan"
                                :value="$user->jabatan" :error="$errors->first('jabatan')" />
                        @endif
                    </div>

                    {{-- Read-only fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        {{-- Role (read-only) --}}
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Role</span></label>
                            <input type="text" class="input input-bordered w-full bg-base-200"
                                value="{{ $roleLabel }}" disabled />
                            <label class="label">
                                <span class="label-text-alt text-base-content/50">Role tidak dapat diubah sendiri</span>
                            </label>
                        </div>

                        {{-- Wilayah RT/RW (read-only for RT/RW role) --}}
                        @if ($role === \App\Models\Role::RT_RW)
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Wilayah</span></label>
                                <input type="text" class="input input-bordered w-full bg-base-200"
                                    value="RT {{ $user->wilayah_rt ?? '-' }} / RW {{ $user->wilayah_rw ?? '-' }}"
                                    disabled />
                                <label class="label">
                                    <span class="label-text-alt text-base-content/50">Hubungi admin untuk mengubah
                                        wilayah</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-ui.button type="primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Ubah Password --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold mb-1">Ubah Password</h3>
                <p class="text-sm text-base-content/60 mb-4">Pastikan akun Anda menggunakan password yang kuat dan unik.
                </p>

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Current Password --}}
                        <div class="md:col-span-2">
                            <x-ui.input name="current_password" type="password" label="Password Saat Ini"
                                placeholder="Masukkan password lama" :error="$errors->first('current_password')" :required="true" />
                        </div>

                        {{-- New Password --}}
                        <x-ui.input name="password" type="password" label="Password Baru"
                            placeholder="Minimal 8 karakter" :error="$errors->first('password')" :required="true" />

                        {{-- Confirm Password --}}
                        <x-ui.input name="password_confirmation" type="password" label="Konfirmasi Password Baru"
                            placeholder="Ulangi password baru" :required="true" />
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-ui.button type="warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Ubah Password
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Info Akses Role --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold mb-1">Akses & Hak Anda</h3>
                <p class="text-sm text-base-content/60 mb-4">Ringkasan modul yang dapat Anda akses berdasarkan role.
                </p>

                @php
                    $accessMap = [
                        \App\Models\Role::ADMIN => [
                            ['label' => 'Manajemen Pengguna & Role', 'icon' => 'users'],
                            ['label' => 'Data Master (Wilayah, Template, Referensi)', 'icon' => 'database'],
                            ['label' => 'Kependudukan (Penduduk, Keluarga, Mutasi)', 'icon' => 'id-card'],
                            ['label' => 'Data Usaha / UMKM', 'icon' => 'shop'],
                            ['label' => 'Audit Log & Laporan', 'icon' => 'chart'],
                            ['label' => 'Seluruh Modul Sistem', 'icon' => 'star'],
                        ],
                        \App\Models\Role::RT_RW => [
                            ['label' => 'Data Warga di Wilayah RT/RW', 'icon' => 'users'],
                            ['label' => 'Kependudukan (Penduduk, Keluarga)', 'icon' => 'id-card'],
                            ['label' => 'Data Usaha / UMKM', 'icon' => 'shop'],
                            ['label' => 'Data Umum (Faskes, Sekolah, dll)', 'icon' => 'database'],
                            ['label' => 'Laporan Wilayah', 'icon' => 'chart'],
                        ],
                    ];

                    $currentAccess = $accessMap[$role] ?? [];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($currentAccess as $access)
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200/50">
                            <div
                                class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm">{{ $access['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

        </div>
    </div>
</x-layouts.app>

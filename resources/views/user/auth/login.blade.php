<x-layouts.guest :title="'Login'">
    <x-ui.card>
        <div class=" max-w-7xl ">
            <div class="flex flex-col ">
                <img src="{{ asset('logo.png') }}" alt="Login Illustration" class="w-10 aspect-square mb-6">

                <div>
                    <h2 class="text-2xl font-semibold mb-1">Selamat Datang</h2>
                    <p class="text-sm text-base-content/70 mb-6">Masuk untuk mengelola data dan informasi kelurahan Batua
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-4">
                    <x-ui.input name="email" label="Email" type="email" placeholder="admin@kelurahan.go.id"
                        required />

                    <x-ui.input name="password" label="Password" type="password" placeholder="Masukkan password"
                        required />

                    <div class="flex items-center justify-between">
                        <x-ui.checkbox name="remember" :single="true" :options="[['label' => 'Ingat saya']]" />
                    </div>

                    <x-ui.button type="primary" class="w-full" :isSubmit="true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Masuk
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.card>
</x-layouts.guest>

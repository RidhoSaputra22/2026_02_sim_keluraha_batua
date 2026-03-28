@props(['title' => 'Pusat Data & Informasi Kelurahan'])

<!DOCTYPE html>

<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title }} - Kelurahan Batua Raya</title>

    {{-- Google Fonts (Material Icons loaded via CSS) --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    @vite(['resources/css/guest.css', 'resources/js/app.js', 'resources/js/guest/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body data-guest-layout="true" class="min-h-screen flex flex-col font-sans text-slate-800 bg-white" data-theme="light">

    {{-- NAVIGATION --}}
    <x-guest::layout.navbar />

    <main class="grow">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <x-guest::layout.footer />
</body>

</html>

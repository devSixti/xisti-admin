<!DOCTYPE html>
<html lang="{{ $lang ?? 'es' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('legal.centro_legal')) — {{ $brandName ?? 'ZIMO' }}</title>
    <meta name="description" content="@yield('meta_description', __('legal.hub_intro'))">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/legal-centro.css') }}">
    <style>
        :root {
            --legal-primary: {{ config('legal_centro.primary_color', '#F5C518') }};
            --legal-primary-hover: {{ config('legal_centro.primary_hover', '#DB9E03') }};
        }
    </style>
    @stack('head')
</head>
<body class="legal-centro-body">
@include('legal.partials.header')

<div class="legal-centro-shell">
    <aside class="legal-sidebar d-none d-lg-block">
        @include('legal.partials.sidebar')
    </aside>
    <main class="legal-main">
        @include('legal.partials.toolbar')
        @yield('content')
    </main>
</div>

@include('partials.legal-footer', ['lang' => $lang ?? 'es'])
@include('partials.cookie-banner', ['lang' => $lang ?? 'es'])

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/legal-centro.js') }}"></script>
@stack('scripts')
</body>
</html>

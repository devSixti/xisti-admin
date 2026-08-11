<!DOCTYPE html>
<html lang="{{ $lang ?? 'es' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — XISTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --xisti-primary: #80FF00; --xisti-secondary: #681FFF; }
        .btn-xisti { background: var(--xisti-primary); color: #111; border: none; font-weight: 600; }
        .btn-xisti:hover { background: #6de600; color: #111; }
        .legal-footer { background: #f8f9fa; border-top: 1px solid #dee2e6; }
        a { color: var(--xisti-secondary); }
    </style>
</head>
<body>
@yield('content')
@include('partials.legal-footer', ['lang' => $lang ?? 'es'])
@include('partials.cookie-banner', ['lang' => $lang ?? 'es'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

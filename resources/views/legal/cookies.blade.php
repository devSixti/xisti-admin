@extends('legal.layout')

@section('title', __('legal.cookies'))

@section('content')
<div class="container py-5">
    <h1>{{ __('legal.cookies') }}</h1>
    <p class="mt-3">ZIMO utiliza cookies y tecnologías similares para el funcionamiento del sitio, recordar preferencias y, con tu consentimiento, analítica y marketing.</p>
    <ul>
        <li><strong>Necesarias:</strong> sesión, seguridad, preferencias de consentimiento.</li>
        <li><strong>Analítica:</strong> Google Analytics, Microsoft Clarity (solo si aceptas).</li>
        <li><strong>Marketing:</strong> Meta Pixel, Google Ads, remarketing (solo si aceptas).</li>
    </ul>
    <p>Puedes cambiar tu decisión borrando cookies del navegador o usando el banner al visitar el sitio.</p>
    <p class="mt-4"><a class="btn btn-zimo" href="{{ $centroLegalUrl }}">{{ __('legal.centro_legal') }}</a></p>
</div>
@endsection

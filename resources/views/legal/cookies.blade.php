@extends('legal.layout')

@section('title', __('legal.cookies'))

@section('content')
<div class="container py-5">
    <h1>{{ __('legal.cookies') }}</h1>
    <p class="mt-3">XISTI utiliza cookies y tecnologías similares para el funcionamiento del sitio, recordar preferencias y, con tu consentimiento, analítica y marketing.</p>
    <p>Consulta también nuestra <a href="{{ url('/legal/privacidad') }}?lang={{ $lang }}">{{ __('legal.privacy') }}</a>.</p>
</div>
@endsection

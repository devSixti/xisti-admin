@extends('legal.layout')

@section('title', __('legal.centro_legal'))

@section('content')
<div class="container py-5">
    <h1 class="mb-3">{{ __('legal.centro_legal') }}</h1>
    <p class="text-muted">{{ __('legal.hub_intro') }}</p>
    <div class="list-group mt-4">
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/terminos') }}?lang={{ $lang }}">{{ __('legal.terms') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/privacidad') }}?lang={{ $lang }}">{{ __('legal.privacy') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/cookies') }}?lang={{ $lang }}">{{ __('legal.cookies') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/aviso-legal') }}?lang={{ $lang }}">{{ __('legal.legal_notice') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/faq') }}?lang={{ $lang }}">{{ __('legal.faq') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/contacto') }}?lang={{ $lang }}">{{ __('legal.contact_link') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/seguridad') }}?lang={{ $lang }}">{{ __('legal.security') }}</a>
        <a class="list-group-item list-group-item-action" href="{{ url('/legal/eliminar-cuenta') }}?lang={{ $lang }}">{{ __('legal.delete_account') }}</a>
    </div>
    <div class="mt-4 d-flex gap-2 flex-wrap">
        @foreach (['es','en','pt','fr','it'] as $code)
            <a href="?lang={{ $code }}" class="btn btn-sm btn-outline-secondary">{{ strtoupper($code) }}</a>
        @endforeach
    </div>
</div>
@endsection

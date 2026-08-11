@extends('legal.layout')

@section('title', __('legal.centro_legal'))
@section('meta_description', __('legal.hub_intro'))

@section('content')
<div class="legal-hero">
    <p class="legal-hero-kicker">{{ $brandName }} · {{ $tagline }}</p>
    <h1 class="legal-hero-title">{{ __('legal.centro_legal') }}</h1>
    <p class="legal-hero-lead">{{ __('legal.hub_intro_long') }}</p>
    <div class="legal-hero-badges">
        <span class="legal-meta-pill"><i class="bi bi-shield-check"></i> {{ __('legal.hub_badge_compliance') }}</span>
        <span class="legal-meta-pill"><i class="bi bi-translate"></i> 5 {{ __('legal.hub_badge_langs') }}</span>
        <span class="legal-meta-pill"><i class="bi bi-file-earmark-text"></i> v{{ $consentVersion }}</span>
    </div>
</div>

<div class="row g-4">
    @foreach($navSections as $sectionKey => $section)
        <div class="col-md-6 col-xl-4">
            <div class="legal-card h-100">
                <h2 class="legal-card-title">{{ __($section['label']) }}</h2>
                <ul class="legal-card-list">
                    @foreach($section['items'] as $item)
                        @php
                            $href = match(true) {
                                $item['slug'] === 'cookies' => url('/legal/cookies').'?lang='.$lang,
                                $item['slug'] === 'eliminar-cuenta' => url('/legal/eliminar-cuenta').'?lang='.$lang,
                                default => url('/legal/'.$item['slug']).'?lang='.$lang,
                            };
                        @endphp
                        <li>
                            <a href="{{ $href }}">
                                @if(!empty($item['icon']))<i class="bi {{ $item['icon'] }}"></i>@endif
                                {{ __($item['label_key']) }}
                                <i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>

<div class="legal-contact-strip mt-5">
    <h2 class="h5">{{ __('legal.hub_contact_title') }}</h2>
    <p class="text-muted mb-3">{{ __('legal.hub_contact_intro') }}</p>
    <div class="d-flex flex-wrap gap-2">
        <a class="legal-btn" href="mailto:{{ $legalEmails['support'] ?? 'soporte@zimo.com' }}"><i class="bi bi-envelope"></i> {{ $legalEmails['support'] ?? 'soporte@zimo.com' }}</a>
        <a class="legal-btn-outline btn" href="{{ url('/legal/pqr') }}?lang={{ $lang }}">{{ __('legal.pqr') }}</a>
        <a class="legal-btn-outline btn" href="{{ url('/') }}?lang={{ $lang }}#contact">{{ __('legal.contact_form.title') }}</a>
    </div>
</div>
@endsection

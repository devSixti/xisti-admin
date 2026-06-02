@php
    $settings = $general_settings ?? \App\Models\GeneralSettings::first();
    $logoFile = ($settings && $settings->website_logo) ? $settings->website_logo : null;
@endphp
<div class="xisti-brand-logo {{ $class ?? '' }}">
    @if ($logoFile)
        <img src="{{ asset('assets/images/website-logo-icon/'.$logoFile) }}" alt="{{ config('xisti.product_name', 'XISTI') }}" class="xisti-brand-logo__img">
    @else
        <img src="{{ asset('assets/images/website-logo-icon/xisti-logo.svg') }}" alt="{{ config('xisti.product_name', 'XISTI') }}" class="xisti-brand-logo__img">
    @endif
    @if (!empty($showTagline))
        <span class="xisti-brand-logo__tagline">{{ config('xisti.tagline', 'Fácil y Seguro') }}</span>
    @endif
</div>

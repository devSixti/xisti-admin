@php
    $lang = $lang ?? 'es';
    app()->setLocale($lang);
    $q = '?lang=' . urlencode($lang);
@endphp
<footer class="legal-footer py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <p class="mb-2"><strong>{{ __('legal.copyright') }}</strong></p>
                <p class="small text-muted mb-3">{{ __('legal.footer_disclaimer') }}</p>
                <div class="d-flex flex-wrap gap-3 small">
                    <a href="{{ url('/legal') }}{{ $q }}">{{ __('legal.centro_legal') }}</a>
                    <a href="{{ url('/legal/terminos') }}{{ $q }}">{{ __('legal.terms') }}</a>
                    <a href="{{ url('/legal/privacidad') }}{{ $q }}">{{ __('legal.privacy') }}</a>
                    <a href="{{ url('/legal/tratamiento-datos') }}{{ $q }}">{{ __('legal.data_processing') }}</a>
                    <a href="{{ url('/legal/pqr') }}{{ $q }}">{{ __('legal.pqr') }}</a>
                    <a href="{{ url('/legal/cookies') }}{{ $q }}">{{ __('legal.cookies') }}</a>
                    <a href="{{ url('/legal/faq') }}{{ $q }}">{{ __('legal.faq') }}</a>
                    <a href="{{ url('/legal/eliminar-cuenta') }}{{ $q }}">{{ __('legal.delete_account') }}</a>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                @php
                    $android = \App\Support\LegalConfig::storeLink('android');
                    $ios = \App\Support\LegalConfig::storeLink('ios');
                @endphp
                @if($android)
                    <a href="{{ $android }}" class="btn btn-xisti btn-sm d-block mb-2">{{ __('legal.download_android') }}</a>
                @else
                    <span class="badge bg-secondary">{{ __('legal.download_android') }} — {{ __('legal.coming_soon') }}</span><br>
                @endif
                @if($ios)
                    <a href="{{ $ios }}" class="btn btn-xisti btn-sm d-block mt-2">{{ __('legal.download_ios') }}</a>
                @else
                    <span class="badge bg-secondary mt-2">{{ __('legal.download_ios') }} — {{ __('legal.coming_soon') }}</span>
                @endif
            </div>
        </div>
    </div>
</footer>

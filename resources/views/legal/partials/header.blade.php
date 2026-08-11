<header class="legal-header">
    <div class="container-fluid legal-header-inner">
        <a class="legal-brand" href="{{ url('/legal') }}?lang={{ $lang }}">
            @php $logo = config('legal_centro.logo_path'); @endphp
            @if($logo && file_exists(public_path($logo)))
                <img src="{{ asset($logo) }}" alt="{{ $brandName }}" class="legal-brand-logo">
            @else
                <span class="legal-brand-text">{{ $brandName }}</span>
            @endif
            <span class="legal-brand-badge">{{ __('legal.centro_legal') }}</span>
        </a>
        <div class="legal-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a class="btn btn-sm legal-btn-outline" href="{{ url('/') }}?lang={{ $lang }}">{{ __('legal.nav.home') }}</a>
            <div class="dropdown">
                <button class="btn btn-sm legal-btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ strtoupper($lang) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach($langs as $code)
                        <li><a class="dropdown-item @if($lang === $code) active @endif" href="?lang={{ $code }}">{{ strtoupper($code) }}</a></li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn btn-sm legal-btn d-none d-lg-inline-flex" onclick="window.print()">
                <i class="bi bi-printer"></i> {{ __('legal.print') }}
            </button>
            <button class="btn btn-sm legal-btn-outline d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#legalNavOffcanvas" aria-controls="legalNavOffcanvas">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</header>

<div class="offcanvas offcanvas-start legal-offcanvas" tabindex="-1" id="legalNavOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ __('legal.centro_legal') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        @include('legal.partials.sidebar')
    </div>
</div>

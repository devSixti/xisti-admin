@if(!empty($title) && ($activeSlug ?? '') !== 'hub')
<div class="legal-toolbar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb legal-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ url('/legal') }}?lang={{ $lang }}">{{ __('legal.centro_legal') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>
    <div class="legal-meta d-flex flex-wrap gap-3 align-items-center">
        <span class="legal-meta-pill"><i class="bi bi-tag"></i> v{{ $consentVersion }}</span>
        <span class="legal-meta-pill"><i class="bi bi-calendar3"></i> {{ __('legal.updated') }} {{ $lastUpdated }}</span>
        @if(!empty($entity['name']))
            <span class="legal-meta-pill text-muted"><i class="bi bi-building"></i> {{ $entity['name'] }}</span>
        @endif
    </div>
</div>
@endif

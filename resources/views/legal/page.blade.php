@extends('legal.layout')

@section('title', $title ?? __('legal.centro_legal'))
@section('meta_description', $summary ?? '')

@section('content')
<article class="legal-document">
    <header class="legal-doc-header">
        <h1 class="legal-doc-title">{{ $title }}</h1>
        @if(!empty($summary))
            <p class="legal-doc-summary">{{ $summary }}</p>
        @endif
    </header>

    <div class="legal-doc-layout">
        <aside class="legal-toc d-none d-xl-block" id="legal-toc" aria-label="{{ __('legal.toc') }}">
            <p class="legal-toc-title">{{ __('legal.toc') }}</p>
            <nav id="legal-toc-list"></nav>
        </aside>
        <div class="legal-doc-body">
            {!! $body ?? '' !!}
        </div>
    </div>

    @if(!empty($deletionFlowUrl) && ($activeSlug ?? '') === 'eliminar-cuenta')
        <div class="mt-4">
            <a class="legal-btn" href="{{ $deletionFlowUrl }}">{{ __('legal.delete_account_page.start_flow') }}</a>
        </div>
    @endif

    <footer class="legal-doc-footer">
        <p class="small text-muted mb-0">{{ __('legal.doc_footer', ['version' => $consentVersion, 'date' => $lastUpdated, 'brand' => $brandName]) }}</p>
    </footer>
</article>
@endsection

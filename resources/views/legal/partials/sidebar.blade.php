<nav class="legal-sidebar-nav" aria-label="{{ __('legal.centro_legal') }}">
    <a class="legal-sidebar-home @if(($activeSlug ?? '') === 'hub') active @endif" href="{{ url('/legal') }}?lang={{ $lang }}">
        <i class="bi bi-grid"></i> {{ __('legal.nav.hub') }}
    </a>
    @foreach($navSections as $section)
        <div class="legal-sidebar-section">
            <p class="legal-sidebar-label">{{ __($section['label']) }}</p>
            @foreach($section['items'] as $item)
                @php
                    $href = $item['route'] === 'get:legal:cookies'
                        ? url('/legal/cookies').'?lang='.$lang
                        : ($item['route'] === 'get:legal:delete-account'
                            ? url('/legal/eliminar-cuenta').'?lang='.$lang
                            : ($item['route'] === 'get:legal:doc'
                                ? url('/legal/'.$item['slug']).'?lang='.$lang
                                : url('/legal/'.$item['slug']).'?lang='.$lang));
                    $active = ($activeSlug ?? '') === $item['slug'];
                @endphp
                <a class="legal-sidebar-link @if($active) active @endif" href="{{ $href }}">
                    @if(!empty($item['icon']))<i class="bi {{ $item['icon'] }}"></i>@endif
                    {{ __($item['label_key']) }}
                </a>
            @endforeach
        </div>
    @endforeach
</nav>

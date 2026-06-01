@php
    use App\Helpers\AdminUi;
    $current = app()->getLocale();
    $labels = AdminUi::localeLabels();
@endphp
<li class="header-notification admin-locale-switcher">
    <div class="dropdown-primary dropdown">
        <div class="dropdown-toggle" data-toggle="dropdown" title="{{ AdminUi::label('locale.switch') }}">
            <i class="feather icon-globe"></i>
            <span class="d-none d-md-inline">{{ strtoupper($current) }}</span>
        </div>
        <ul class="show-notification dropdown-menu dropdown-menu-right" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
            @foreach (AdminUi::LOCALES as $code)
                <li>
                    <form method="post" action="{{ route('post:admin:locale') }}" class="m-0">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $code }}">
                        <button type="submit" class="dropdown-item {{ $current === $code ? 'active' : '' }}">
                            {{ $labels[$code] ?? strtoupper($code) }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</li>

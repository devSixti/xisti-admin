@props([
    'title',
    'subtitle' => null,
    'icon' => 'feather icon-layers',
    'iconBg' => 'bg-c-blue',
])

<div class="page-header card">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="{{ $icon }} {{ $iconBg }}"></i>
                <div class="d-inline">
                    <h5>{{ $title }}</h5>
                    @if ($subtitle)
                        <span>{{ $subtitle }}</span>
                    @endif
                </div>
            </div>
        </div>
        @if (isset($actions))
            <div class="col-lg-4 text-right">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>

<div class="text-right p-3">
    @foreach (\App\Helpers\AdminUi::LOCALES as $code)
        <form method="post" action="{{ route('post:admin:locale') }}" class="d-inline">
            @csrf
            <input type="hidden" name="locale" value="{{ $code }}">
            <button type="submit" class="btn btn-sm {{ app()->getLocale() === $code ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ strtoupper($code) }}
            </button>
        </form>
    @endforeach
</div>

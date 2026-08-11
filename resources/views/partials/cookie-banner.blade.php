@php
    $lang = $lang ?? 'es';
    app()->setLocale($lang);
@endphp
<div id="xisti-cookie-banner" class="position-fixed bottom-0 start-0 end-0 bg-white border-top shadow p-3 d-none" style="z-index: 9999;">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-8 small">
                {{ __('legal.cookie.message') }}
                <a href="{{ url('/legal/cookies') }}?lang={{ $lang }}">{{ __('legal.cookie.policy_link') }}</a>
            </div>
            <div class="col-lg-4 d-flex flex-wrap gap-2 justify-content-lg-end">
                <button type="button" class="btn btn-xisti btn-sm" data-cookie-choice="accept">{{ __('legal.cookie.accept') }}</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-cookie-choice="reject">{{ __('legal.cookie.reject') }}</button>
                <a href="{{ url('/legal/cookies') }}?lang={{ $lang }}" class="btn btn-outline-secondary btn-sm">{{ __('legal.cookie.configure') }}</a>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var key = 'xisti_cookie_consent_v1';
    var banner = document.getElementById('xisti-cookie-banner');
    if (!banner) return;
    var stored = localStorage.getItem(key);
    if (!stored) {
        banner.classList.remove('d-none');
    } else if (stored === 'accept') {
        window.xistiAnalyticsAllowed = true;
    }
    banner.querySelectorAll('[data-cookie-choice]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var choice = btn.getAttribute('data-cookie-choice');
            localStorage.setItem(key, choice);
            banner.classList.add('d-none');
            window.xistiAnalyticsAllowed = choice === 'accept';
        });
    });
})();
</script>

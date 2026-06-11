@php
    $gs = request()->get('general_settings');
    $siteName = $gs->website_name ?? 'XISTI';
    $logo = $gs->website_logo
        ? asset('assets/images/website-logo-icon/'.$gs->website_logo)
        : asset('assets/images/website-logo-icon/xisti-logo.png');
    $favicon = $gs->website_favicon
        ? asset('assets/images/website-logo-icon/'.$gs->website_favicon)
        : $logo;
    $playStore = $gs->user_playstore_link ?? '#';
    $appStore = $gs->user_appstore_link ?? '#';
    $address = $gs->address ?? 'Medellín, Colombia';
    $email = $gs->email ?? 'soporte@xistiapp.com';
    $phone = $gs->contact_no ?? '+57 3000000000';
    $copyright = $gs->copy_right ?? '© XISTI APP — Todos los derechos reservados.';
    $instagram = $gs->instagram_link ?? 'https://www.instagram.com/xistiapp/';
    $facebook = $gs->facebook_link ?? 'https://www.facebook.com/';
    $linkedin = $gs->linkedin_link ?? 'https://www.linkedin.com/company/xistiapp/';
    $img = fn (string $path) => asset('assets/front/img/'.$path);
    $storeDir = public_path('assets/front/img/store');
    $storeShots = [];
    $storeLabels = [
        '01-rider-home.png' => 'Home pasajero',
        '02-rider-service-modes.png' => 'Moto y Carro',
        '03-rider-searching.png' => 'Buscando conductor',
        '04-rider-tracking.png' => 'Viaje en curso',
        '05-rider-history.png' => 'Historial',
        '06-driver-home.png' => 'Conductor online',
        '07-driver-request.png' => 'Nueva solicitud',
        '08-driver-detail.png' => 'Detalle del viaje',
        '09-driver-active.png' => 'Viaje activo',
        '10-rider-wallet.png' => 'Billetera',
    ];
    if (is_dir($storeDir)) {
        foreach (glob($storeDir.'/*.png') ?: [] as $file) {
            $storeShots[basename($file)] = asset('assets/front/img/store/'.basename($file));
        }
        ksort($storeShots);
    }
    $heroPhone = ($storeShots['01-rider-home.png'] ?? null) ?: ($storeShots[array_key_first($storeShots) ?? ''] ?? $img('Customer-App-Features.png'));
    $driverPhone = ($storeShots['06-driver-home.png'] ?? null) ?: ($storeShots[array_key_first($storeShots) ?? ''] ?? $img('Driver-App-Features.png'));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="XISTI transforma tu manera de moverte por Medellín. Seguridad, economía y facilidad en cada viaje. Descarga la app en Google Play y App Store.">
    <meta name="theme-color" content="#070707">
    <title>{{ $siteName }} — Fácil y Seguro</title>
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700;800&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/front/css/xisti-homepage.css') }}?v=2.2.0" rel="stylesheet">
</head>
<body class="xisti-home">
<div class="x-shell">

    <header class="x-top" id="x-top">
        <div class="x-top__bar">
            <a class="x-top__logo" href="#inicio" aria-label="{{ $siteName }} inicio">
                <img src="{{ $logo }}" alt="{{ $siteName }}">
            </a>
            <nav class="x-top__nav" id="x-nav" aria-label="Principal">
                <a href="#inicio">Inicio</a>
                <a href="#pilares">Pilares</a>
                <a href="#servicios">Servicios</a>
                <a href="#como">Cómo funciona</a>
                <a href="#app">App</a>
                <a href="#descargar">Descargar</a>
            </nav>
            <div class="x-top__actions">
                <a class="x-top__admin" href="{{ url('/admin/login') }}">Panel admin</a>
                <a class="x-top__cta" href="#descargar">Obtener app</a>
                <button class="x-top__burger" type="button" id="x-burger" aria-expanded="false" aria-controls="x-nav">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="x-hero" id="inicio">
            <div class="x-hero__sky" aria-hidden="true">
                <img src="{{ $img('xisti-hero-medellin.png') }}" alt="">
            </div>
            <div class="x-hero__mesh" aria-hidden="true"></div>
            <div class="x-hero__inner">
                <div class="x-hero__copy x-reveal">
                    <p class="x-kicker"><span class="x-kicker__dot"></span> Medellín · Movilidad urbana</p>
                    <h1 class="x-hero__title">
                        <span class="x-hero__title-line">Muévete con</span>
                        <span class="x-hero__title-brand">{{ $siteName }}</span>
                    </h1>
                    <p class="x-hero__text">
                        Transformamos tu manera de moverte por Medellín: viaje seguro, cómodo y eficiente en cada trayecto.
                        Negocia tu tarifa en pasos de <strong>$500 COP</strong> y conecta con conductores verificados en tiempo real.
                    </p>
                    <div class="x-hero__chips">
                        <span>Moto</span>
                        <span>Carro</span>
                        <span>Encomiendas</span>
                    </div>
                    <div class="x-hero__btns">
                        <a class="x-btn x-btn--lime" href="#descargar">Descargar gratis</a>
                        <a class="x-btn x-btn--line" href="#como">Ver cómo funciona</a>
                    </div>
                </div>
                <div class="x-hero__device x-reveal x-reveal--delay">
                    <div class="x-device">
                        <div class="x-device__ring x-device__ring--1"></div>
                        <div class="x-device__ring x-device__ring--2"></div>
                        <div class="x-device__frame">
                            <img src="{{ $heroPhone }}" alt="App {{ $siteName }} pasajero">
                        </div>
                        <div class="x-device__float x-device__float--fare">
                            <span class="x-device__float-label">Tarifa acordada</span>
                            <strong>$12.500</strong>
                        </div>
                        <div class="x-device__float x-device__float--live">
                            <span class="x-device__float-dot"></span> En vivo
                        </div>
                    </div>
                </div>
            </div>
            <div class="x-metrics x-reveal">
                <article class="x-metric">
                    <strong>8%</strong>
                    <span>Comisión plataforma</span>
                </article>
                <article class="x-metric">
                    <strong>$500</strong>
                    <span>Paso de negociación</span>
                </article>
                <article class="x-metric">
                    <strong>$13.000</strong>
                    <span>Recarga mínima wallet</span>
                </article>
                <article class="x-metric">
                    <strong>SOS</strong>
                    <span>Seguimiento + alerta</span>
                </article>
            </div>
        </section>

        <section class="x-pillars" id="pilares">
            <div class="x-wrap">
                <header class="x-head x-reveal">
                    <p class="x-eyebrow">Quiénes somos</p>
                    <h2>Seguridad, economía y facilidad en cada viaje</h2>
                    <p>Los tres pilares que guían {{ $siteName }} — alineados con la promesa de movilidad urbana en Medellín.</p>
                </header>
                <div class="x-pillars__grid">
                    @foreach([
                        ['Economía', 'Tarifas justas y negociables. Calidad y accesibilidad en cada servicio, con recargas wallet desde $13.000 COP.'],
                        ['Seguridad', 'Monitoreo GPS en tiempo real, verificación de identidad y botón SOS integrado para viajar con tranquilidad.'],
                        ['Facilidad', 'Reserva en segundos, una sola app para pasajero y conductor, y soporte operativo en español.'],
                    ] as [$title, $desc])
                    <article class="x-pillar x-reveal">
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="x-band" id="servicios">
            <div class="x-wrap x-band__grid">
                <div class="x-band__visual x-reveal">
                    <div class="x-band__img-stack">
                        <img class="x-band__img-main" src="{{ $img('about-us-image-1.png') }}" alt="Ciudad {{ $siteName }}">
                        <img class="x-band__img-accent" src="{{ $img('xisti-hero-medellin.png') }}" alt="">
                    </div>
                </div>
                <div class="x-band__text x-reveal">
                    <p class="x-eyebrow">Sobre {{ $siteName }}</p>
                    <h2>Movilidad hecha para la ciudad, no para plantillas</h2>
                    <p>
                        {{ $siteName }} conecta pasajeros y conductores independientes con una experiencia oscura, rápida y clara:
                        solicita viajes o envíos, revisa opciones cercanas y acuerda tarifas antes de confirmar.
                    </p>
                    <p class="x-band__legal">
                        La plataforma facilita la conexión tecnológica. Los servicios se acuerdan entre usuarios y conductores bajo nuestros términos.
                    </p>
                    <ul class="x-checklist">
                        <li>Pasajero y conductor en la misma app</li>
                        <li>Wallet prepago con Wompi</li>
                        <li>OTP, documentos y validación</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="x-flow" id="como">
            <div class="x-wrap">
                <header class="x-head x-reveal">
                    <p class="x-eyebrow">Flujo simple</p>
                    <h2>De la descarga al viaje en seis pasos</h2>
                    <p>Sin fricción. Diseñado para Medellín y su ritmo urbano.</p>
                </header>
                <ol class="x-timeline">
                    @foreach([
                        ['Descarga', 'Google Play o App Store. Cuenta en minutos.'],
                        ['Regístrate', 'Perfil pasajero o conductor verificado.'],
                        ['Solicita', 'Origen, destino y tipo de servicio.'],
                        ['Negocia', 'Propón tarifa y revisa conductores.'],
                        ['Sigue', 'Ubicación en tiempo real en el mapa.'],
                        ['Cierra', 'Paga con wallet y califica el viaje.'],
                    ] as $i => [$title, $desc])
                    <li class="x-timeline__item x-reveal" style="--i: {{ $i }}">
                        <span class="x-timeline__idx">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="x-modes">
            <div class="x-wrap">
                <header class="x-head x-reveal">
                    <p class="x-eyebrow">Dos roles, una plataforma</p>
                    <h2>Pasajero y conductor, mismo ADN</h2>
                </header>
                <div class="x-modes__grid">
                    <article class="x-mode x-mode--rider x-reveal">
                        <div class="x-mode__shot">
                            <img src="{{ $heroPhone }}" alt="Modo pasajero">
                        </div>
                        <div class="x-mode__body">
                            <h3>Modo pasajero</h3>
                            <ul>
                                <li>Tarifa negociable antes de confirmar</li>
                                <li>Envíos urbanos: Moto y Carro</li>
                                <li>Encomiendas activas en lanzamiento</li>
                                <li>Wallet + Wompi desde $13.000</li>
                            </ul>
                        </div>
                    </article>
                    <article class="x-mode x-mode--driver x-reveal x-reveal--delay">
                        <div class="x-mode__shot">
                            <img src="{{ $driverPhone }}" alt="Modo conductor">
                        </div>
                        <div class="x-mode__body">
                            <h3>Modo conductor</h3>
                            <ul>
                                <li>Activa disponibilidad cuando quieras</li>
                                <li>Oferta tu tarifa a cada solicitud</li>
                                <li>Documentación validada en admin</li>
                                <li>Gestión de ganancias en app</li>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="x-features">
            <div class="x-wrap">
                <header class="x-head x-reveal">
                    <p class="x-eyebrow">Por qué {{ $siteName }}</p>
                    <h2>Todo lo esencial, nada de ruido</h2>
                </header>
                <div class="x-features__grid">
                    @foreach([
                        ['Tarifa negociable', 'Acuerda el precio en pasos de $500 COP antes de cada servicio.'],
                        ['Tiempo real', 'Sigue el recorrido desde la app con mapa en vivo.'],
                        ['Conductores verificados', 'Documentos y validación desde el panel administrativo.'],
                        ['Wallet seguro', 'Recargas con Wompi y saldo prepago para viajes.'],
                        ['SOS integrado', 'Alerta y registro de eventos para soporte operativo.'],
                        ['Una sola app', 'Cambia entre pasajero y conductor sin instalar otra cosa.'],
                    ] as [$title, $desc])
                    <article class="x-feat x-reveal">
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="x-screens" id="app">
            <div class="x-wrap">
                <header class="x-head x-reveal">
                    <p class="x-eyebrow">La app en acción</p>
                    <h2>Pantallas listas para tiendas</h2>
                    <p>Capturas de producción — pasajero y conductor en Medellín.</p>
                </header>
                @if(count($storeShots) > 0)
                <div class="x-screens__track x-reveal">
                    @foreach($storeShots as $file => $shot)
                    <figure class="x-screens__card">
                        <div class="x-screens__frame">
                            <img src="{{ $shot }}" alt="{{ $storeLabels[$file] ?? $siteName }}" loading="lazy">
                        </div>
                        <figcaption>{{ $storeLabels[$file] ?? pathinfo($file, PATHINFO_FILENAME) }}</figcaption>
                    </figure>
                    @endforeach
                </div>
                @else
                <div class="x-screens__duo x-reveal">
                    <figure class="x-screens__card x-screens__card--large">
                        <img src="{{ $img('Customer-App-Features.png') }}" alt="Vista pasajero">
                    </figure>
                    <figure class="x-screens__card x-screens__card--large">
                        <img src="{{ $img('Driver-App-Features.png') }}" alt="Vista conductor">
                    </figure>
                </div>
                @endif
            </div>
        </section>

        <section class="x-download" id="descargar">
            <div class="x-wrap x-download__inner x-reveal">
                <div class="x-download__copy">
                    <p class="x-eyebrow x-eyebrow--lime">Únete hoy</p>
                    <h2>Empieza a moverte con {{ $siteName }}</h2>
                    <p>
                        Descarga, regístrate y solicita tu primer viaje o envío.
                        Aplican términos y condiciones; {{ $siteName }} valida usuarios y conductores.
                    </p>
                    <div class="x-stores">
                        <a href="{{ $playStore }}" class="x-store" rel="noopener noreferrer">
                            <img src="{{ $img('google-play-icon.png') }}" alt="">
                            <span><small>Disponible en</small><strong>Google Play</strong></span>
                        </a>
                        <a href="{{ $appStore }}" class="x-store" rel="noopener noreferrer">
                            <img src="{{ $img('app-store-icon.png') }}" alt="">
                            <span><small>Disponible en</small><strong>App Store</strong></span>
                        </a>
                    </div>
                </div>
                <div class="x-download__art">
                    <img src="{{ $img('Be-The-Part-of-XISTI.png') }}" alt="Comunidad {{ $siteName }}">
                </div>
            </div>
        </section>
    </main>

    <footer class="x-foot" id="contacto">
        <div class="x-wrap x-foot__grid">
            <div class="x-foot__brand">
                <img src="{{ $logo }}" alt="{{ $siteName }}">
                <p>Soporte operativo y consultas sobre {{ $siteName }} APP en Medellín.</p>
                <div class="x-foot__social">
                    <a href="{{ $instagram }}" rel="noopener" aria-label="Instagram">
                        <img src="{{ $img('instagram.svg') }}" alt="">
                    </a>
                    <a href="{{ $facebook }}" rel="noopener" aria-label="Facebook">
                        <img src="{{ $img('facebook.svg') }}" alt="">
                    </a>
                    <a href="{{ $linkedin }}" rel="noopener" aria-label="LinkedIn">
                        <img src="{{ $img('linkedin.svg') }}" alt="">
                    </a>
                </div>
            </div>
            <div class="x-foot__links">
                <h4>Enlaces</h4>
                <a href="#inicio">Inicio</a>
                <a href="#servicios">Servicios</a>
                <a href="#como">Cómo funciona</a>
                <a href="{{ url('/privacy-policy') }}">Privacidad</a>
                <a href="{{ url('/terms-and-conditions') }}">Términos</a>
                <a href="{{ url('/admin/login') }}">Panel admin</a>
            </div>
            <div class="x-foot__contact">
                <h4>Contacto</h4>
                <p class="x-foot__row">
                    <img src="{{ $img('location.svg') }}" alt="">
                    <span>{{ $address }}</span>
                </p>
                <p class="x-foot__row">
                    <img src="{{ $img('mail.svg') }}" alt="">
                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                </p>
                <p class="x-foot__row">
                    <img src="{{ $img('call.svg') }}" alt="">
                    <span>{{ $phone }}</span>
                </p>
            </div>
            <div class="x-foot__map">
                <img src="{{ $img('Footer-map-image.png') }}" alt="Mapa Medellín">
            </div>
        </div>
        <p class="x-foot__copy">{{ $copyright }}</p>
    </footer>

</div>
<script>
(function () {
    var top = document.getElementById('x-top');
    var burger = document.getElementById('x-burger');
    var nav = document.getElementById('x-nav');

    function onScroll() {
        if (!top) return;
        top.classList.toggle('is-scrolled', window.scrollY > 24);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (burger && nav) {
        burger.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            burger.classList.toggle('is-open', open);
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        nav.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                nav.classList.remove('is-open');
                burger.classList.remove('is-open');
                burger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    var reveals = document.querySelectorAll('.x-reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-in'); });
    }
})();
</script>
</body>
</html>

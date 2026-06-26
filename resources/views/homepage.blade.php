@php
    $gs = request()->get('general_settings');
    $siteName = $gs->website_name ?? 'XISTI';
    $logoVer = '2.9';
    $logoFile = 'assets/images/website-logo-icon/xisti-logo-header.png';
    if (!is_file(public_path($logoFile))) {
        $logoFile = 'assets/images/website-logo-icon/xisti-logo-web.png';
    }
    $logo = asset($logoFile).'?v='.$logoVer;
    $faviconVer = '2.6';
    $favicon = asset('favicon.ico').'?v='.$faviconVer;
    $faviconPng = asset('assets/images/website-logo-icon/xisti-favicon.png').'?v='.$faviconVer;
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
    $storeGalleryOrder = [
        '01-splash.png',
        '02-rider-services.png',
        '03-rider-offer.png',
        '04-rider-radar.png',
        '05-driver-incoming.png',
        '06-driver-detail.png',
        '07-driver-active.png',
        '08-rider-active.png',
    ];
    $storeLabels = [
        '01-splash.png' => 'Splash XISTI',
        '02-rider-services.png' => 'Servicios pasajero',
        '03-rider-offer.png' => 'Proponer valor',
        '04-rider-radar.png' => 'Radar en vivo',
        '05-driver-incoming.png' => 'Solicitud conductor',
        '06-driver-detail.png' => 'Detalle del viaje',
        '07-driver-active.png' => 'Conductor en ruta',
        '08-rider-active.png' => 'Viaje en curso',
    ];
    $storeDesc = [
        '01-splash.png' => 'Fácil y Seguro — la promesa de XISTI desde el primer segundo en Medellín.',
        '02-rider-services.png' => 'Moto, Carro, Viajes y Envío en un mapa oscuro. Tú eliges el servicio.',
        '03-rider-offer.png' => 'Define el monto a ofrecer, método de pago y detalles del envío antes de confirmar.',
        '04-rider-radar.png' => 'Negocia en pasos de $500 COP mientras conductores cercanos responden a tu oferta.',
        '05-driver-incoming.png' => 'El conductor en línea recibe origen, destino, valor y perfil del pasajero al instante.',
        '06-driver-detail.png' => 'Ruta en mapa, método de pago y contraofertas antes de aceptar el servicio.',
        '07-driver-active.png' => 'Gestiona recogida, llegada y estados del viaje con controles claros en pantalla.',
        '08-rider-active.png' => 'Sigue a tu conductor en el mapa con ETA, vehículo y código de verificación.',
    ];
    if (is_dir($storeDir)) {
        foreach ($storeGalleryOrder as $name) {
            $path = $storeDir.'/'.$name;
            if (is_file($path)) {
                $storeShots[$name] = asset('assets/front/img/store/'.$name).'?v='.filemtime($path);
            }
        }
    }
    $shot = fn (string $name) => $storeShots[$name] ?? null;
    $heroPhone = $shot('02-rider-services.png') ?? $shot('08-rider-active.png') ?? $img('xisti-hero-medellin.png');
    $driverPhone = $shot('07-driver-active.png') ?? $shot('06-driver-detail.png') ?? $shot('05-driver-incoming.png') ?? $heroPhone;
    $splashPhone = $shot('01-splash.png') ?? $heroPhone;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="XISTI transforma tu manera de moverte por Medellín. Seguridad, economía y facilidad en cada viaje. Descarga la app en Google Play y App Store.">
    <meta name="theme-color" content="#070707">
    <title>{{ $siteName }} — Fácil y Seguro</title>
    <link rel="icon" href="{{ $favicon }}" sizes="any">
    <link rel="icon" href="{{ $faviconPng }}" type="image/png" sizes="48x48">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/website-logo-icon/xisti-favicon-180.png') }}?v={{ $faviconVer }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('assets/images/website-logo-icon/xisti-favicon-192.png') }}?v={{ $faviconVer }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700;800&family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/front/css/xisti-homepage.css') }}?v=2.6.0" rel="stylesheet">
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
                        Negocia tu valor en pasos de <strong>$500 COP</strong> y conecta con conductores verificados en tiempo real.
                    </p>
                    <div class="x-hero__chips">
                        <span>Moto</span>
                        <span>Carro</span>
                        <span>Envíos</span>
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
                            <span class="x-device__float-label">Valor acordado</span>
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
                        ['Economía', 'Valores justos y negociables. Calidad y accesibilidad en cada servicio, con recargas wallet desde $13.000 COP.'],
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
                    <div class="x-band__phones">
                        <div class="x-band__phone x-band__phone--back">
                            <img src="{{ $splashPhone }}" alt="Splash {{ $siteName }}">
                        </div>
                        <div class="x-band__phone x-band__phone--front">
                            <img src="{{ $heroPhone }}" alt="App {{ $siteName }} pasajero">
                        </div>
                    </div>
                </div>
                <div class="x-band__text x-reveal">
                    <p class="x-eyebrow">Sobre {{ $siteName }}</p>
                    <h2>Movilidad hecha para la ciudad, no para plantillas</h2>
                    <p>
                        {{ $siteName }} conecta pasajeros y conductores independientes con una experiencia oscura, rápida y clara:
                        solicita viajes o envíos, revisa opciones cercanas y acuerda valores antes de confirmar.
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
                        ['Negocia', 'Propón valor y revisa conductores.'],
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
                                <li>Valor negociable antes de confirmar</li>
                                <li>Envíos urbanos con Moto y Carro</li>
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
                                <li>Oferta tu valor a cada solicitud</li>
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
                        ['Valor negociable', 'Acuerda el precio en pasos de $500 COP antes de cada servicio.'],
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
                    <p>Capturas reales del flujo pasajero ↔ conductor en Medellín.</p>
                </header>
                <div class="x-screens__track x-reveal">
                    @foreach($storeShots as $file => $shotUrl)
                    <figure class="x-screens__card">
                        <div class="x-screens__frame">
                            <img src="{{ $shotUrl }}" alt="{{ $storeLabels[$file] ?? $siteName }}" loading="lazy" decoding="async">
                        </div>
                        <figcaption>
                            <strong>{{ $storeLabels[$file] ?? pathinfo($file, PATHINFO_FILENAME) }}</strong>
                            @if(!empty($storeDesc[$file]))
                            <span class="x-screens__desc">{{ $storeDesc[$file] }}</span>
                            @endif
                        </figcaption>
                    </figure>
                    @endforeach
                </div>
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
                    <img src="{{ $img('Be-The-Part-of-XISTI.png') }}?v=2.4" alt="Comunidad {{ $siteName }}">
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
                <img src="{{ $img('xisti-hero-medellin.png') }}?v=2.4" alt="Medellín {{ $siteName }}">
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

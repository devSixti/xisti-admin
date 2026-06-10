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
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="XISTI — movilidad urbana en Medellín. Negocia tu tarifa, recarga wallet y viaja fácil y seguro.">
    <title>{{ $siteName }} — Fácil y Seguro</title>
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <link href="{{ asset('assets/front/css/bootstrap_version_5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/xisti-homepage.css') }}?v=1.0.0" rel="stylesheet">
</head>
<body class="xisti-home">
<div class="x-wrap">

    <header class="x-nav">
        <div class="x-nav__inner">
            <a class="x-nav__brand" href="#hero">
                <img src="{{ $logo }}" alt="{{ $siteName }}">
            </a>
            <div class="x-nav__panel" id="x-nav-panel">
            <ul class="x-nav__links">
                <li><a href="#hero">Inicio</a></li>
                <li><a href="#about">Nosotros</a></li>
                <li><a href="#how">Cómo funciona</a></li>
                <li><a href="#features">Características</a></li>
                <li><a href="#download">Descargar</a></li>
                <li><a href="#contact">Contacto</a></li>
            </ul>
            </div>
            <button class="x-nav__toggle" type="button" aria-expanded="false" aria-controls="x-nav-panel" id="x-nav-toggle">Menú</button>
        </div>
    </header>

    <section class="x-hero" id="hero">
        <div class="x-hero__grid" aria-hidden="true"></div>
        <div class="x-hero__lines" aria-hidden="true"></div>
        <div class="x-hero__content">
            <div>
                <span class="x-badge">Fácil y Seguro</span>
                <h1><span>{{ $siteName }}</span> te mueve por Medellín.</h1>
                <p class="x-hero__lead">
                    Viajes urbanos, envíos y wallet prepago en una sola app. Negocia tu tarifa en pasos de $500 COP
                    y conéctate con conductores verificados en tiempo real.
                </p>
                <div class="x-hero__actions">
                    <a href="#download" class="x-btn x-btn--primary">Descargar app</a>
                    <a href="#contact" class="x-btn x-btn--ghost">Contáctanos</a>
                </div>
            </div>
            <div class="x-hero__visual">
                <div class="x-hero__orb" aria-hidden="true"></div>
                <img src="{{ $img('hero-section-image-1.png') }}" alt="Aplicación móvil {{ $siteName }}">
            </div>
        </div>
        <div class="x-stats">
            <div class="x-stat">
                <strong>Negociación</strong>
                <span>Tarifas flexibles por pasos de $500</span>
            </div>
            <div class="x-stat">
                <strong>Wallet</strong>
                <span>Recarga segura con Wompi</span>
            </div>
            <div class="x-stat">
                <strong>Seguridad</strong>
                <span>OTP, SOS y seguimiento en vivo</span>
            </div>
        </div>
    </section>

    <section class="x-section" id="about">
        <div class="x-container x-about">
            <div class="x-about__img">
                <img src="{{ $img('about-us-image-1.png') }}" alt="Sobre {{ $siteName }}">
            </div>
            <div class="x-about__copy">
                <div class="x-section__eyebrow">Sobre nosotros</div>
                <h2 style="font-family: var(--font-display); font-size: clamp(1.5rem, 3vw, 2rem); margin: 0 0 1rem;">Movilidad urbana hecha para la ciudad</h2>
                <p>{{ $siteName }} conecta pasajeros y conductores independientes con una experiencia moderna: solicita viajes o envíos, revisa opciones cercanas y acuerda tarifas antes de confirmar.</p>
                <p>La plataforma facilita la conexión tecnológica; los servicios de transporte son acordados directamente entre usuarios y conductores bajo nuestros términos de uso.</p>
            </div>
        </div>
    </section>

    <section class="x-section x-section--alt" id="how">
        <div class="x-container">
            <div class="x-section__head">
                <div class="x-section__eyebrow">Flujo simple</div>
                <h2>¿Cómo funciona {{ $siteName }}?</h2>
                <p>De la descarga al viaje en seis pasos claros.</p>
            </div>
            <div class="x-steps">
                @foreach([
                    ['01', 'user_registration_profile_setup.png', 'Descarga la app', 'Disponible en Google Play y App Store. Crea tu cuenta en minutos.'],
                    ['02', 'booking_a_ride_or_sending_a_parcel.png', 'Regístrate', 'Completa tu perfil como pasajero o conductor verificado.'],
                    ['03', 'fare_bidding.png', 'Solicita servicio', 'Elige origen, destino y tipo: viaje o envío urbano.'],
                    ['04', 'driver_selection.png', 'Negocia tarifa', 'Propón tu precio y revisa conductores cercanos.'],
                    ['05', 'real_time_tracking.png', 'Sigue en vivo', 'Monitorea el recorrido desde la aplicación.'],
                    ['06', 'payments_and_rating.png', 'Paga y califica', 'Wallet prepago, Wompi y valoración al finalizar.'],
                ] as [$num, $icon, $title, $desc])
                <article class="x-step">
                    <div class="x-step__num">{{ $num }}</div>
                    <div class="x-step__icon"><img src="{{ $img($icon) }}" alt=""></div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="x-section" id="features">
        <div class="x-container">
            <div class="x-section__head">
                <div class="x-section__eyebrow">Por qué elegirnos</div>
                <h2>Todo lo que necesitas en una app</h2>
                <p>Pasajeros y conductores comparten la misma plataforma con herramientas diseñadas para Medellín.</p>
            </div>
            <div class="x-bento">
                <div class="x-bento__card x-bento__card--hero">
                    <img class="phone" src="{{ $img('Customer-App-Features.png?v=2.0') }}" alt="Vista pasajero">
                </div>
                @foreach([
                    ['Book_ride_with_own_fare.png', 'Tarifa negociable', 'Acuerda el precio antes de confirmar cada servicio.'],
                    ['Price_negotiations.png', 'Viajes y envíos', 'Moto y carro para mover personas o paquetes urbanos.'],
                    ['Real-Time-Tracking.png', 'Tiempo real', 'Ubicación del conductor visible durante el trayecto.'],
                    ['In-app-payments.png', 'Wallet + Wompi', 'Recarga mínima $13.000 COP y pagos digitales seguros.'],
                    ['check_drivers_details.png', 'Conductores verificados', 'Documentación y validación dentro de la plataforma.'],
                ] as [$icon, $title, $desc])
                <div class="x-bento__card x-bento__card--wide">
                    <h3><img src="{{ $img($icon) }}" alt="">{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
                @endforeach
                <div class="x-bento__card x-bento__card--hero">
                    <img class="phone" src="{{ $img('Driver-App-Features.png?v=2.0') }}" alt="Vista conductor">
                </div>
                @foreach([
                    ['Set_availability.png', 'Modo conductor', 'Activa disponibilidad y recibe solicitudes cercanas.'],
                    ['Bid_with_your_fare.png', 'Oferta tu tarifa', 'Responde con tu precio a cada solicitud.'],
                    ['in_app_wallet.png', 'Gana con flexibilidad', 'Gestiona servicios desde la misma aplicación.'],
                ] as [$icon, $title, $desc])
                <div class="x-bento__card">
                    <h3><img src="{{ $img($icon) }}" alt="">{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="x-section x-section--alt" id="download">
        <div class="x-cta">
            <div>
                <h3>Únete a {{ $siteName }}</h3>
                <p style="color: var(--x-muted); margin: 0 0 1.5rem;">
                    Descarga la app, regístrate y empieza a moverte. Aplican términos y condiciones.
                    {{ $siteName }} se reserva el derecho de admisión y validación de usuarios.
                </p>
                <div class="x-store-btns">
                    <a href="{{ $playStore }}" class="x-store-btn" rel="noopener">
                        <img src="{{ $img('google-play-icon.png') }}" alt="Google Play">
                        <span><small>Disponible en</small><strong>Google Play</strong></span>
                    </a>
                    <a href="{{ $appStore }}" class="x-store-btn" rel="noopener">
                        <img src="{{ $img('app-store-icon.png') }}" alt="App Store">
                        <span><small>Disponible en</small><strong>App Store</strong></span>
                    </a>
                </div>
            </div>
            <div>
                <img src="{{ $img('Be-The-Part-of-XISTI.png?v=2.0') }}" alt="Comunidad {{ $siteName }}" style="width:100%; border-radius: var(--radius-lg);">
            </div>
        </div>
    </section>

    <footer class="x-footer" id="contact">
        <div class="x-container x-footer__grid">
            <div class="x-footer__brand">
                <img src="{{ $logo }}" alt="{{ $siteName }}">
                <p>Nuestro equipo está disponible para brindar información y soporte relacionado con {{ $siteName }} APP.</p>
                <h4 style="font-family: var(--font-display); font-size: 1rem; margin: 1.5rem 0 1rem;">Contáctenos</h4>
                <div class="x-contact-item">
                    <img src="{{ $img('location.svg') }}" alt="">
                    <span>{{ $address }}</span>
                </div>
                <div class="x-contact-item">
                    <img src="{{ $img('mail.svg') }}" alt="">
                    <a href="mailto:{{ $email }}" style="color: inherit; text-decoration: none;">{{ $email }}</a>
                </div>
                <div class="x-contact-item">
                    <img src="{{ $img('call.svg') }}" alt="">
                    <span>{{ $phone }}</span>
                </div>
                <div class="x-social">
                    <a href="{{ $instagram }}" rel="noopener" aria-label="Instagram"><img src="{{ $img('instagram.svg') }}" alt=""></a>
                    <a href="{{ $facebook }}" rel="noopener" aria-label="Facebook"><img src="{{ $img('facebook.svg') }}" alt=""></a>
                    <a href="{{ $linkedin }}" rel="noopener" aria-label="LinkedIn"><img src="{{ $img('linkedin.svg') }}" alt=""></a>
                </div>
            </div>
            <div class="x-footer__map">
                <img src="{{ $img('Footer-map-image.png?v=2.0') }}" alt="Mapa Medellín">
            </div>
        </div>
        <p class="x-footer__copy">{{ $copyright }}</p>
    </footer>

</div>
<script>
(function () {
    var toggle = document.getElementById('x-nav-toggle');
    var panel = document.getElementById('x-nav-panel');
    if (!toggle || !panel) return;
    toggle.addEventListener('click', function () {
        var open = panel.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    panel.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            panel.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
})();
</script>
</body>
</html>

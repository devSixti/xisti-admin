<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{request()->get('general_settings')->website_name != null ? request()->get('general_settings')->website_name : 'XISTI'}}</title>
    <link rel="icon"
          href="{{(request()->get('general_settings')->website_favicon != Null) ? asset('assets/images/website-logo-icon/'.request()->get('general_settings')->website_favicon) : ''  }}"
          type="image/x-icon">
    <link
        href="{{ (request()->get('general_settings')->website_favicon != Null) ? asset('assets/images/website-logo-icon/'.request()->get('general_settings')->website_favicon) : '' }}"
        rel="apple-touch-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/front/css/bootstrap_version_5.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --xisti-bg: #0B0B0B;
            --xisti-surface: #141414;
            --xisti-primary: #39FF14;
            --xisti-secondary: #9333EA;
            --xisti-text: #F3F4F6;
            --xisti-muted: #9CA3AF;
            --theme-primary: var(--xisti-primary);
            --theme-light: rgba(57, 255, 20, 0.08);
            --theme-medium-light: rgba(57, 255, 20, 0.18);
            --theme-gra-medium-light: rgba(147, 51, 234, 0.22);
        }

        body {
            background-color: var(--xisti-bg);
            color: var(--xisti-text);
        }

        .bg-theme-gradient {
            background: linear-gradient(135deg, var(--xisti-bg) 0%, #111827 45%, rgba(147, 51, 234, 0.15) 100%) !important;
        }

        .bg-theme-light-gradient {
            background: linear-gradient(180deg, var(--xisti-surface) 0%, var(--xisti-bg) 100%) !important;
        }

        .bg-theme-light { background-color: var(--xisti-surface); }
        .bg-theme-color { background-color: var(--xisti-primary); color: #0B0B0B !important; }
        .border-theme { border: 1px solid rgba(57, 255, 20, 0.35) !important; border-radius: 12px; }

        .custom-hover {
            color: #0B0B0B;
            background-color: var(--xisti-primary);
            border: 1px solid var(--xisti-primary);
            transition: all 0.3s ease-in-out;
        }

        .custom-hover:hover {
            background-color: transparent !important;
            color: var(--xisti-primary) !important;
            border-color: var(--xisti-primary) !important;
        }

        .text-theme { color: var(--xisti-primary); }
        .text-muted { color: var(--xisti-muted) !important; }

        section { padding-top: 75px !important; padding-bottom: 75px !important; }
        header { margin-top: 75px !important; }

        .navbar {
            background-color: rgba(11, 11, 11, 0.92) !important;
            backdrop-filter: blur(8px);
            position: fixed !important;
            top: 0 !important;
            width: 100% !important;
            border-bottom: 1px solid rgba(57, 255, 20, 0.15);
        }

        .navbar .nav-link { color: var(--xisti-text) !important; }
        .navbar .nav-link:hover { color: var(--xisti-primary) !important; }
        .navbar-toggler { filter: invert(1); }

        #how-it-works { padding-top: 80px; }
        .gradient-shadow { box-shadow: 0 8px 32px rgba(57, 255, 20, 0.12) !important; }
        .passenger_driver { background: linear-gradient(135deg, #141414, rgba(147, 51, 234, 0.25)) !important; }

        .card {
            background-color: var(--xisti-surface);
            color: var(--xisti-text);
        }

        footer.bg-light {
            background-color: var(--xisti-surface) !important;
            color: var(--xisti-text) !important;
        }

        .tagline-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            border: 1px solid rgba(147, 51, 234, 0.5);
            color: var(--xisti-primary);
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }
    </style>
</head>
<body>

<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ request()->get('general_settings')->website_logo != null ? asset('assets/images/website-logo-icon/'.request()->get('general_settings')->website_logo) : asset('assets/images/website-logo-icon/xisti-logo.png') }}" alt="XISTI" height="50">
            </a>

            <!-- Mobile Menu Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active"><a class="nav-link" href="#hero-section">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">Cómo Funciona</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Características</a></li>
                    <li class="nav-item"><a class="nav-link" href="#more-features">Más Características</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contáctenos</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<!-- Hero Section -->
<section id="hero-section" class="bg-theme-gradient pt-5 pb-0">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left Column (Text) -->
            <div class="col-md-6 text-center text-md-start">
                <span class="tagline-badge mb-3">Fácil y Seguro</span>
                <h1 class="fw-bold text-theme mb-2">XISTI te mueve.</h1>
                <p class="text-muted text-start">
                    Movilidad urbana en Medellín: negocia tu tarifa, paga con wallet prepago y viaja con conductores verificados.
                </p>
                <p class="text-muted text-start">
                    Una plataforma tecnológica diseñada para conectar usuarios y conductores
                    independientes para viajes, envíos y soluciones de movilidad urbana de forma rápida,
                    flexible y segura.
                </p>
                <p class="text-muted text-start">
                    Desde una sola aplicación, los usuarios pueden solicitar servicios, revisar opciones
                    disponibles y conectarse fácilmente con conductores cercanos.
                </p>
                <a href="#contact" class="btn px-4 py-2 rounded-4 custom-hover bg-theme-color">Contáctenos</a>
            </div>

            <!-- Right Column (Image) -->
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/front/img/hero-section-image-1.png') }}" class="img-fluid" alt="Hero Image">
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="container">
    <h2 class="text-center mb-4 d-flex flex-wrap justify-content-center align-items-center">
        <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
        <span>Sobre XISTI</span>
        <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
    </h2>

    <div class="row align-items-center">
        <!-- Image Column -->
        <div class="col-md-6 text-center">
            <img src="{{ asset('assets/front/img/about-us-image-1.png') }}" class="img-fluid rounded"
                 alt="About Us Image">
        </div>

        <!-- Text Column -->
        <div class="col-md-6">
            <div class="p-3">
                <p class="text-muted">
                    XISTI es una plataforma tecnológica creada para facilitar la movilidad y los envíos
                    urbanos de forma moderna, flexible y cercana.
                </p>
                <p class="text-muted">
                    La aplicación conecta usuarios y conductores independientes mediante una experiencia
                    práctica que permite solicitar viajes, realizar envíos y acceder a soluciones de movilidad
                    desde una sola plataforma.
                </p>
                <p class="text-muted">
                    Con XISTI, los usuarios pueden revisar opciones disponibles, negociar tarifas y
                    conectarse fácilmente con conductores cercanos según sus necesidades.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How Does XISTI Work Section -->
<div class="bg-theme-light pt-0 pb-0" id="how-it-works">
<section class="container text-center">
    <h2 class="text-center mb-4 d-flex flex-wrap justify-content-center align-items-center">
        <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
        <span>¿Cómo funciona XISTI?</span>
        <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
    </h2>

    <div class="row justify-content-center gap-4">
        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/user_registration_profile_setup.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Descarga la aplicación</h4>
                </div>
                <p class="text-muted mb-0 text-start">Descarga XISTI desde Google Play o App Store y crea tu cuenta fácilmente.</p>
            </div>
        </div>
        <!-- Card End -->

        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/booking_a_ride_or_sending_a_parcel.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Regístrate fácilmente</h4>
                </div>
                <p class="text-muted mb-0 text-start">Completa tu perfil como usuario o conductor y accede a las funcionalidades de la
                    plataforma.</p>
            </div>
        </div>
        <!-- Card End -->

        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/fare_bidding.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Solicita un viaje o envío</h4>
                </div>
                <p class="text-muted mb-0 text-start">Selecciona ubicación, destino y tipo de servicio que necesitas.</p>
            </div>
        </div>
        <!-- Card End -->

        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/driver_selection.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Negocia tu tarifa</h4>
                </div>
                <p class="text-muted mb-0 text-start">Propón una tarifa y revisa las opciones disponibles de conductores cercanos.</p>
            </div>
        </div>
        <!-- Card End -->

        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/real_time_tracking.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Seguimiento en tiempo real</h4>
                </div>
                <p class="text-muted mb-0 text-start">Sigue el recorrido del viaje o envío directamente desde la aplicación.</p>
            </div>
        </div>
        <!-- Card End -->

        <!-- Card Start -->
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card py-4 px-4 shadow-sm rounded-4 border-theme">
                <div class="d-flex align-items-center text-start gap-3 mb-2">
                    <div
                        class="bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/front/img/payments_and_rating.png') }}" alt="Icon" width="40">
                    </div>
                    <h4 class="fs-5 mb-0">Muévete fácil y seguro</h4>
                </div>
                <p class="text-muted mb-0 text-start">Disfruta una experiencia moderna, flexible y diseñada para facilitar tu movilidad.</p>
            </div>
        </div>
        <!-- Card End -->
    </div>
</section>
</div>

<!-- Customer App Features -->
<section id="features" class="container pb-0">
    <h2 class="text-center mb-3 d-flex flex-wrap justify-content-center align-items-center">
        <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
        <span>¿Por qué elegir XISTI?</span>
        <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
    </h2>

    <!-- Description Text -->
    <p class="text-center text-muted mx-auto" style="max-width: 600px;">
        XISTI reúne movilidad, tecnología y flexibilidad dentro de una sola aplicación diseñada
        para usuarios y conductores.
    </p>

    <div class="row align-items-center mt-4">
        <!-- Left Column -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="d-flex flex-column gap-3">
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Book_ride_with_own_fare.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Aplicación todo en uno</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Usuarios y conductores interactúan desde una sola aplicación diseñada para simplificar
                        la experiencia.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Price_negotiations.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Negocia tu tarifa</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Usuarios y conductores pueden acordar tarifas de manera flexible antes de confirmar un
                        servicio.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Accept-Reject-Offer.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Viajes y envíos en una sola app</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Solicita movilidad o envíos urbanos desde una misma plataforma moderna e intuitiva.</p>
                </div>
            </div>
        </div>

        <!-- Center Column (App Image) -->
        <div class="col-lg-4 col-md-12 text-center mb-3">
            <img src="{{ asset('assets/front/img/Customer-App-Features.png?v=2.0') }}"
                 class="img-fluid passenger_driver rounded-4" alt="App Preview">
        </div>

        <!-- Right Column -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="d-flex flex-column gap-3">
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Real-Time-Tracking.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Seguimiento en tiempo real</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Consulta la ubicación del conductor y realiza seguimiento del servicio directamente
                        desde la aplicación.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/check_drivers_details.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Experiencia flexible</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">La plataforma permite una experiencia más dinámica y adaptable para usuarios y
                        conductores según cada necesidad.
                    </p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/become_a_driver.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Conecta usuarios y conductores</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">La plataforma facilita la conexión entre personas que necesitan movilidad o envíos y
                        conductores independientes disponibles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Driver App Features -->
<section id="features" class="container pt-5 pb-4">
    <h2 class="text-center mb-3 d-flex flex-wrap justify-content-center align-items-center">
        <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
        <span>Conduce con XISTI</span>
        <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
    </h2>

    <!-- Description Text -->
    <p class="text-center text-muted mx-auto" style="max-width: 600px;">
        XISTI también está diseñado para ofrecer a los conductores independientes una
        experiencia práctica, flexible y moderna dentro de una sola aplicación.
    </p>

    <div class="row align-items-center mt-4">
        <!-- Left Column -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="d-flex flex-column gap-3">
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Manage_vehicle.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Administra tu disponibilidad</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Activa o desactiva tu disponibilidad fácilmente desde la aplicación.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Check_ride_details.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Consulta información antes de aceptar</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Revisa detalles relacionados con solicitudes y servicios antes de tomar decisiones
                        dentro de la plataforma.
                    </p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/in_app_wallet.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Recibe solicitudes desde la app</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Conéctate con solicitudes disponibles según el tipo de servicio y configuración
                        habilitada.</p>
                </div>
            </div>
        </div>

        <!-- Center Column (App Image) -->
        <div class="col-lg-4 col-md-12 text-center mb-3">
            <img src="{{ asset('assets/front/img/Driver-App-Features.png?v=2.0') }}"
                 class="img-fluid passenger_driver rounded-4" alt="App Preview">
        </div>

        <!-- Right Column -->
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="d-flex flex-column gap-3">
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/cancel_trip.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Gestiona tu perfil y vehículo</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Revisa detalles relacionados con solicitudes y servicios antes de tomar decisiones
                        dentro de la plataforma.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Bid_with_your_fare.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Consulta información antes de aceptar</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Revisa detalles relacionados con solicitudes y servicios antes de tomar decisiones
                        dentro de la plataforma.</p>
                </div>
                <div class="card p-3 rounded-4 bg-theme-light-gradient border-theme">
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="icon bg-theme-color text-white p-2 rounded-3 d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/front/img/Set_availability.png') }}" alt="Car Icon"
                                 width="30">
                        </div>
                        <h4 class="fw-bold text-dark mb-0">Experiencia práctica y flexible</h4>
                    </div>
                    <p class="text-muted mt-3 mb-0">Una plataforma pensada para facilitar la conexión entre usuarios y conductores
                        independientes.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Be The Part Of XISTI App -->
<section class="container py-3 py-lg-5">
    <div class="bg-theme-light border rounded-4 shadow-sm pt-md-5 px-md-5 pb-0 gradient-shadow border-theme">


        <h2 class="text-center mb-4 mb-md-4 d-flex flex-wrap justify-content-center align-items-center">
            <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
            <span>Sé parte de la aplicación XISTI</span>
            <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
        </h2>

        <!-- Description Text -->
        <p class="text-muted mx-auto mb-4 mb-lg-5 px-2 px-md-0 text-center" style="max-width: 600px;">
            XISTI también está diseñado para ofrecer a los conductores independientes una
            experiencia práctica, flexible y moderna dentro de una sola aplicación.
        </p>

        <div class="row align-items-start g-4 g-lg-5">
            <!-- Left Column (Image) -->
            <div class="col-lg-6 order-lg-1 order-2">
                <img src="{{ asset('assets/front/img/Be-The-Part-of-XISTI.png?v=2.0') }}"
                     class="img-fluid w-100"
                     alt="Join Us Image">
            </div>

            <!-- Right Column (Text + Download Buttons) -->
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="d-flex flex-column h-100">
                    <div class="mb-3 mb-md-4 p-2">
                        <p class="text-muted mb-3">
                            XISTI es una plataforma tecnológica que conecta usuarios y conductores
                            independientes.
                        </p>

                        <p class="text-muted">
                            La plataforma no presta directamente servicios de transporte ni participa como parte
                            operativa en acuerdos o negociaciones directas entre usuarios y conductores.
                        </p>
                        <p class="text-muted">
                            Aplican términos y condiciones de uso.
                        </p>

                        <p class="text-muted">
                            XISTI se reserva el derecho de admisión, validación y permanencia de usuarios o
                            conductores registrados en la plataforma.
                        </p>
                    </div>

                    <!-- Download Buttons -->
                    <div class="d-flex flex-wrap justify-content-lg-start justify-content-center gap-3 mt-auto">
                        <a href="{{ request()->get('general_settings')->user_playstore_link != null ? request()->get('general_settings')->user_playstore_link: '' }}" class="btn btn-dark d-flex align-items-center px-2 px-md-3 py-2 rounded-4 flex-shrink-0">
                            <img src="{{ asset('assets/front/img/google-play-icon.png') }}" alt="play-store" width="25" class="me-2">
                            <div>
                                <span class="small text-capitalize">Get It On</span>
                                <span class="fw-bold text-capitalize d-block">Google Play</span>
                            </div>
                        </a>

                        <a href="{{ request()->get('general_settings')->user_appstore_link != null ? request()->get('general_settings')->user_appstore_link: '' }}" class="btn btn-dark d-flex align-items-center px-2 px-md-3 py-2 rounded-4 flex-shrink-0">
                            <img src="{{ asset('assets/front/img/app-store-icon.png') }}" alt="app-store" width="30" class="me-2">
                            <div>
                                <span class="small text-capitalize">Get It On</span>
                                <span class="fw-bold text-capitalize d-block">App Store</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="container pb-0 pt-3">
    <div class="row align-items-center bg-theme-color text-white px-lg-5 px-4 rounded-5">
        <!-- Left Section: Text & Buttons -->
        <div class="col-md-6  pb-2">
            <h3 class="fw-bold mt-4 mt-md-0">Ya sea que tenga una pregunta o una consulta sobre nuestros servicios, ¡no dude en contactarnos!</h3>
            <div class="d-flex gap-3 mt-2">
                <a href="#contact" class="btn btn-outline-light px-4 py-2 rounded-4 custom-hover">Contáctenos</a>
            </div>
        </div>

        <!-- Right Section: Image (Fixed Positioning) -->
        <div class="col-md-6 text-end">
            <img src="{{ asset('assets/front/img/cta-image-1.png') }}"
                 class="img-fluid w-100"
                 style="max-width: 400px;"
                 alt="Car Sharing Image">
        </div>
    </div>
</section>

<!-- More Features Section -->
<section id="more-features" class="container">
    <h2 class="text-center mb-4 d-flex flex-wrap justify-content-center align-items-center">
        <img src="{{ asset('assets/front/img/left-line.png?v=2.0') }}" class="img-fluid me-2" style="max-width: 10%;">
        <span>Funciones diseñadas para una mejor experiencia</span>
        <img src="{{ asset('assets/front/img/right-line.png?v=2.0') }}" class="img-fluid ms-2" style="max-width: 10%;">
    </h2>

    <div class="row">
        <!-- Feature Cards (2 per row) -->
        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/Single_app_for_driver_and_customer.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Código OTP de validación</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">La aplicación cuenta con validación mediante código OTP para confirmar servicios y
                    mejorar la seguridad durante la experiencia.</p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/Book_a_ride_and_courier.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Botón SOS</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">XISTI integra herramientas de seguridad como botón SOS dentro de la aplicación.</p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/Negotiate_prices_with_drivers.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Seguimiento en tiempo real</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">Los usuarios pueden realizar seguimiento de sus viajes o envíos directamente desde la
                    app.</p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/In-app-payments.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Validación facial y biométrica</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">La plataforma cuenta con procesos de validación facial y/o biométrica según
                    configuración disponible.</p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/Customizable.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Registro y gestión de perfil</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">Usuarios y conductores pueden administrar fácilmente su información y configuración
                    dentro de la plataforma.
                </p>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card p-3 rounded-4 shadow-sm border-theme">
                <!-- Row 1: Icon + Title -->
                <div class="d-flex align-items-center mb-2">
                    <div class="icon bg-theme-light p-2 rounded-3 me-3">
                        <img src="{{ asset('assets/front/img/Real_time_live_tracking.png') }}" alt="Feature Icon"
                             width="30">
                    </div>
                    <h5 class="font-weight-bold mb-0">Gestión de solicitudes</h5>
                </div>
                <!-- Row 2: Description -->
                <p class="mb-0">Conductores y usuarios pueden revisar información relacionada con servicios y
                    solicitudes directamente desde la aplicación.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<hr class="my-0">
<footer id="contact" class="bg-light text-dark py-4">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-md-5 text-start text-md-left mb-3">
                <img src="{{ request()->get('general_settings')->website_logo != null ? asset('assets/images/website-logo-icon/'.request()->get('general_settings')->website_logo) : asset('assets/images/website-logo-icon/xisti-logo.png') }}" alt="XISTI" width="200" class="me-2">
                <p class="mt-2 text-start">
                    Nuestro equipo está disponible para brindar información y soporte relacionado con
                    XISTI APP.
                </p>

                <!-- Contact Info -->
                <div class="text-start">
                    <h5 class="mb-3">Contáctenos</h5>
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ asset('assets/front/img/location.svg') }}" class="bg-theme-color img-fluid me-2 rounded-circle p-2" width="40" />
                        <span>{{ request()->get('general_settings')->address ?? 'Medellín, Colombia' }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <img src="{{ asset('assets/front/img/mail.svg') }}" class="bg-theme-color img-fluid me-2 rounded-circle p-2" width="40" />
                        <span>{{ request()->get('general_settings')->email ?? 'soporte@xistiapp.com' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/front/img/call.svg') }}" class="bg-theme-color img-fluid me-2 rounded-circle p-2" width="40" />
                        <span>{{ request()->get('general_settings')->contact_no ?? '+57 3000000000' }}</span>
                    </div>
                </div>
            </div>

            <!-- Static Map -->
            <div class="col-md-7 text-center">
{{--                <h5 class="mb-3">Our Location</h5>--}}
                <img src="{{ asset('assets/front/img/Footer-map-image.png?v=2.0') }}" class="img-fluid rounded" alt="Static Map" />

                <!-- Social Media -->
                <h5 class="mt-4 text-start ms-2">Reach Out Us</h5>
                <div class="text-start">
                    <a href="{{ request()->get('general_settings')->instagram_link != null ? request()->get('general_settings')->instagram_link : 'https://www.instagram.com/xistiapp/'}}" class="mx-2"><img src="{{ asset('assets\front\img\instagram.svg') }}" class="bg-theme-color img-fluid me-1 rounded-circle p-2" width="40"></a>

                    <a href="{{ isset($general_setting) && request()->get('general_settings')->facebook_link != null ? request()->get('general_settings')->facebook_link : 'https://www.facebook.com/'}}" class="mx-2"><img src="{{ asset('assets\front\img\facebook.svg') }}" class="bg-theme-color img-fluid me-1 rounded-circle p-2" width="40"></a>

                    <a href="{{ isset($general_setting) && request()->get('general_settings')->linkedin_link != null ? request()->get('general_settings')->linkedin_link : 'https://www.linkedin.com/company/xistiapp/'}}" class="mx-2"><img src="{{ asset('assets\front\img\linkedin.svg') }}" class="bg-theme-color img-fluid me-1 rounded-circle p-2" width="40"></a>
                </div>

            </div>
            <!-- Divider -->
            <hr class="my-4">

            <!-- Copyright -->
            <p class="mt-3 mb-0 text-muted text-center">
                {{ request()->get('general_settings')->copy_right ?? '© XISTI APP — Todos los derechos reservados.' }}
            </p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JavaScript Bundle (Includes Popper) -->
<script src="{{ asset('assets/front/js/bootstrap_version_5.bundle.min.js') }}"></script>
</body>
</html>

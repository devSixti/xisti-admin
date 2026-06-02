@extends('admin.layout.super_admin')
@section('title')
    Dashboard
@endsection
@section('page-css')
    <style>
        .xisti-dashboard-hero {
            border: 1px solid rgba(57, 255, 20, 0.2);
            border-radius: 16px;
            background: linear-gradient(130deg, #0f172a 0%, #111827 50%, rgba(147, 51, 234, 0.35) 100%);
            color: #f9fafb;
            padding: 1.5rem;
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.2);
        }
        .xisti-dashboard-hero__title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }
        .xisti-dashboard-hero__meta {
            color: #d1d5db;
            font-size: .9rem;
            margin-bottom: 1rem;
        }
        .xisti-quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .xisti-quick-actions .btn {
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .03em;
        }
        .xisti-metric-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 26px rgba(17, 24, 39, 0.08);
            overflow: hidden;
            height: 100%;
        }
        .xisti-metric-card .card-body {
            padding: 1rem;
        }
        .xisti-metric-card__label {
            font-size: .8rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: .07em;
            margin-bottom: .45rem;
        }
        .xisti-metric-card__value {
            font-size: 1.55rem;
            line-height: 1.15;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0;
        }
        .xisti-metric-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #111827;
            font-size: 1rem;
            background: rgba(57, 255, 20, .2);
        }
        .xisti-metric-card.--revenue { border-top: 3px solid #39ff14; }
        .xisti-metric-card.--completed { border-top: 3px solid #22c55e; }
        .xisti-metric-card.--cancelled { border-top: 3px solid #ef4444; }
        .xisti-metric-card.--rides { border-top: 3px solid #9333ea; }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-home bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.nav.dashboard') }}</h5>
                            <span>{{ config('xisti.product_name', 'XISTI') }} · {{ config('xisti.tagline', 'Fácil y Seguro') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">

                        <div class="row">
                            <div class="col-xl-12 col-md-12">
                                <div class="xisti-dashboard-hero">
                                    <h5 class="xisti-dashboard-hero__title">Panel de control XISTI</h5>
                                    <p class="xisti-dashboard-hero__meta">Monitorea operación, ingresos y estado del servicio desde un solo lugar.</p>
                                    <div class="xisti-quick-actions">
                                        <a href="{{ route('get:admin:ride_list_new') }}" class="btn btn-outline-light btn-sm">Ver viajes</a>
                                        <a href="{{ route('get:admin:transport_service_provider_list', ['approved']) }}" class="btn btn-outline-light btn-sm">Ver conductores</a>
                                        <a href="{{ route('get:admin:user_list_new') }}" class="btn btn-outline-light btn-sm">Ver usuarios</a>
                                        <a href="{{ route('get:admin:general_setting') }}" class="btn btn-success btn-sm">Configurar plataforma</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card xisti-metric-card --revenue">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="col">
                                                <p class="xisti-metric-card__label">Ingresos Totales</p>
                                                <h3 class="xisti-metric-card__value">
                                                    <span class="currency"></span>&nbsp;
                                                    {{ isset($total_revenue) ? $total_revenue : 0 }}
                                                </h3>
                                            </div>
                                            <div class="xisti-metric-card__icon">
                                                <i class="fa fa-money"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card xisti-metric-card --completed">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="col">
                                                <p class="xisti-metric-card__label">Viajes Completados</p>
                                                <h3 class="xisti-metric-card__value">{{ isset($total_completed_order) ? $total_completed_order : 0 }}</h3>
                                            </div>
                                            <div class="xisti-metric-card__icon">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card xisti-metric-card --cancelled">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="col">
                                                <p class="xisti-metric-card__label">Viajes Cancelados</p>
                                                <h3 class="xisti-metric-card__value">{{ isset($total_cancelled_order) ? $total_cancelled_order : 0 }}</h3>
                                            </div>
                                            <div class="xisti-metric-card__icon">
                                                <i class="fas fa-times-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card xisti-metric-card --rides">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="col">
                                                <p class="xisti-metric-card__label">Viajes Totales</p>
                                                <h3 class="xisti-metric-card__value">{{ isset($total_order) ? $total_order : 0 }}</h3>
                                            </div>
                                            <div class="xisti-metric-card__icon">
                                                <i class="fa fa-route"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="styleSelector"></div>
@endsection
@section('page-js')
@endsection


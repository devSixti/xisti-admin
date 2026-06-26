@extends('admin.layout.super_admin')
@section('title')
    Comisiones por vehículo
@endsection
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-percent bg-c-green"></i>
                        <div class="d-inline">
                            <h5>Comisiones por tipo de vehículo</h5>
                            <span>Configure el % de comisión plataforma por categoría. Si no hay valor específico, se usa la comisión global de Service Settings.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">
                        <div class="alert alert-info">
                            Comisión global actual: <strong>{{ number_format($global_commission, 2) }}%</strong>.
                            El IVA ({{ number_format($vat_rate, 0) }}%) se calcula sobre la comisión del vehículo correspondiente.
                        </div>
                        <form method="post" action="{{ route('post:admin:update_vehicle_commission_rates') }}">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5>Tarifas por vehículo</h5>
                                </div>
                                <div class="card-block table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Vehículo</th>
                                            <th>Clave</th>
                                            <th style="width: 180px;">Comisión (%)</th>
                                            <th style="width: 100px;">Activo</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rates as $rate)
                                            <tr>
                                                <td>{{ $rate->label }}</td>
                                                <td><code>{{ $rate->variant_key }}</code></td>
                                                <td>
                                                    <input type="hidden" name="rates[{{ $rate->id }}][id]" value="{{ $rate->id }}">
                                                    <input type="number"
                                                           class="form-control"
                                                           name="rates[{{ $rate->id }}][commission_percent]"
                                                           min="0"
                                                           max="100"
                                                           step="0.01"
                                                           required
                                                           value="{{ old('rates.'.$rate->id.'.commission_percent', $rate->commission_percent) }}">
                                                </td>
                                                <td>
                                                    <select class="form-control" name="rates[{{ $rate->id }}][status]">
                                                        <option value="1" @selected((int) $rate->status === 1)>Sí</option>
                                                        <option value="0" @selected((int) $rate->status === 0)>No</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center m-t-20">
                                <a href="{{ route('get:admin:service_setting') }}" class="btn btn-secondary">Volver a Service Settings</a>
                                <button type="submit" class="btn btn-success">Guardar comisiones</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

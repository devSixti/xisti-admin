@extends('admin.layout.super_admin')
@section('title', __('admin.pages.push_notification'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.pages.push_notification') }}</h5>
                            <span>{{ __('admin.pages.push_notification') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <!-- Page body start -->
                    <div class="page-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Notificaciones automáticas por evento</h5>
                                        <span class="text-muted">Estos mensajes se envían solos cuando ocurre cada evento en la app (viajes, nueva solicitud, billetera, etc.).</span>
                                    </div>
                                    <div class="card-block">
                                        <p class="text-muted mb-3">
                                            Variables disponibles en algunos eventos:
                                            <code>{currency}</code>, <code>{price}</code>, <code>{pickup}</code>, <code>{destination}</code>, <code>{days}</code>.
                                            Sonido <strong>new_request</strong> = tono de nueva solicitud para conductores.
                                        </p>
                                        <form method="post" action="{{ route('post:admin:save_push_event_templates') }}">
                                            {{ csrf_field() }}
                                            <div class="dt-responsive table-responsive">
                                                <table class="table table-bordered table-striped" style="width:100%">
                                                    <thead>
                                                    <tr>
                                                        <th>Evento</th>
                                                        <th>Destinatario</th>
                                                        <th>Tipo app</th>
                                                        <th>Título (ES)</th>
                                                        <th>Mensaje (ES)</th>
                                                        <th>Título (EN)</th>
                                                        <th>Mensaje (EN)</th>
                                                        <th>Sonido</th>
                                                        <th>Activo</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($push_event_templates ?? [] as $event)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $event->label }}</strong><br>
                                                                <small class="text-muted">{{ $event->event_key }}</small>
                                                                @if($event->placeholders)
                                                                    <br><small>{{ $event->placeholders }}</small>
                                                                @endif
                                                            </td>
                                                            <td>{{ $event->audience === 'passenger' ? 'Cliente' : 'Conductor' }}</td>
                                                            <td>{{ $event->app_notification_type }}</td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                       name="events[{{ $event->id }}][title_es]"
                                                                       value="{{ $event->title_es }}" required>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control form-control-sm" rows="2"
                                                                          name="events[{{ $event->id }}][message_es]" required>{{ $event->message_es }}</textarea>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                       name="events[{{ $event->id }}][title_en]"
                                                                       value="{{ $event->title_en }}">
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control form-control-sm" rows="2"
                                                                          name="events[{{ $event->id }}][message_en]">{{ $event->message_en }}</textarea>
                                                            </td>
                                                            <td>
                                                                <select class="form-control form-control-sm"
                                                                        name="events[{{ $event->id }}][sound_profile]">
                                                                    <option value="default" @selected($event->sound_profile === 'default')>default</option>
                                                                    <option value="new_request" @selected($event->sound_profile === 'new_request')>new_request</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                       name="events[{{ $event->id }}][is_active]"
                                                                       value="1" @checked($event->is_active)>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <center class="mt-3">
                                                <button type="submit" class="btn btn-success m-b-0">Guardar eventos automáticos</button>
                                            </center>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <form id="main" method="post"
                                      action="{{ route('post:admin:update_push_notification') }}"
                                      enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Envío masivo (manual)</h5>
                                            <span class="text-muted">Anuncios puntuales a todos los usuarios o conductores (no ligados a un viaje).</span>
                                            {{--<a href="{{ route('get:admin:user_list') }}"--}}
                                            {{--class="btn btn-primary m-b-0 btn-right render_link"> Back</a>--}}
                                        </div>
                                        <div class="card-block">

                                            <div class="row">
                                                <div class="form-group col-sm-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">{{ __('admin.forms.notification_type') }}:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <select type="text" class="form-control"
                                                                    name="notification_type"
                                                                    required id="notification_type">
                                                                <option value="" disabled selected>Select Notification Type
                                                                </option>
                                                                <option value="1">{{ __('admin.pages.all_users_drivers') }}</option>
                                                                <option value="2">{{ __('admin.pages.all_users') }}</option>
                                                                <option value="3">{{ __('admin.pages.all_drivers') }}</option>
                                                            </select>
                                                            <span
                                                                class="error">{{ $errors->first('notification_type') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">{{ __('admin.forms.title_label') }}:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" name="title"
                                                                      id="title" required
                                                                      placeholder="{{ __('admin.forms.title_label') }}">{{ (isset($store_details)) ? $store_details->title : old('title') }}</textarea>
                                                            <span class="error">{{ $errors->first('title') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">{{ __('admin.forms.message_label') }}:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" name="message"
                                                                      id="message" required
                                                                      placeholder="{{ __('admin.forms.message_label') }}">{{ (isset($store_details)) ? $store_details->message : old('message') }}</textarea>
                                                            <span class="error">{{ $errors->first('message') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <center>
                                                <button type="submit" class="btn btn-primary m-b-0">Send</button>
                                            </center>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Historial de envíos masivos</h5>
                                    </div>
                                    <div class="card-block">

                                        <div class="dt-responsive table-responsive">
                                            <table id="new-cons" class="table table-striped table-bordered nowrap"
                                                   style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>{{ __('admin.common.no') }}</th>
                                                    <th>{{ __('admin.forms.notification_type') }}</th>
                                                    <th>{{ __('admin.columns.title') }}</th>
                                                    <th>{{ __('admin.forms.message_label') }}</th>
                                                    <th>Send Time</th>
                                                    <th data-orderable="false">{{ __('admin.columns.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(isset($push_notification))
                                                    @foreach($push_notification as $key => $notification_details)
                                                        <tr id="delete_notification_{{$notification_details->id}}">
                                                            <td>{{ $key + 1 }}</td>
                                                            {{--                                                    <td>{{ $key + 1 }}</td>--}}

                                                            <td>{{ $notification_details->notification_type }}</td>
                                                            <td>{{ $notification_details->title }}</td>
                                                            <td>{{ $notification_details->message }}</td>
                                                            <td>
                                                                    <?php $today = date('Y-m-d H:i:s'); ?>

                                                                @if($notification_details->created_at->diffInSeconds($today) <= 60)
                                                                    {{ round($notification_details->created_at->diffInSeconds($today)) }} seconds ago
                                                                @elseif($notification_details->created_at->diffInMinutes($today) <= 60)
                                                                    {{ round($notification_details->created_at->diffInMinutes($today)) }} minute{{ round($notification_details->created_at->diffInMinutes($today)) > 1 ? 's' : '' }} ago
                                                                @elseif($notification_details->created_at->diffInHours($today) < 24)
                                                                    {{ round($notification_details->created_at->diffInHours($today)) }} hour{{ round($notification_details->created_at->diffInHours($today)) > 1 ? 's' : '' }} ago
                                                                @elseif($notification_details->created_at->diffInDays($today) <= 7)
                                                                    {{ floor($notification_details->created_at->diffInDays($today)) }} day{{ floor($notification_details->created_at->diffInDays($today)) > 1 ? 's' : '' }} ago
                                                                @elseif($notification_details->created_at->diffInWeeks($today) < 4)
                                                                    {{ round($notification_details->created_at->diffInWeeks($today)) }} week{{ round($notification_details->created_at->diffInWeeks($today)) > 1 ? 's' : '' }} ago
                                                                @elseif($notification_details->created_at->diffInMonths($today) <= 12)
                                                                    {{ round($notification_details->created_at->diffInMonths($today)) }} month{{ round($notification_details->created_at->diffInMonths($today)) > 1 ? 's' : '' }} ago
                                                                @else
                                                                    {{ round($notification_details->created_at->diffInYears($today)) }} year{{ round($notification_details->created_at->diffInYears($today)) > 1 ? 's' : '' }} ago
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a class="delete" notifyid="{{$notification_details->id}}" style="margin: 0 7px;">
                                                                    <img src=" {{asset('/assets/images/template-images/remove-1.png')}} " style="width:20px; height: 20px;" title="Delete">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}"></script>
    <script>
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var id = $(this).attr('notifyid');
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'get',
                            url: '{{ route('get:admin:delete_push_notification') }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    // RemovetableRow.remove().draw();
                                    // swal("Success", result.message, "success");
                                    // location.reload();
                                    var new_id = "#delete_notification_" + id;
                                    swal("Success", "Push Notification removed successfully", "success");
                                    $(new_id).hide();
                                }else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "Your Data is safe :)", "error");
                    }
                });
        });
    </script>
@endsection


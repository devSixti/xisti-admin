@extends('admin.layout.super_admin')
@section('title')
    {{ __('admin.audit.title') }}
@endsection
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-file-text bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.audit.title') }}</h5>
                            <span>{{ __('admin.audit.subtitle') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="card">
                <div class="card-block">
                    @if(!empty($schema_missing))
                        <div class="alert alert-warning">{{ __('admin.mfa.schema_missing') }}</div>
                    @endif
                    <form method="get" class="form-inline m-b-20">
                        <input type="number" name="admin_id" class="form-control m-r-10" placeholder="{{ __('admin.audit.admin_id') }}" value="{{ request('admin_id') }}">
                        <select name="action" class="form-control m-r-10">
                            <option value="">{{ __('admin.audit.all_actions') }}</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="from" class="form-control m-r-10" value="{{ request('from') }}">
                        <input type="date" name="to" class="form-control m-r-10" value="{{ request('to') }}">
                        <button type="submit" class="btn btn-primary">{{ __('admin.audit.filter') }}</button>
                        <a href="{{ route('get:admin:audit_logs.export', request()->query()) }}" class="btn btn-secondary m-l-10">{{ __('admin.audit.export_csv') }}</a>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('admin.audit.when') }}</th>
                                <th>{{ __('admin.audit.admin') }}</th>
                                <th>{{ __('admin.audit.action') }}</th>
                                <th>{{ __('admin.audit.subject') }}</th>
                                <th>{{ __('admin.audit.ip') }}</th>
                                <th>{{ __('admin.audit.path') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->admin_email ?? $log->admin_id }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->subject_type }} #{{ $log->subject_id }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td><code>{{ $log->request_path }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">{{ __('admin.common.no_results') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

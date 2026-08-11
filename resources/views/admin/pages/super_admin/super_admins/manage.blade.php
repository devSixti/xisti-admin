@extends('admin.layout.super_admin')
@section('title', __('admin.pages.super_admins'))
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-users bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>Super Administrators</h5>
                            <span>Maximum {{ $max_super_admins }} accounts ({{ $slots_remaining }} slots remaining)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">
                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                        <div class="card">
                            <div class="card-header">
                                <h5>Super admin accounts</h5>
                                @if($slots_remaining > 0)
                                    <a href="{{ route('get:admin:add_super_admin') }}" class="btn btn-primary btn-right">Add super admin</a>
                                @endif
                            </div>
                            <div class="card-block table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ __('admin.columns.name') }}</th>
                                        <th>{{ __('admin.columns.email') }}</th>
                                        <th>{{ __('admin.common.status') }}</th>
                                        <th>MFA</th>
                                        <th>Last login</th>
                                        <th>{{ __('admin.common.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($admins as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ (int)($item->status ?? 1) === 1 ? 'Active' : 'Suspended' }}</td>
                                            <td>{{ $item->totp_enabled_at ? 'Yes' : 'No' }}</td>
                                            <td>{{ $item->last_login_at ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('get:admin:edit_super_admin', $item->id) }}">{{ __('admin.common.edit') }}</a>
                                                |
                                                <a href="{{ route('get:admin:suspend_super_admin', $item->id) }}" onclick="return confirm('Toggle suspend status?');">
                                                    {{ (int)($item->status ?? 1) === 1 ? 'Suspend' : 'Activate' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

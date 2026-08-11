@extends('admin.layout.super_admin')
@section('title')
    {{ $admin ? 'Edit' : 'Add' }} Super Admin
@endsection
@section('page-content')
    <div class="pcoded-content">
        <div class="page-body">
            <div class="card">
                <div class="card-header"><h5>{{ $admin ? 'Edit' : 'Add' }} super administrator</h5></div>
                <div class="card-block">
                    <form method="post" action="{{ $admin ? route('post:admin:update_super_admin') : route('post:admin:store_super_admin') }}">
                        @csrf
                        @if($admin)
                            <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                        @endif
                        <div class="form-group">
                            <label>{{ __('admin.columns.name') }}</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $admin->name ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('admin.columns.email') }}</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email', $admin->email ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ $admin ? 'New password (optional)' : 'Temporary password' }}</label>
                            <input type="password" name="password" class="form-control" {{ $admin ? '' : 'required' }} minlength="{{ config('admin.password_min_length', 12) }}">
                            <small class="text-muted">Min {{ config('admin.password_min_length', 12) }} chars, upper, lower, number.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('admin.common.save') }}</button>
                        <a href="{{ route('get:admin:super_admin_list') }}" class="btn btn-secondary">{{ __('admin.common.cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

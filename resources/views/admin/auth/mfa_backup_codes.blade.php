@extends('admin.layout.auth')
@section('title')
    Backup codes
@endsection
@section('page-content')
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="auth-box card">
                        <div class="card-block">
                            <h3 class="text-center txt-primary">Save your backup codes</h3>
                            <p class="text-muted">Each code works once if you lose access to your authenticator. Store them securely.</p>
                            <ul class="list-group m-b-20">
                                @foreach($backup_codes as $code)
                                    <li class="list-group-item"><code>{{ $code }}</code></li>
                                @endforeach
                            </ul>
                            <a href="{{ route('get:admin:dashboard') }}" class="btn btn-primary btn-block">Continue to dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

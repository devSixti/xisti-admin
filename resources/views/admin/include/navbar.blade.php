<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">
        <div class="navbar-logo">

            @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
                <a class="render_link" href="{{ route('get:admin:dashboard') }}">
            @endif
                @include('admin.include.brand_logo', ['class' => 'xisti-navbar-logo'])
            </a>
            <a class="mobile-menu" id="mobile-collapse">
                <i class="feather icon-toggle-right" style="cursor: pointer;"></i>
            </a>
            <a class="mobile-options waves-effect waves-light">
                <i class="feather icon-more-horizontal"></i>
            </a>

        </div>


        <div class="navbar-container container-fluid">
            <ul class="nav-left">
            </ul>
            <ul class="nav-right">
                @include('admin.include.locale_switcher')
                <li class="user-profile header-notification">
                    <div class="dropdown-primary dropdown">
                        <div class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('assets/images/website-logo-icon/user.png')}}" class="img-radius" alt="User-Profile-Image">
                                <span>{{ Illuminate\Support\Facades\Auth::guard()->user()->name }}</span>
                            <i class="feather icon-chevron-down"></i>
                        </div>
                        <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            @if(Illuminate\Support\Facades\Auth::guard("admin")->check() && Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 1)
                                <li>
                                    <a href="{{ route('get:admin:security') }}">
                                        <i class="feather icon-shield"></i> {{ __('admin.mfa.nav_security') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('get:admin:super_admin_list') }}">
                                        <i class="feather icon-users"></i> {{ __('admin.mfa.nav_super_admins') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('get:admin:audit_logs') }}">
                                        <i class="feather icon-file-text"></i> {{ __('admin.mfa.nav_audit_logs') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('get:admin:change_password') }}">
                                        <i class="feather icon-edit"></i> {{ __('admin.nav.change_password') }}
                                    </a>
                                </li>
                            @endif
                            <li>
                                @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
                                    <a href="{{ route('admin:logout',[ 'admin' ]) }}"><i class="feather icon-log-out"></i>{{ __('admin.nav.logout') }}</a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ Header ] end -->

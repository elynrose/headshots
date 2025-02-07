<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("frontend.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("frontend/permissions*") ? "c-show" : "" }} {{ request()->is("frontend/roles*") ? "c-show" : "" }} {{ request()->is("frontend/users*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("frontend.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/permissions") || request()->is("frontend/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("frontend.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/roles") || request()->is("frontend/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("frontend.users.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/users") || request()->is("frontend/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('photo_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.photos.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/photos") || request()->is("frontend/photos/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-camera-retro c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.photo.title') }}
                </a>
            </li>
        @endcan
        @can('train_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.trains.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/trains") || request()->is("frontend/trains/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.train.title') }}
                </a>
            </li>
        @endcan
        @can('generate_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.generates.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/generates") || request()->is("frontend/generates/*") ? "c-active" : "" }}">
                    <i class="fa-fw fab fa-500px c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.generate.title') }}
                </a>
            </li>
        @endcan
        @can('credit_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.credits.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/credits") || request()->is("frontend/credits/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-hand-holding-usd c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.credit.title') }}
                </a>
            </li>
        @endcan
        @can('payment_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.payments.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/payments") || request()->is("frontend/payments/*") ? "c-active" : "" }}">
                    <i class="fa-fw far fa-credit-card c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.payment.title') }}
                </a>
            </li>
        @endcan
        @can('user_alert_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("frontend.user-alerts.index") }}" class="c-sidebar-nav-link {{ request()->is("frontend/user-alerts") || request()->is("frontend/user-alerts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userAlert.title') }}
                </a>
            </li>
        @endcan
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
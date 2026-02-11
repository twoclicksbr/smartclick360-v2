@php
    $user = Auth::guard('web')->user();
@endphp

<div id="kt_app_header" class="app-header">
    <!--begin::Header container-->
    <div class="app-container container-xxl d-flex align-items-stretch justify-content-between">
        <!--begin::Logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
            <a href="{{ route('landlord.dashboard') }}">
                <img alt="Logo" src="{{ asset('assets/media/logos/default-dark.svg') }}" class="h-20px h-lg-30px app-sidebar-logo-default theme-light-show" />
                <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}" class="h-20px h-lg-30px app-sidebar-logo-default theme-dark-show" />
            </a>
        </div>
        <!--end::Logo-->

        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <!--begin::Navbar-->
            <div class="d-flex align-items-center" id="kt_header_nav">
                <!--begin::Menu-->
                <div class="d-flex align-items-center gap-5">
                    <a href="{{ route('landlord.dashboard') }}" class="btn btn-sm btn-flex btn-light {{ request()->routeIs('landlord.dashboard') ? 'btn-active-primary' : '' }}">
                        <i class="ki-outline ki-home fs-4 me-1"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('landlord.tenants.index') }}" class="btn btn-sm btn-flex btn-light {{ request()->routeIs('landlord.tenants.*') ? 'btn-active-primary' : '' }}">
                        <i class="ki-outline ki-profile-circle fs-4 me-1"></i>
                        Credenciais
                    </a>
                    <a href="#" class="btn btn-sm btn-flex btn-light">
                        <i class="ki-outline ki-chart-simple fs-4 me-1"></i>
                        Relatórios
                    </a>
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Navbar-->

            <!--begin::Toolbar wrapper-->
            <div class="d-flex align-items-stretch flex-shrink-0">
                <!--begin::Theme mode-->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                    <a href="#" class="btn btn-icon btn-active-light-primary btn-custom w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <i class="ki-outline ki-night-day theme-light-show fs-1"></i>
                        <i class="ki-outline ki-moon theme-dark-show fs-1"></i>
                    </a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-night-day fs-2"></i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-moon fs-2"></i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-screen fs-2"></i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Theme mode-->

                <!--begin::User-->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                    <!--begin::Menu wrapper-->
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40px me-3">
                                <div class="symbol-label bg-light-primary">
                                    <span class="text-primary fw-bold fs-4">{{ strtoupper(substr($user->person->first_name ?? $user->email, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="d-none d-md-flex flex-column">
                                <div class="fw-bold text-gray-900 fs-6">{{ $user->person->first_name ?? 'Usuário' }} {{ $user->person->surname ?? '' }}</div>
                                <span class="fw-semibold text-gray-500 fs-7">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                    <!--begin::User account menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <div class="symbol-label bg-light-primary">
                                        <span class="text-primary fw-bold fs-3">{{ strtoupper(substr($user->person->first_name ?? $user->email, 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">
                                        {{ $user->person->first_name ?? 'Usuário' }} {{ $user->person->surname ?? '' }}
                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Admin</span>
                                    </div>
                                    <span class="fw-semibold text-muted fs-7">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5">Meu Perfil</a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('landlord.logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link menu-link px-5 text-start w-100 text-decoration-none">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                    <!--end::User account menu-->
                    <!--end::Menu wrapper-->
                </div>
                <!--end::User-->
            </div>
            <!--end::Toolbar wrapper-->
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>

@php
    $connection = config('database.connections.tenant.database') ? 'tenant' : 'landlord';
    $scope = $connection === 'tenant' ? ['tenant', 'both'] : ['landlord', 'both'];

    $menuModules = \Illuminate\Support\Facades\DB::connection($connection)
        ->table('modules')
        ->where('type', 'module')
        ->where('status', true)
        ->whereIn('scope', $scope)
        ->whereNull('deleted_at')
        ->orderBy('order')
        ->get();
@endphp

<!--begin::Menu-->
<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
    id="kt_app_header_menu" data-kt-menu="true">

    <!--begin::Dashboard-->
    <div class="menu-item me-0 me-lg-2">
        <a class="menu-link {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}" href="{{ route('landlord.dashboard') }}">
            <span class="menu-icon">
                <i class="ki-outline ki-element-11 fs-2"></i>
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
    </div>
    <!--end::Dashboard-->

    <!--begin::Separator-->
    <div class="menu-item d-none d-lg-block">
        <div class="menu-content">
            <div class="separator mx-1 my-2"></div>
        </div>
    </div>
    <!--end::Separator-->

    <!--begin::Cadastros (dropdown com módulos dinâmicos)-->
    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
        class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2
        {{ $menuModules->contains(fn($m) => request()->segment(1) === $m->slug) ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-icon">
                <i class="ki-outline ki-burger-menu-2 fs-2"></i>
            </span>
            <span class="menu-title">Cadastros</span>
            <span class="menu-arrow d-lg-none"></span>
        </span>
        <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">
            @foreach($menuModules as $menuModule)
                <div class="menu-item">
                    <a class="menu-link {{ request()->segment(1) === $menuModule->slug ? 'active' : '' }}"
                       href="/{{ $menuModule->slug }}">
                        <span class="menu-icon">
                            <i class="{{ $menuModule->icon ?? 'ki-outline ki-element-11' }} fs-3"></i>
                        </span>
                        <span class="menu-title">{{ $menuModule->name }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <!--end::Cadastros-->

    <!--begin::Separator-->
    <div class="menu-item d-none d-lg-block">
        <div class="menu-content">
            <div class="separator mx-1 my-2"></div>
        </div>
    </div>
    <!--end::Separator-->

    <!--begin::Configurações-->
    <div class="menu-item me-0 me-lg-2">
        <a class="menu-link {{ request()->routeIs('landlord.tenants.index') ? 'active' : '' }}" href="{{ route('landlord.tenants.index') }}">
            <span class="menu-icon">
                <i class="ki-outline ki-setting-2 fs-2"></i>
            </span>
            <span class="menu-title">Credenciais</span>
        </a>
    </div>
    <!--end::Configurações-->

</div>
<!--end::Menu-->

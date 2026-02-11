<!DOCTYPE html>
<html lang="pt-BR">
<!--begin::Head-->
<head>
    <title>Dashboard - Painel Administrativo | {{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="Dashboard - Painel Administrativo" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="app-blank">
    <!--begin::Theme mode setup on page load-->
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            <div id="kt_app_header" class="app-header">
                <!--begin::Header container-->
                <div class="app-container container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Logo-->
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
                        <a href="#">
                            <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}" class="h-20px h-lg-30px app-sidebar-logo-default" />
                        </a>
                    </div>
                    <!--end::Logo-->
                    <!--begin::Header wrapper-->
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                        <!--begin::Navbar-->
                        <div class="d-flex align-items-center" id="kt_header_nav">
                            <div class="d-flex align-items-center">
                                <h1 class="text-gray-900 fw-bold mb-0 me-5">Painel Administrativo</h1>
                            </div>
                        </div>
                        <!--end::Navbar-->
                        <!--begin::Toolbar wrapper-->
                        <div class="d-flex align-items-stretch flex-shrink-0">
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
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                <div class="symbol-label bg-light-primary">
                                                    <span class="text-primary fw-bold fs-3">{{ strtoupper(substr($user->person->first_name ?? $user->email, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">{{ $user->person->first_name ?? 'Usuário' }} {{ $user->person->surname ?? '' }}</div>
                                                <span class="fw-semibold text-muted fs-7">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu separator-->
                                    <div class="separator my-2"></div>
                                    <!--end::Menu separator-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-5">
                                        <form method="POST" action="{{ route('landlord.logout') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-link menu-link px-5 text-start w-100 text-decoration-none">
                                                Sair
                                            </button>
                                        </form>
                                    </div>
                                    <!--end::Menu item-->
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
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <!--begin::Row-->
                                <div class="row gy-5 g-xl-10">
                                    <!--begin::Col-->
                                    <div class="col-xl-12">
                                        <!--begin::Card-->
                                        <div class="card card-flush h-xl-100">
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-column justify-content-center text-center p-15">
                                                <!--begin::Illustration-->
                                                <div class="mb-10">
                                                    <i class="ki-outline ki-shield-tick fs-5x text-primary"></i>
                                                </div>
                                                <!--end::Illustration-->
                                                <!--begin::Heading-->
                                                <h1 class="fw-bolder text-gray-900 mb-5">
                                                    Bem-vindo ao Painel Administrativo
                                                </h1>
                                                <!--end::Heading-->
                                                <!--begin::Subtitle-->
                                                <div class="fw-semibold fs-3 text-gray-500 mb-10">
                                                    {{ $user->person->first_name ?? 'Administrador' }}
                                                </div>
                                                <!--end::Subtitle-->
                                                <!--begin::Content-->
                                                <div class="mb-0">
                                                    <div class="card bg-light-primary mb-8">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ki-outline ki-information-5 fs-2x text-primary me-4"></i>
                                                                <div class="text-start">
                                                                    <h3 class="text-gray-900 fw-bold mb-2">Painel em Construção</h3>
                                                                    <div class="text-gray-700 fw-semibold fs-6">
                                                                        Em breve você terá acesso à gestão de tenants, planos, assinaturas e relatórios.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="text-gray-600 fw-semibold fs-6">
                                                        O backoffice completo está sendo desenvolvido para você gerenciar todos os aspectos da plataforma SmartClick360.
                                                    </p>
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Card-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                </div>
                <!--end::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>var hostUrl = "{{ asset('assets/') }}/";</script>
    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
</body>
<!--end::Body-->
</html>

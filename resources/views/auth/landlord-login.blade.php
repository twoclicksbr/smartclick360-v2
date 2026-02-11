<!DOCTYPE html>
<html lang="pt-BR">
<!--begin::Head-->
<head>
    <title>Login - Painel Administrativo | {{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <meta name="description" content="Login - Painel Administrativo" />
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
<body id="kt_body" class="app-blank bg-body">
    <!--begin::Theme mode setup on page load-->
    <script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Authentication - Sign-in-->
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
            <!--begin::Form-->
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <!--begin::Wrapper-->
                <div class="w-lg-500px p-10">
                    <!--begin::Form-->
                    <form class="form w-100" novalidate="novalidate" method="POST" action="{{ route('landlord.authenticate') }}">
                        @csrf

                        <!--begin::Heading-->
                        <div class="text-center mb-11">
                            <!--begin::Logo-->
                            <a href="#" class="mb-5 d-block">
                                <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}" class="h-40px h-lg-50px" />
                            </a>
                            <!--end::Logo-->
                            <!--begin::Title-->
                            <h1 class="text-gray-900 fw-bolder mb-3">SmartClick360</h1>
                            <!--end::Title-->
                            <!--begin::Subtitle-->
                            <div class="text-gray-500 fw-semibold fs-6">Painel Administrativo</div>
                            <!--end::Subtitle-->
                        </div>
                        <!--end::Heading-->

                        <!--begin::Input group - Email-->
                        <div class="fv-row mb-8">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Email</label>
                            <input
                                type="email"
                                placeholder="seu@email.com"
                                name="email"
                                autocomplete="email"
                                autofocus
                                class="form-control bg-transparent @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                            />
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    <i class="ki-outline ki-cross-circle fs-5 text-danger me-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Password-->
                        <div class="fv-row mb-8">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Senha</label>
                            <input
                                type="password"
                                placeholder="Digite sua senha"
                                name="password"
                                autocomplete="current-password"
                                class="form-control bg-transparent @error('password') is-invalid @enderror"
                                required
                            />
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Input group-->

                        <!--begin::Wrapper - Remember + Forgot Password-->
                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                            <!--begin::Checkbox - Remember me-->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" />
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>
                            <!--end::Checkbox-->
                            <!--begin::Link - Forgot Password-->
                            <span class="text-gray-400">
                                Esqueci minha senha (em breve)
                            </span>
                            <!--end::Link-->
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Submit button-->
                        <div class="d-grid mb-10">
                            <button type="submit" class="btn btn-primary">
                                <span class="indicator-label">Entrar</span>
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Form-->
        </div>
        <!--end::Authentication - Sign-in-->
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

@php
    use Illuminate\Support\Str;

    // Pega dados do usuário autenticado
    $authUser = Auth::guard('web')->user();
    $userName = $authUser->person->first_name ?? 'Admin';

    // Define breadcrumbs e título baseado na rota atual
    $routeName = Route::currentRouteName();
    $currentPath = request()->path();

    // Mapa de tradução de slugs para português
    $breadcrumbLabels = [
        'dashboard' => 'Dashboard',
        'tenants' => 'Credenciais',
        'plans' => 'Planos',
        'reports' => 'Relatórios',
        'settings' => 'Configurações',
    ];

    // Breadcrumbs padrão (podem ser sobrescritos nas views com @section('breadcrumbs'))
    $breadcrumbs = $breadcrumbs ?? [];

    // Se não houver breadcrumbs customizados, gera automaticamente baseado no path
    if (empty($breadcrumbs)) {
        $segments = collect(explode('/', $currentPath))->filter()->values();

        foreach ($segments as $index => $segment) {
            // Usa o mapa de tradução ou fallback para Str::title
            $label = $breadcrumbLabels[strtolower($segment)] ?? Str::title(str_replace('-', ' ', $segment));
            $url = $index < $segments->count() - 1 ? url(implode('/', $segments->take($index + 1)->toArray())) : null;

            $breadcrumbs[] = [
                'label' => $label,
                'url' => $url,
                'segment' => $segment // Adiciona o segmento original para permitir atualização via JS
            ];
        }
    }

    // Título da página (pode ser sobrescrito nas views com @section('pageTitle'))
    $pageTitle = $pageTitle ?? (isset($breadcrumbs[count($breadcrumbs) - 1]) ? $breadcrumbs[count($breadcrumbs) - 1]['label'] : 'Dashboard');

    // Descrição da página (pode ser sobrescrita nas views com @section('pageDescription'))
    $pageDescription = $pageDescription ?? "Bem-vindo de volta, {$userName}!";
@endphp

<div id="kt_app_toolbar" class="app-toolbar py-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-start">
        <!--begin::Toolbar container-->
        <div class="d-flex flex-column flex-row-fluid">
            <!--begin::Toolbar wrapper-->
            <div class="d-flex align-items-center pt-1">
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold">
                    <!--begin::Home item-->
                    <li class="breadcrumb-item text-white fw-bold lh-1">
                        <a href="{{ route('landlord.dashboard') }}" class="text-white text-hover-primary">
                            <i class="ki-outline ki-home text-gray-700 fs-6"></i>
                        </a>
                    </li>
                    <!--end::Home item-->

                    @foreach($breadcrumbs as $breadcrumb)
                        <!--begin::Separator-->
                        <li class="breadcrumb-item">
                            <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i>
                        </li>
                        <!--end::Separator-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-white fw-bold lh-1">
                            @if($breadcrumb['url'])
                                <a href="{{ $breadcrumb['url'] }}" class="text-white text-hover-primary">
                                    <span class="breadcrumb-segment" data-segment="{{ $breadcrumb['segment'] ?? '' }}">{{ $breadcrumb['label'] }}</span>
                                </a>
                            @else
                                <span class="breadcrumb-segment" data-segment="{{ $breadcrumb['segment'] ?? '' }}">{{ $breadcrumb['label'] }}</span>
                            @endif
                        </li>
                        <!--end::Item-->
                    @endforeach
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Toolbar wrapper=-->
            <!--begin::Toolbar wrapper=-->
            <div class="d-flex flex-stack flex-wrap flex-lg-nowrap gap-4 gap-lg-10 pt-13 pb-6">
                <!--begin::Page title-->
                <div class="page-title me-5">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-white fw-bold fs-2 flex-column justify-content-center my-0">
                        {{ $pageTitle }}
                        @if($pageDescription)
                            <!--begin::Description-->
                            <span class="page-desc text-gray-600 fw-semibold fs-6 pt-3">{{ $pageDescription }}</span>
                            <!--end::Description-->
                        @endif
                    </h1>
                    <!--end::Title-->
                </div>
                <!--end::Page title-->

                <!--begin::Actions-->
                <div class="d-flex align-self-center flex-center flex-shrink-0">
                    @hasSection('toolbar_actions')
                        @yield('toolbar_actions')
                    @else
                        <a href="#" class="btn btn-flex btn-sm btn-outline btn-active-color-primary btn-custom px-4" id="btn-pin-dashboard">
                            <i class="ki-outline ki-pin fs-4 me-2"></i>Fixar
                        </a>
                        <a href="#" class="btn btn-flex btn-sm btn-active-color-primary btn-outline btn-custom ms-3 px-4" id="btn-help">
                            <i class="ki-outline ki-question fs-4 me-2"></i>Ajuda
                        </a>
                    @endif
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar wrapper=-->
        </div>
        <!--end::Toolbar container=-->
    </div>
    <!--end::Toolbar container-->
</div>

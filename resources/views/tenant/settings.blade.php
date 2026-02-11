@extends('tenant.layouts.app')

@php
    use App\Models\Landlord\Tenant;

    // Breadcrumbs personalizados
    $breadcrumbs = [
        ['label' => $tenant->name, 'url' => url('/dashboard/main')],
        ['label' => 'Credenciais', 'url' => null]
    ];

    $pageTitle = 'Credenciais';
    $pageDescription = 'Gerencie as informações da sua conta';

    // Define cor do badge do plano
    $planName = $subscription ? $subscription->plan->name : 'Free';
    $badgeColor = match($planName) {
        'Starter' => 'primary',
        'Professional' => 'success',
        'Enterprise' => 'warning',
        default => 'secondary'
    };

    // Define cor do badge de status
    $statusColor = match($tenant->status) {
        'active' => 'success',
        'suspended' => 'warning',
        'cancelled' => 'danger',
        default => 'secondary'
    };

    $subscriptionStatusColor = match($subscription?->status ?? 'none') {
        'active' => 'success',
        'trial' => 'info',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        default => 'secondary'
    };
@endphp

@section('title', 'Credenciais - ' . ($tenant->name ?? 'Tenant'))

@section('content')
<div class="row g-5 g-xl-10">
    <!--begin::Col - Informações do Tenant-->
    <div class="col-xl-4">
        <!--begin::Card-->
        <div class="card card-flush h-xl-100">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Informações da Conta</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Dados cadastrais</span>
                </h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-5">
                <!--begin::Item-->
                <div class="d-flex flex-stack mb-5">
                    <div class="d-flex align-items-center me-2">
                        <div class="symbol symbol-50px me-3">
                            <div class="symbol-label bg-light-primary">
                                <i class="ki-outline ki-profile-circle fs-2x text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold text-hover-primary fs-6">Nome da Empresa</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">{{ $tenant->name }}</span>
                        </div>
                    </div>
                </div>
                <!--end::Item-->

                <!--begin::Item-->
                <div class="d-flex flex-stack mb-5">
                    <div class="d-flex align-items-center me-2">
                        <div class="symbol symbol-50px me-3">
                            <div class="symbol-label bg-light-success">
                                <i class="ki-outline ki-abstract-26 fs-2x text-success"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold text-hover-primary fs-6">Slug (Subdomínio)</span>
                            <a href="http://{{ $tenant->slug }}.smartclick360.com" target="_blank" class="text-primary fw-semibold d-block fs-7 text-hover-primary">
                                {{ $tenant->slug }}.smartclick360.com
                                <i class="ki-outline ki-arrow-up-right fs-7 ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Item-->

                <!--begin::Item-->
                <div class="d-flex flex-stack mb-5">
                    <div class="d-flex align-items-center me-2">
                        <div class="symbol symbol-50px me-3">
                            <div class="symbol-label bg-light-info">
                                <i class="ki-outline ki-status fs-2x text-info"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold text-hover-primary fs-6">Status</span>
                            <span class="d-block">
                                <span class="badge badge-{{ $statusColor }} fw-bold fs-7 mt-1">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
                <!--end::Item-->

                <!--begin::Item-->
                <div class="d-flex flex-stack mb-5">
                    <div class="d-flex align-items-center me-2">
                        <div class="symbol symbol-50px me-3">
                            <div class="symbol-label bg-light-warning">
                                <i class="ki-outline ki-calendar fs-2x text-warning"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold text-hover-primary fs-6">Data de Criação</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">
                                {{ $tenant->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                </div>
                <!--end::Item-->

                <!--begin::Item-->
                <div class="d-flex flex-stack">
                    <div class="d-flex align-items-center me-2">
                        <div class="symbol symbol-50px me-3">
                            <div class="symbol-label bg-light-danger">
                                <i class="ki-outline ki-data fs-2x text-danger"></i>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold text-hover-primary fs-6">Banco de Dados</span>
                            <span class="text-gray-500 fw-semibold d-block fs-7">{{ $tenant->database_name }}</span>
                        </div>
                    </div>
                </div>
                <!--end::Item-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->

    <!--begin::Col - Plano e Assinatura-->
    <div class="col-xl-8">
        <!--begin::Card - Plano Atual-->
        <div class="card card-flush mb-5 mb-xl-10">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Plano Atual</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Informações da assinatura</span>
                </h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-5">
                @if($subscription)
                    <div class="d-flex flex-wrap">
                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-medal-star fs-3 text-{{ $badgeColor }} me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->plan->name }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Plano</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-2 fw-bold">
                                    <span class="badge badge-{{ $subscriptionStatusColor }} fs-5">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Status</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-calendar-tick fs-3 text-success me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->starts_at ? \Carbon\Carbon::parse($subscription->starts_at)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Data Início</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-calendar-remove fs-3 text-danger me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Data Fim</div>
                        </div>
                        <!--end::Col-->

                        @if($subscription->trial_ends_at)
                            <!--begin::Col-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-timer fs-3 text-info me-2"></i>
                                    <div class="fs-2 fw-bold">{{ \Carbon\Carbon::parse($subscription->trial_ends_at)->format('d/m/Y') }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-500">Trial até</div>
                            </div>
                            <!--end::Col-->
                        @endif

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-arrows-loop fs-3 text-warning me-2"></i>
                                <div class="fs-2 fw-bold">{{ ucfirst($subscription->cycle) }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Ciclo</div>
                        </div>
                        <!--end::Col-->
                    </div>

                    <!--begin::Plan Features-->
                    <div class="separator my-5"></div>
                    <h4 class="fw-bold mb-5">Recursos do Plano</h4>
                    <div class="d-flex flex-column gap-3">
                        @php
                            $features = json_decode($subscription->plan->features, true);
                        @endphp

                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">
                                Até <strong>{{ $subscription->plan->max_users }}</strong> usuários
                            </span>
                        </div>

                        @if(isset($features['modules']))
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                <span class="fs-6 fw-semibold text-gray-700">
                                    @if($features['modules'] === 'all' || (is_array($features['modules']) && in_array('all', $features['modules'])))
                                        Todos os módulos
                                    @else
                                        Módulos: {{ is_array($features['modules']) ? implode(', ', $features['modules']) : $features['modules'] }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if(isset($features['priority_support']) && $features['priority_support'])
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                <span class="fs-6 fw-semibold text-gray-700">Suporte prioritário</span>
                            </div>
                        @endif

                        @if(isset($features['dedicated_support']) && $features['dedicated_support'])
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                <span class="fs-6 fw-semibold text-gray-700">Suporte dedicado</span>
                            </div>
                        @endif

                        @if(isset($features['api_access']) && $features['api_access'])
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                <span class="fs-6 fw-semibold text-gray-700">Acesso à API</span>
                            </div>
                        @endif
                    </div>
                    <!--end::Plan Features-->

                @else
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="ki-outline ki-information-5 fs-2tx text-warning me-4"></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Nenhuma assinatura ativa</h4>
                                <div class="fs-6 text-gray-700">
                                    Você não possui uma assinatura ativa no momento.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

        <!--begin::Card - Histórico de Assinaturas-->
        @if($subscriptionHistory->count() > 0)
        <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">Histórico de Assinaturas</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">Todas as assinaturas</span>
                </h3>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-5">
                <!--begin::Table-->
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-150px">Plano</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px">Ciclo</th>
                                <th class="min-w-120px">Data Início</th>
                                <th class="min-w-120px">Data Fim</th>
                                <th class="min-w-120px">Trial até</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptionHistory as $sub)
                            @php
                                $subStatusColor = match($sub->status) {
                                    'active' => 'success',
                                    'trial' => 'info',
                                    'expired' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="text-gray-900 fw-bold d-block fs-6">{{ $sub->plan->name }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $subStatusColor }} fw-bold">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-700 fw-semibold d-block fs-6">
                                        {{ ucfirst($sub->cycle) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-700 fw-semibold d-block fs-6">
                                        {{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-700 fw-semibold d-block fs-6">
                                        {{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-700 fw-semibold d-block fs-6">
                                        {{ $sub->trial_ends_at ? \Carbon\Carbon::parse($sub->trial_ends_at)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
        @endif
        <!--end::Card-->
    </div>
    <!--end::Col-->
</div>
@endsection

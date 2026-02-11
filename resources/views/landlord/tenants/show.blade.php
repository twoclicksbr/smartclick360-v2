@extends('landlord.layouts.app')

@section('title', 'Detalhes da Credencial')

@php
    // Assinatura ativa/trial
    $subscription = $tenant->subscriptions()
        ->whereIn('status', ['active', 'trial'])
        ->latest()
        ->first();

    $planName = $subscription && $subscription->plan ? $subscription->plan->name : 'Free';

    // Cores dos badges
    $planBadgeColor = match($planName) {
        'Starter' => 'primary',
        'Professional' => 'success',
        'Enterprise' => 'warning',
        default => 'secondary'
    };

    $statusBadgeColor = match($tenant->status) {
        'active' => 'success',
        'suspended' => 'warning',
        'cancelled' => 'danger',
        default => 'secondary'
    };
@endphp

@section('content')
<div class="container-xxl">
    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack mb-6">
        <div class="d-flex align-items-center">
            <a href="{{ route('landlord.tenants.index') }}" class="btn btn-sm btn-icon btn-light me-3">
                <i class="ki-outline ki-left fs-2"></i>
            </a>
            <h1 class="fs-2x fw-bold my-2">
                {{ $tenant->name }}
                <span class="badge badge-{{ $statusBadgeColor }} fs-7 ms-2">{{ ucfirst($tenant->status) }}</span>
            </h1>
        </div>

        <div class="d-flex flex-wrap my-2">
            <a href="http://{{ $tenant->slug }}.smartclick360-v2.test" target="_blank" class="btn btn-sm btn-success me-2">
                <i class="ki-outline ki-entrance-right fs-4"></i>
                Acessar Credencial
            </a>
            <button class="btn btn-sm btn-warning me-2">
                <i class="ki-outline ki-pencil fs-4"></i>
                Editar
            </button>
            <button class="btn btn-sm btn-danger">
                <i class="ki-outline ki-trash fs-4"></i>
                Deletar
            </button>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Row-->
    <div class="row g-5 g-xl-10">
        <!--begin::Col - Informações Básicas-->
        <div class="col-xl-4">
            <div class="card card-flush h-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Informações Básicas</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-stack mb-5">
                        <span class="text-gray-600 fw-semibold fs-6">Nome:</span>
                        <span class="text-gray-800 fw-bold fs-6">{{ $tenant->name }}</span>
                    </div>
                    <div class="separator my-3"></div>
                    <div class="d-flex flex-stack mb-5">
                        <span class="text-gray-600 fw-semibold fs-6">Slug:</span>
                        <span class="text-gray-800 fw-bold fs-6">{{ $tenant->slug }}</span>
                    </div>
                    <div class="separator my-3"></div>
                    <div class="d-flex flex-stack mb-5">
                        <span class="text-gray-600 fw-semibold fs-6">Status:</span>
                        <span class="badge badge-{{ $statusBadgeColor }}">{{ ucfirst($tenant->status) }}</span>
                    </div>
                    <div class="separator my-3"></div>
                    <div class="d-flex flex-stack mb-5">
                        <span class="text-gray-600 fw-semibold fs-6">Database:</span>
                        <span class="text-gray-800 fw-bold fs-6">{{ $tenant->database_name }}</span>
                    </div>
                    <div class="separator my-3"></div>
                    <div class="d-flex flex-stack mb-5">
                        <span class="text-gray-600 fw-semibold fs-6">Criado em:</span>
                        <span class="text-gray-800 fw-bold fs-6">{{ $tenant->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="separator my-3"></div>
                    <div class="d-flex flex-stack">
                        <span class="text-gray-600 fw-semibold fs-6">Atualizado em:</span>
                        <span class="text-gray-800 fw-bold fs-6">{{ $tenant->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Col-->

        <!--begin::Col - Assinatura Atual-->
        <div class="col-xl-8">
            @if($subscription)
            <div class="card card-flush mb-5 mb-xl-10">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Assinatura Atual</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-wrap">
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-medal-star fs-3 text-{{ $planBadgeColor }} me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->plan->name }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Plano</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-2 fw-bold">
                                    <span class="badge badge-{{ match($subscription->status) { 'active' => 'success', 'trial' => 'info', 'expired' => 'danger', default => 'secondary' } }} fs-5">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Status</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-calendar-tick fs-3 text-success me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->starts_at ? \Carbon\Carbon::parse($subscription->starts_at)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Início</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-calendar-remove fs-3 text-danger me-2"></i>
                                <div class="fs-2 fw-bold">{{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Fim</div>
                        </div>

                        @if($subscription->trial_ends_at)
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-timer fs-3 text-info me-2"></i>
                                <div class="fs-2 fw-bold">{{ \Carbon\Carbon::parse($subscription->trial_ends_at)->format('d/m/Y') }}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Trial até</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!--begin::Card - Histórico-->
            <div class="card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">Histórico de Assinaturas</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Todas as assinaturas desta credencial</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>Plano</th>
                                    <th>Status</th>
                                    <th>Ciclo</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Trial</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenant->subscriptions as $sub)
                                <tr>
                                    <td><span class="text-gray-900 fw-bold fs-6">{{ $sub->plan->name }}</span></td>
                                    <td>
                                        <span class="badge badge-{{ match($sub->status) { 'active' => 'success', 'trial' => 'info', 'expired' => 'danger', default => 'secondary' } }}">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td><span class="text-gray-700 fw-semibold">{{ ucfirst($sub->cycle) }}</span></td>
                                    <td><span class="text-gray-700 fw-semibold">{{ $sub->starts_at ? \Carbon\Carbon::parse($sub->starts_at)->format('d/m/Y') : '-' }}</span></td>
                                    <td><span class="text-gray-700 fw-semibold">{{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('d/m/Y') : '-' }}</span></td>
                                    <td><span class="text-gray-700 fw-semibold">{{ $sub->trial_ends_at ? \Carbon\Carbon::parse($sub->trial_ends_at)->format('d/m/Y') : '-' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
</div>
@endsection

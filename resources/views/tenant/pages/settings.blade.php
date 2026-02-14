@extends('tenant.layouts.app')

@section('title', 'Credenciais')

@section('content')
<!--begin::Loading skeleton-->
<div id="settings-loading" class="text-center py-20">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
    <p class="text-gray-600 mt-3">Carregando configurações...</p>
</div>
<!--end::Loading skeleton-->

<!--begin::Content (hidden until loaded)-->
<div id="settings-content" style="display: none;">
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
                                <span class="text-gray-500 fw-semibold d-block fs-7" id="tenant-name">...</span>
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
                                <a href="#" target="_blank" class="text-primary fw-semibold d-block fs-7 text-hover-primary" id="tenant-slug-link">
                                    <span id="tenant-slug">...</span>
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
                                    <span class="badge fw-bold fs-7 mt-1" id="tenant-status">...</span>
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
                                <span class="text-gray-500 fw-semibold d-block fs-7" id="tenant-created">...</span>
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
                                <span class="text-gray-500 fw-semibold d-block fs-7" id="tenant-database">...</span>
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
                <div class="card-body pt-5" id="subscription-container">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->

            <!--begin::Card - Histórico de Assinaturas-->
            <div class="card card-flush" id="subscription-history-card" style="display: none;">
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
                            <tbody id="subscription-history-tbody">
                                <!-- Linhas serão preenchidas via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
    </div>
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('settings-loading');
    const contentElement = document.getElementById('settings-content');

    fetch('/api/v1/settings', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao carregar configurações');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const tenant = data.data.tenant;
            const subscription = data.data.subscription;
            const subscriptionHistory = data.data.subscriptionHistory;

            // Cores dos badges
            const statusBadgeColors = {
                'active': 'success',
                'suspended': 'warning',
                'cancelled': 'danger'
            };

            const planBadgeColors = {
                'Starter': 'primary',
                'Professional': 'success',
                'Enterprise': 'warning'
            };

            const subscriptionStatusColors = {
                'active': 'success',
                'trial': 'info',
                'expired': 'danger',
                'cancelled': 'secondary'
            };

            // Preenche informações do tenant
            document.getElementById('tenant-name').textContent = tenant.name;
            document.getElementById('tenant-slug').textContent = tenant.slug + '.smartclick360.com';
            document.getElementById('tenant-slug-link').href = `http://${tenant.slug}.smartclick360.com`;

            const statusColor = statusBadgeColors[tenant.status] || 'secondary';
            const statusBadge = document.getElementById('tenant-status');
            statusBadge.textContent = tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1);
            statusBadge.className = `badge badge-${statusColor} fw-bold fs-7 mt-1`;

            const createdAt = new Date(tenant.created_at);
            document.getElementById('tenant-created').textContent = createdAt.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            document.getElementById('tenant-database').textContent = tenant.database_name;

            // Preenche assinatura atual
            const subscriptionContainer = document.getElementById('subscription-container');

            if (subscription) {
                const planName = subscription.plan.name;
                const planColor = planBadgeColors[planName] || 'secondary';
                const subStatusColor = subscriptionStatusColors[subscription.status] || 'secondary';

                let subscriptionHTML = `
                    <div class="d-flex flex-wrap">
                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-medal-star fs-3 text-${planColor} me-2"></i>
                                <div class="fs-2 fw-bold">${planName}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Plano</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-2 fw-bold">
                                    <span class="badge badge-${subStatusColor} fs-5">
                                        ${subscription.status.charAt(0).toUpperCase() + subscription.status.slice(1)}
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
                                <div class="fs-2 fw-bold">${subscription.starts_at ? new Date(subscription.starts_at).toLocaleDateString('pt-BR') : 'N/A'}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Data Início</div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-calendar-remove fs-3 text-danger me-2"></i>
                                <div class="fs-2 fw-bold">${subscription.ends_at ? new Date(subscription.ends_at).toLocaleDateString('pt-BR') : 'N/A'}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Data Fim</div>
                        </div>
                        <!--end::Col-->
                `;

                if (subscription.trial_ends_at) {
                    subscriptionHTML += `
                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-timer fs-3 text-info me-2"></i>
                                <div class="fs-2 fw-bold">${new Date(subscription.trial_ends_at).toLocaleDateString('pt-BR')}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Trial até</div>
                        </div>
                        <!--end::Col-->
                    `;
                }

                subscriptionHTML += `
                        <!--begin::Col-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-arrows-loop fs-3 text-warning me-2"></i>
                                <div class="fs-2 fw-bold">${subscription.cycle.charAt(0).toUpperCase() + subscription.cycle.slice(1)}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Ciclo</div>
                        </div>
                        <!--end::Col-->
                    </div>

                    <!--begin::Plan Features-->
                    <div class="separator my-5"></div>
                    <h4 class="fw-bold mb-5">Recursos do Plano</h4>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">
                                Até <strong>${subscription.plan.max_users}</strong> usuários
                            </span>
                        </div>
                `;

                // Features do plano
                const features = subscription.plan.features;

                if (features.modules) {
                    const modulesText = (features.modules === 'all' || (Array.isArray(features.modules) && features.modules.includes('all')))
                        ? 'Todos os módulos'
                        : `Módulos: ${Array.isArray(features.modules) ? features.modules.join(', ') : features.modules}`;

                    subscriptionHTML += `
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">${modulesText}</span>
                        </div>
                    `;
                }

                if (features.priority_support) {
                    subscriptionHTML += `
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">Suporte prioritário</span>
                        </div>
                    `;
                }

                if (features.dedicated_support) {
                    subscriptionHTML += `
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">Suporte dedicado</span>
                        </div>
                    `;
                }

                if (features.api_access) {
                    subscriptionHTML += `
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                            <span class="fs-6 fw-semibold text-gray-700">Acesso à API</span>
                        </div>
                    `;
                }

                subscriptionHTML += `
                    </div>
                    <!--end::Plan Features-->
                `;

                subscriptionContainer.innerHTML = subscriptionHTML;
            } else {
                subscriptionContainer.innerHTML = `
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
                `;
            }

            // Preenche histórico de assinaturas
            if (subscriptionHistory && subscriptionHistory.length > 0) {
                const tbody = document.getElementById('subscription-history-tbody');
                let historyHTML = '';

                subscriptionHistory.forEach(sub => {
                    const subStatusColor = subscriptionStatusColors[sub.status] || 'secondary';

                    historyHTML += `
                        <tr>
                            <td>
                                <span class="text-gray-900 fw-bold d-block fs-6">${sub.plan.name}</span>
                            </td>
                            <td>
                                <span class="badge badge-${subStatusColor} fw-bold">
                                    ${sub.status.charAt(0).toUpperCase() + sub.status.slice(1)}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700 fw-semibold d-block fs-6">
                                    ${sub.cycle.charAt(0).toUpperCase() + sub.cycle.slice(1)}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700 fw-semibold d-block fs-6">
                                    ${sub.starts_at ? new Date(sub.starts_at).toLocaleDateString('pt-BR') : '-'}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700 fw-semibold d-block fs-6">
                                    ${sub.ends_at ? new Date(sub.ends_at).toLocaleDateString('pt-BR') : '-'}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700 fw-semibold d-block fs-6">
                                    ${sub.trial_ends_at ? new Date(sub.trial_ends_at).toLocaleDateString('pt-BR') : '-'}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = historyHTML;
                document.getElementById('subscription-history-card').style.display = 'block';
            }

            // Esconde loading e mostra conteúdo
            loadingElement.style.display = 'none';
            contentElement.style.display = 'block';
        } else {
            throw new Error(data.message || 'Erro ao carregar dados');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar configurações:', error);
        loadingElement.innerHTML = `
            <div class="text-center py-20">
                <i class="ki-outline ki-information-5 fs-3x text-danger mb-3"></i>
                <p class="text-danger fw-bold">Erro ao carregar configurações</p>
                <p class="text-gray-600">${error.message}</p>
                <button onclick="location.reload()" class="btn btn-sm btn-primary mt-3">
                    <i class="ki-outline ki-arrows-circle fs-3"></i>
                    Tentar Novamente
                </button>
            </div>
        `;
    });
});
</script>
@endpush

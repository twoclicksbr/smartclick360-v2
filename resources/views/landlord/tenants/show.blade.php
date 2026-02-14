@extends('landlord.layouts.app')

@section('title', 'Detalhes da Credencial')

@section('content')
<div class="container-xxl">
    <!--begin::Loading skeleton-->
    <div id="tenant-loading" class="text-center py-20">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="text-gray-600 mt-3">Carregando detalhes...</p>
    </div>
    <!--end::Loading skeleton-->

    <!--begin::Content (hidden until loaded)-->
    <div id="tenant-content" style="display: none;">
        <!--begin::Toolbar-->
        <div class="d-flex flex-wrap flex-stack mb-6">
            <div class="d-flex align-items-center">
                <a href="{{ route('landlord.tenants.index') }}" class="btn btn-sm btn-icon btn-light me-3">
                    <i class="ki-outline ki-left fs-2"></i>
                </a>
                <h1 class="fs-2x fw-bold my-2">
                    <span id="tenant-name-header">...</span>
                    <span class="badge fs-7 ms-2" id="tenant-status-badge">...</span>
                </h1>
            </div>

            <div class="d-flex flex-wrap my-2">
                <a href="#" target="_blank" class="btn btn-sm btn-success me-2" id="tenant-access-link">
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
                            <span class="text-gray-800 fw-bold fs-6" id="tenant-name">...</span>
                        </div>
                        <div class="separator my-3"></div>
                        <div class="d-flex flex-stack mb-5">
                            <span class="text-gray-600 fw-semibold fs-6">Slug:</span>
                            <span class="text-gray-800 fw-bold fs-6" id="tenant-slug">...</span>
                        </div>
                        <div class="separator my-3"></div>
                        <div class="d-flex flex-stack mb-5">
                            <span class="text-gray-600 fw-semibold fs-6">Status:</span>
                            <span class="badge" id="tenant-status">...</span>
                        </div>
                        <div class="separator my-3"></div>
                        <div class="d-flex flex-stack mb-5">
                            <span class="text-gray-600 fw-semibold fs-6">Database:</span>
                            <span class="text-gray-800 fw-bold fs-6" id="tenant-database">...</span>
                        </div>
                        <div class="separator my-3"></div>
                        <div class="d-flex flex-stack mb-5">
                            <span class="text-gray-600 fw-semibold fs-6">Criado em:</span>
                            <span class="text-gray-800 fw-bold fs-6" id="tenant-created">...</span>
                        </div>
                        <div class="separator my-3"></div>
                        <div class="d-flex flex-stack">
                            <span class="text-gray-600 fw-semibold fs-6">Atualizado em:</span>
                            <span class="text-gray-800 fw-bold fs-6" id="tenant-updated">...</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col - Assinatura Atual-->
            <div class="col-xl-8">
                <div id="subscription-current-card" style="display: none;">
                    <div class="card card-flush mb-5 mb-xl-10">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-800">Assinatura Atual</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div class="d-flex flex-wrap" id="subscription-details">
                                <!-- Detalhes da assinatura serão preenchidos via JS -->
                            </div>
                        </div>
                    </div>
                </div>

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
                                <tbody id="subscriptions-history">
                                    <!-- Histórico será preenchido via JS -->
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
    <!--end::Content-->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('tenant-loading');
    const contentElement = document.getElementById('tenant-content');

    // Extrai o code da URL: /tenants/{code}
    const code = window.location.pathname.split('/').pop();

    fetch('/api/v1/landlord/tenants/' + code, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao carregar detalhes');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const tenant = data.data.tenant;

            // Cores dos badges
            const planBadgeColors = {
                'Starter': 'primary',
                'Professional': 'success',
                'Enterprise': 'warning'
            };

            const statusBadgeColors = {
                'active': 'success',
                'suspended': 'warning',
                'cancelled': 'danger'
            };

            const subscriptionStatusColors = {
                'active': 'success',
                'trial': 'info',
                'expired': 'danger'
            };

            const statusColor = statusBadgeColors[tenant.status] || 'secondary';

            // Preenche informações básicas
            document.getElementById('tenant-name-header').textContent = tenant.name;
            document.getElementById('tenant-status-badge').textContent = tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1);
            document.getElementById('tenant-status-badge').className = `badge badge-${statusColor} fs-7 ms-2`;

            document.getElementById('tenant-access-link').href = `http://${tenant.slug}.smartclick360-v2.test`;

            document.getElementById('tenant-name').textContent = tenant.name;
            document.getElementById('tenant-slug').textContent = tenant.slug;
            document.getElementById('tenant-status').textContent = tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1);
            document.getElementById('tenant-status').className = `badge badge-${statusColor}`;
            document.getElementById('tenant-database').textContent = tenant.database_name;

            // Formata datas
            const createdAt = new Date(tenant.created_at);
            const updatedAt = new Date(tenant.updated_at);
            document.getElementById('tenant-created').textContent = createdAt.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('tenant-updated').textContent = updatedAt.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Assinatura atual (ativa ou trial)
            const subscriptions = tenant.subscriptions || [];
            const currentSubscription = subscriptions.find(s => s.status === 'active' || s.status === 'trial');

            if (currentSubscription) {
                const planName = currentSubscription.plan ? currentSubscription.plan.name : 'Free';
                const planColor = planBadgeColors[planName] || 'secondary';
                const subStatusColor = subscriptionStatusColors[currentSubscription.status] || 'secondary';

                let detailsHTML = `
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-medal-star fs-3 text-${planColor} me-2"></i>
                            <div class="fs-2 fw-bold">${planName}</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-500">Plano</div>
                    </div>

                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="fs-2 fw-bold">
                                <span class="badge badge-${subStatusColor} fs-5">${currentSubscription.status.charAt(0).toUpperCase() + currentSubscription.status.slice(1)}</span>
                            </div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-500">Status</div>
                    </div>

                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-calendar-tick fs-3 text-success me-2"></i>
                            <div class="fs-2 fw-bold">${currentSubscription.starts_at ? new Date(currentSubscription.starts_at).toLocaleDateString('pt-BR') : 'N/A'}</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-500">Início</div>
                    </div>

                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-outline ki-calendar-remove fs-3 text-danger me-2"></i>
                            <div class="fs-2 fw-bold">${currentSubscription.ends_at ? new Date(currentSubscription.ends_at).toLocaleDateString('pt-BR') : 'N/A'}</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-500">Fim</div>
                    </div>
                `;

                if (currentSubscription.trial_ends_at) {
                    detailsHTML += `
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-timer fs-3 text-info me-2"></i>
                                <div class="fs-2 fw-bold">${new Date(currentSubscription.trial_ends_at).toLocaleDateString('pt-BR')}</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Trial até</div>
                        </div>
                    `;
                }

                document.getElementById('subscription-details').innerHTML = detailsHTML;
                document.getElementById('subscription-current-card').style.display = 'block';
            }

            // Histórico de assinaturas
            const historyHTML = subscriptions.map(sub => {
                const planName = sub.plan ? sub.plan.name : 'Free';
                const subStatusColor = subscriptionStatusColors[sub.status] || 'secondary';

                return `
                    <tr>
                        <td><span class="text-gray-900 fw-bold fs-6">${planName}</span></td>
                        <td>
                            <span class="badge badge-${subStatusColor}">
                                ${sub.status.charAt(0).toUpperCase() + sub.status.slice(1)}
                            </span>
                        </td>
                        <td><span class="text-gray-700 fw-semibold">${sub.cycle.charAt(0).toUpperCase() + sub.cycle.slice(1)}</span></td>
                        <td><span class="text-gray-700 fw-semibold">${sub.starts_at ? new Date(sub.starts_at).toLocaleDateString('pt-BR') : '-'}</span></td>
                        <td><span class="text-gray-700 fw-semibold">${sub.ends_at ? new Date(sub.ends_at).toLocaleDateString('pt-BR') : '-'}</span></td>
                        <td><span class="text-gray-700 fw-semibold">${sub.trial_ends_at ? new Date(sub.trial_ends_at).toLocaleDateString('pt-BR') : '-'}</span></td>
                    </tr>
                `;
            }).join('');

            document.getElementById('subscriptions-history').innerHTML = historyHTML || '<tr><td colspan="6" class="text-center text-gray-500">Nenhuma assinatura encontrada</td></tr>';

            // Esconde loading e mostra conteúdo
            loadingElement.style.display = 'none';
            contentElement.style.display = 'block';
        } else {
            throw new Error(data.message || 'Erro ao carregar dados');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar detalhes:', error);
        loadingElement.innerHTML = `
            <div class="text-center py-20">
                <i class="ki-outline ki-information-5 fs-3x text-danger mb-3"></i>
                <p class="text-danger fw-bold">Erro ao carregar detalhes da credencial</p>
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

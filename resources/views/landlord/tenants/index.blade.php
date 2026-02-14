@extends('landlord.layouts.app')

@section('title', 'Gestão de Credenciais')

@section('content')
<div class="container-xxl">
    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack mb-6">
        <!--begin::Title-->
        <h1 class="fs-2x fw-bold my-2">
            Gestão de Credenciais
            <span class="fs-6 text-gray-500 fw-semibold ms-1" id="tenants-count">...</span>
        </h1>
        <!--end::Title-->

        <!--begin::Actions-->
        <div class="d-flex flex-wrap my-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_tenant">
                <i class="ki-outline ki-plus fs-2"></i>
                Criar Nova Credencial
            </button>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Loading skeleton-->
    <div id="tenants-loading" class="text-center py-20">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="text-gray-600 mt-3">Carregando credenciais...</p>
    </div>
    <!--end::Loading skeleton-->

    <!--begin::Row-->
    <div class="row g-6 g-xl-9" id="tenants-grid" style="display: none;">
        <!-- Cards serão gerados dinamicamente via JavaScript -->
    </div>
    <!--end::Row-->

    <!--begin::Pagination-->
    <div id="tenants-pagination" style="display: none;">
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-semibold text-gray-700" id="pagination-info"></div>

            <ul class="pagination">
                <li class="page-item previous disabled">
                    <a href="#" class="page-link"><i class="previous"></i></a>
                </li>
                <li class="page-item active">
                    <a href="#" class="page-link">1</a>
                </li>
                <li class="page-item next disabled">
                    <a href="#" class="page-link"><i class="next"></i></a>
                </li>
            </ul>
        </div>
    </div>
    <!--end::Pagination-->
</div>

<!--begin::Modal - Criar Credencial-->
<div class="modal fade" id="kt_modal_create_tenant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Criar Nova Credencial</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <p class="text-gray-600">
                    Para criar uma nova credencial, utilize o formulário de registro público em:
                </p>
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mt-5">
                    <i class="ki-outline ki-information-5 fs-2tx text-primary me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-700">
                                <a href="{{ url('/register') }}" target="_blank" class="fw-bold text-primary">
                                    {{ url('/register') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <a href="{{ url('/register') }}" target="_blank" class="btn btn-primary">
                    Ir para Registro
                </a>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('tenants-loading');
    const gridElement = document.getElementById('tenants-grid');
    const paginationElement = document.getElementById('tenants-pagination');
    const countElement = document.getElementById('tenants-count');
    const paginationInfo = document.getElementById('pagination-info');

    fetch('/api/v1/landlord/tenants', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao carregar credenciais');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const tenants = data.data.tenants || [];

            // Atualiza contador
            const count = tenants.length;
            const countText = count === 1 ? 'credencial' : 'credenciais';
            countElement.textContent = `(${count} ${countText})`;

            // Se não houver tenants, mostra empty state
            if (count === 0) {
                gridElement.innerHTML = `
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center py-20">
                                <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                                <h3 class="text-gray-800 fw-bold mb-2">Nenhuma credencial encontrada</h3>
                                <p class="text-gray-500 fs-6 mb-6">Comece criando sua primeira credencial</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_tenant">
                                    <i class="ki-outline ki-plus fs-2"></i>
                                    Criar Primeira Credencial
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Gera os cards
                gridElement.innerHTML = tenants.map(tenant => createTenantCard(tenant)).join('');

                // Atualiza paginação
                paginationInfo.textContent = `Mostrando ${count} ${count === 1 ? 'resultado' : 'resultados'}`;
                paginationElement.style.display = 'block';
            }

            // Esconde loading e mostra conteúdo
            loadingElement.style.display = 'none';
            gridElement.style.display = 'flex';
        } else {
            throw new Error(data.message || 'Erro ao carregar dados');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar credenciais:', error);
        loadingElement.innerHTML = `
            <div class="text-center py-20">
                <i class="ki-outline ki-information-5 fs-3x text-danger mb-3"></i>
                <p class="text-danger fw-bold">Erro ao carregar credenciais</p>
                <p class="text-gray-600">${error.message}</p>
                <button onclick="location.reload()" class="btn btn-sm btn-primary mt-3">
                    <i class="ki-outline ki-arrows-circle fs-3"></i>
                    Tentar Novamente
                </button>
            </div>
        `;
    });

    function createTenantCard(tenant) {
        // Pega a assinatura ativa/trial
        const subscription = tenant.subscriptions && tenant.subscriptions.length > 0 ? tenant.subscriptions[0] : null;
        const planName = subscription && subscription.plan ? subscription.plan.name : 'Free';

        // Define cor do badge do plano
        const planBadgeColors = {
            'Starter': 'primary',
            'Professional': 'success',
            'Enterprise': 'warning'
        };
        const planBadgeColor = planBadgeColors[planName] || 'secondary';

        // Define cor do badge de status
        const statusBadgeColors = {
            'active': 'success',
            'suspended': 'warning',
            'cancelled': 'danger'
        };
        const statusBadgeColor = statusBadgeColors[tenant.status] || 'secondary';

        // Define cor do card baseado no status
        const cardBorderClasses = {
            'active': 'border-success',
            'suspended': 'border-warning',
            'cancelled': 'border-danger'
        };
        const cardBorderClass = cardBorderClasses[tenant.status] || 'border-gray-300';

        // Iniciais do tenant
        const initials = tenant.name.substring(0, 2).toUpperCase();

        // Calcula dias desde a criação
        const createdAt = new Date(tenant.created_at);
        const now = new Date();
        const daysActive = Math.floor((now - createdAt) / (1000 * 60 * 60 * 24));

        // Formata data de criação
        const createdDateFormatted = createdAt.toLocaleDateString('pt-BR');

        // Status formatado
        const statusFormatted = tenant.status.charAt(0).toUpperCase() + tenant.status.slice(1);

        return `
            <div class="col-md-6 col-xl-4">
                <div class="card border ${cardBorderClass} h-100">
                    <div class="card-header border-0 pt-9">
                        <div class="card-title m-0">
                            <div class="symbol symbol-50px w-50px bg-light-${planBadgeColor}">
                                <span class="symbol-label fs-5 fw-bold text-${planBadgeColor}">${initials}</span>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <span class="badge badge-light-${statusBadgeColor} fw-bold me-auto px-4 py-3">
                                ${statusFormatted}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-9">
                        <div class="fs-3 fw-bold text-gray-900">${tenant.name}</div>
                        <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7">
                            <i class="ki-outline ki-abstract-26 fs-6 me-1"></i>
                            ${tenant.slug}.smartclick360.com
                        </p>
                        <div class="d-flex flex-wrap mb-5">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${planName}</div>
                                <div class="fw-semibold text-gray-500">Plano</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                <div class="fs-6 text-gray-800 fw-bold">${daysActive} dias</div>
                                <div class="fw-semibold text-gray-500">Ativo há</div>
                            </div>
                        </div>
                        <div class="h-4px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="Assinaturas">
                            <div class="bg-${planBadgeColor} rounded h-4px" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex flex-column text-gray-600">
                            <div class="d-flex align-items-center py-2">
                                <span class="bullet bullet-dot bg-primary me-3"></span>
                                <i class="ki-outline ki-calendar fs-6 me-2"></i>
                                Criado em ${createdDateFormatted}
                            </div>
                            <div class="d-flex align-items-center py-2">
                                <span class="bullet bullet-dot bg-success me-3"></span>
                                <i class="ki-outline ki-book fs-6 me-2"></i>
                                ${tenant.subscriptions_count} ${tenant.subscriptions_count === 1 ? 'assinatura' : 'assinaturas'}
                            </div>
                            <div class="d-flex align-items-center py-2">
                                <span class="bullet bullet-dot bg-warning me-3"></span>
                                <i class="ki-outline ki-data fs-6 me-2"></i>
                                DB: ${tenant.database_name}
                            </div>
                        </div>
                        <div class="d-flex flex-stack mt-7">
                            <a href="/tenants/${tenant.encoded_id}" class="btn btn-sm btn-light-primary">
                                <i class="ki-outline ki-eye fs-5"></i>
                                Detalhes
                            </a>
                            <div class="btn-group" role="group">
                                <a href="http://${tenant.slug}.smartclick360-v2.test" target="_blank" class="btn btn-sm btn-icon btn-light-success" title="Acessar Credencial">
                                    <i class="ki-outline ki-entrance-right fs-5"></i>
                                </a>
                                <button class="btn btn-sm btn-icon btn-light-warning" title="Editar">
                                    <i class="ki-outline ki-pencil fs-5"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger" title="Deletar">
                                    <i class="ki-outline ki-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
@endpush

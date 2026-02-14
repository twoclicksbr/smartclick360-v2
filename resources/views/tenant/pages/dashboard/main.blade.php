@extends('tenant.layouts.app')

@section('title', 'Dashboard')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => url('/dashboard/main')],
        ['label' => 'Visão Geral', 'url' => null]
    ];
    $pageTitle = 'Visão Geral';
    $pageDescription = "Painel principal do sistema";
@endphp

@section('content')
<!--begin::Row-->
<div class="row g-5 g-xl-8">
	<!--begin::Col-->
	<div class="col-xl-12">
		<!--begin::Card-->
		<div class="card card-flush h-xl-100">
			<!--begin::Card body-->
			<div class="card-body d-flex flex-column justify-content-center text-center p-10 p-lg-15">
				<!--begin::Loading skeleton-->
				<div id="dashboard-loading" class="text-center">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Carregando...</span>
					</div>
					<p class="text-gray-600 mt-3">Carregando dashboard...</p>
				</div>
				<!--end::Loading skeleton-->

				<!--begin::Content (hidden until loaded)-->
				<div id="dashboard-content" style="display: none;">
					<!--begin::Icon-->
					<div class="mb-7">
						<i class="ki-outline ki-rocket fs-5x text-primary"></i>
					</div>
					<!--end::Icon-->
					<!--begin::Title-->
					<h1 class="fw-bolder text-gray-900 mb-5">
						Bem-vindo, <span id="user-name">...</span>!
					</h1>
					<!--end::Title-->
					<!--begin::Subtitle-->
					<div class="fw-semibold fs-3 text-gray-500 mb-10">
						<span id="tenant-name">...</span>
					</div>
					<!--end::Subtitle-->
					<!--begin::Message-->
					<div class="mb-0">
						<div class="card bg-light-primary border-0 mb-8">
							<div class="card-body p-8">
								<div class="d-flex align-items-start">
									<div class="flex-shrink-0 me-5">
										<i class="ki-outline ki-information-5 fs-2x text-primary"></i>
									</div>
									<div class="text-start flex-grow-1">
										<h3 class="text-gray-900 fw-bold mb-3">Dashboard em Construção</h3>
										<div class="text-gray-700 fw-semibold fs-6">
											Estamos preparando seu painel de controle. Em breve você terá acesso a métricas, relatórios e muito mais.
										</div>
									</div>
								</div>
							</div>
						</div>
						<p class="text-gray-600 fw-semibold fs-6 mb-8">
							Nossa equipe está trabalhando para entregar a melhor experiência para você gerenciar seu negócio.
						</p>
						<!--begin::Logout button-->
						<form method="POST" action="{{ url('/logout') }}" class="d-inline">
							@csrf
							<button type="submit" class="btn btn-light-danger fw-bold">
								<i class="ki-outline ki-exit-right fs-2"></i>
								<span class="ms-2">Sair</span>
							</button>
						</form>
						<!--end::Logout button-->
					</div>
					<!--end::Message-->
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('dashboard-loading');
    const contentElement = document.getElementById('dashboard-content');

    fetch('/api/v1/dashboard', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao carregar dashboard');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Preenche os dados do usuário e tenant
            const userName = data.data.user?.person?.first_name || 'Usuário';
            const tenantName = data.data.tenant?.name || 'Empresa';

            document.getElementById('user-name').textContent = userName;
            document.getElementById('tenant-name').textContent = tenantName;

            // Atualiza o título da página
            document.title = `Dashboard - ${tenantName}`;

            // Esconde loading e mostra conteúdo
            loadingElement.style.display = 'none';
            contentElement.style.display = 'block';
        } else {
            throw new Error(data.message || 'Erro ao carregar dados');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar dashboard:', error);
        loadingElement.innerHTML = `
            <div class="text-center">
                <i class="ki-outline ki-information-5 fs-3x text-danger mb-3"></i>
                <p class="text-danger fw-bold">Erro ao carregar o dashboard</p>
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

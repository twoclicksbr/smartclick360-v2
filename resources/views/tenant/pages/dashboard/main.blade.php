@extends('tenant.layouts.app')

@section('title', 'Dashboard - ' . ($tenant->name ?? 'Tenant'))

@php
    $breadcrumbs = [
        ['label' => $tenant->name, 'url' => url('/dashboard/main')],
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
				<!--begin::Icon-->
				<div class="mb-7">
					<i class="ki-outline ki-rocket fs-5x text-primary"></i>
				</div>
				<!--end::Icon-->
				<!--begin::Title-->
				<h1 class="fw-bolder text-gray-900 mb-5">
					Bem-vindo, {{ $user->person->first_name ?? 'Usuário' }}!
				</h1>
				<!--end::Title-->
				<!--begin::Subtitle-->
				<div class="fw-semibold fs-3 text-gray-500 mb-10">
					{{ $tenant->name ?? 'Empresa' }}
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
			<!--end::Card body-->
		</div>
		<!--end::Card-->
	</div>
	<!--end::Col-->
</div>
<!--end::Row-->
@endsection

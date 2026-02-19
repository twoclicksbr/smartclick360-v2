@extends('landlord.layouts.app')

@section('page_title', 'Dashboard')
@section('page_description', 'Painel Administrativo')

@section('content')

{{-- Bem-vindo --}}
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0 text-center">
        <div class="mb-5">
            <i class="ki-duotone ki-shield-tick fs-3x text-primary">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <h1 class="fw-bold mb-3">Bem-vindo ao Painel Administrativo</h1>
        <p class="text-gray-500 fs-5 mb-8">{{ Auth::guard('web')->user()->person->first_name ?? 'Admin' }}</p>
    </div>
</div>

{{-- Aviso de construção --}}
<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
    <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-column">
        <h4 class="mb-1 text-dark">Painel em Construção</h4>
        <span>Em breve você terá acesso à gestão de tenants, planos, assinaturas e relatórios.</span>
    </div>
</div>

@endsection

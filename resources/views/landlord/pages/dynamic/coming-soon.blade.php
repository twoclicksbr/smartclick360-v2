@extends('landlord.layouts.app')

@section('title', $config->name)

@section('toolbar')
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            {{ $config->name }}
        </h1>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body p-10 p-lg-15 text-center">
            <div class="mb-10">
                <i class="ki-outline ki-wrench fs-5tx text-gray-300"></i>
            </div>
            <h2 class="fw-bold text-gray-800 mb-5">Módulo em Construção</h2>
            <p class="text-gray-600 fs-5 mb-10">
                O módulo <strong>{{ $config->name }}</strong> está sendo desenvolvido e estará disponível em breve.
            </p>
            <a href="/dashboard" class="btn btn-primary">
                <i class="ki-outline ki-arrow-left fs-2"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>
@endsection

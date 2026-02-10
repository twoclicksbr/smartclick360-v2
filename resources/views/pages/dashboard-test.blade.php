@extends('layouts.dashboard')

@section('title', 'Dashboard Test')

@section('page-title', 'Dashboard Test')
@section('page-description', 'Testing the dashboard layout')

@section('content')
<div class="row g-5 g-xl-8">
    <div class="col-xl-12">
        <div class="card card-flush h-md-100">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <h1 class="display-1 fw-bold text-gray-900">Dashboard Test</h1>
                <p class="fs-3 text-muted">Layout dashboard está funcionando corretamente!</p>
                <div class="mt-10">
                    <a href="{{ route('home') }}" class="btn btn-primary">Voltar para Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

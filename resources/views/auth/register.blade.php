@extends('layouts.landing')

@section('title', 'Register')

@section('content')
<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center p-12">
    <!--begin::Card-->
    <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-800px p-20 shadow">
        <!--begin::Wrapper-->
        <div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
            <!--begin::Form-->
            <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" method="POST" action="{{ route('register.store') }}">
                @csrf

                <!--begin::Heading-->
                <div class="text-center mb-11">
                    <h1 class="text-gray-900 fw-bolder mb-3">Crie sua conta</h1>
                    <div class="text-gray-500 fw-semibold fs-6">Comece seu trial gratuito de 7 dias</div>
                </div>
                <!--end::Heading-->

                <!--begin::Section Dados da Empresa-->
                <div class="mb-10">
                    <h3 class="text-gray-800 fw-bold mb-6">Dados da Empresa</h3>

                    <!--begin::Input group - Company Name-->
                    <div class="fv-row mb-8">
                        <label class="form-label fw-semibold text-gray-900 fs-6">Nome da empresa</label>
                        <input type="text" placeholder="Digite o nome da sua empresa" name="company_name" id="company_name" autocomplete="off" class="form-control bg-transparent @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required />
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Slug-->
                    <div class="fv-row mb-8">
                        <label class="form-label fw-semibold text-gray-900 fs-6">Subdomínio (slug)</label>
                        <input type="text" placeholder="seu-slug" name="slug" id="slug" autocomplete="off" class="form-control bg-transparent @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required />
                        <div class="form-text">Seu site será: <strong id="slug-preview">seu-slug.smartclick360.com</strong></div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Section Dados da Empresa-->

                <!--begin::Section Dados Pessoais-->
                <div class="mb-10">
                    <h3 class="text-gray-800 fw-bold mb-6">Seus Dados Pessoais</h3>

                    <!--begin::Row-->
                    <div class="row g-4 mb-8">
                        <!--begin::Col - First Name-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Nome</label>
                            <input type="text" placeholder="Seu nome" name="first_name" autocomplete="off" class="form-control bg-transparent @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required />
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - Surname-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Sobrenome</label>
                            <input type="text" placeholder="Seu sobrenome" name="surname" autocomplete="off" class="form-control bg-transparent @error('surname') is-invalid @enderror" value="{{ old('surname') }}" required />
                            @error('surname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->

                    <!--begin::Row-->
                    <div class="row g-4 mb-8">
                        <!--begin::Col - WhatsApp-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">WhatsApp</label>
                            <input type="text" placeholder="(00) 00000-0000" name="whatsapp" id="whatsapp" autocomplete="off" class="form-control bg-transparent @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp') }}" required />
                            @error('whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - CPF/CNPJ-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">CPF/CNPJ</label>
                            <input type="text" placeholder="000.000.000-00 ou 00.000.000/0000-00" name="cpf_cnpj" id="cpf_cnpj" autocomplete="off" class="form-control bg-transparent @error('cpf_cnpj') is-invalid @enderror" value="{{ old('cpf_cnpj') }}" required />
                            @error('cpf_cnpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Section Dados Pessoais-->

                <!--begin::Section Dados de Acesso-->
                <div class="mb-10">
                    <h3 class="text-gray-800 fw-bold mb-6">Dados de Acesso</h3>

                    <!--begin::Input group - Email-->
                    <div class="fv-row mb-8">
                        <label class="form-label fw-semibold text-gray-900 fs-6">Email</label>
                        <input type="email" placeholder="seu@email.com" name="email" autocomplete="off" class="form-control bg-transparent @error('email') is-invalid @enderror" value="{{ old('email') }}" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Password-->
                    <div class="fv-row mb-8" data-kt-password-meter="true">
                        <div class="mb-1">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Senha</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent @error('password') is-invalid @enderror" type="password" placeholder="Digite sua senha" name="password" id="password" autocomplete="off" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <!--begin::Meter-->
                            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                            </div>
                            <!--end::Meter-->
                        </div>
                        <div class="text-muted">Use 8 ou mais caracteres com letras, números e símbolos.</div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Section Dados de Acesso-->

                <!--begin::Separator-->
                <div class="separator separator-content my-14">
                    <span class="w-150px text-gray-500 fw-semibold fs-7">Selecione seu plano</span>
                </div>
                <!--end::Separator-->

                <!--begin::Pricing Section-->
                <div id="pricing-section" class="mb-10">
                    <!--begin::Billing Toggle-->
                    <div class="d-flex justify-content-center mb-10">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="billing_toggle" id="billing_monthly" value="monthly" checked />
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="billing_monthly">Mensal</label>

                            <input type="radio" class="btn-check" name="billing_toggle" id="billing_yearly" value="yearly" />
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="billing_yearly">Anual</label>
                        </div>
                    </div>
                    <!--end::Billing Toggle-->

                    <!--begin::Plans Row-->
                    <div class="row g-5 mb-10">
                        <!--begin::Plan Starter-->
                        <div class="col-md-4">
                            <div class="card plan-card h-100" data-plan="starter" onclick="selectPlan('starter')">
                                <div class="card-body d-flex flex-column p-8">
                                    <h3 class="fw-bold text-gray-900 mb-2">Starter</h3>
                                    <div class="mb-7">
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-monthly">R$ 97</span>
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-yearly d-none">R$ 970</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-monthly">/mês</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-yearly d-none">/ano</span>
                                    </div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-outline btn-outline-dashed btn-active-light-primary w-100 plan-select-btn">
                                            Selecionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Plan Starter-->

                        <!--begin::Plan Professional (Recommended)-->
                        <div class="col-md-4">
                            <div class="card plan-card h-100 border-primary selected" data-plan="professional" onclick="selectPlan('professional')">
                                <div class="card-body d-flex flex-column p-8 position-relative">
                                    <span class="badge badge-primary position-absolute top-0 end-0 m-3">Recomendado</span>
                                    <h3 class="fw-bold text-gray-900 mb-2">Professional</h3>
                                    <div class="mb-7">
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-monthly">R$ 197</span>
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-yearly d-none">R$ 1.970</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-monthly">/mês</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-yearly d-none">/ano</span>
                                    </div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-primary w-100 plan-select-btn">
                                            Selecionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Plan Professional-->

                        <!--begin::Plan Enterprise-->
                        <div class="col-md-4">
                            <div class="card plan-card h-100" data-plan="enterprise" onclick="selectPlan('enterprise')">
                                <div class="card-body d-flex flex-column p-8">
                                    <h3 class="fw-bold text-gray-900 mb-2">Enterprise</h3>
                                    <div class="mb-7">
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-monthly">R$ 397</span>
                                        <span class="fs-2x fw-bold text-gray-900 plan-price-yearly d-none">R$ 3.970</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-monthly">/mês</span>
                                        <span class="text-gray-600 fw-semibold fs-7 plan-period-yearly d-none">/ano</span>
                                    </div>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-outline btn-outline-dashed btn-active-light-primary w-100 plan-select-btn">
                                            Selecionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Plan Enterprise-->
                    </div>
                    <!--end::Plans Row-->

                    <!--begin::Trial Notice-->
                    <div class="text-center text-gray-700 fw-semibold fs-6 mb-10">
                        <i class="ki-outline ki-check-circle text-success fs-2 me-2"></i>
                        Todos os planos incluem 7 dias de trial gratuito
                    </div>
                    <!--end::Trial Notice-->

                    <!--begin::Hidden Inputs-->
                    <input type="hidden" name="plan" id="selected_plan" value="professional" />
                    <input type="hidden" name="billing_cycle" id="billing_cycle" value="monthly" />
                    <!--end::Hidden Inputs-->
                </div>
                <!--end::Pricing Section-->

                <!--begin::Submit button-->
                <div class="d-grid mb-10">
                    <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                        <span class="indicator-label">Criar conta</span>
                        <span class="indicator-progress">
                            Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Submit button-->

                <!--begin::Sign in link-->
                <div class="text-gray-500 text-center fw-semibold fs-6">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="link-primary fw-semibold">Faça login</a>
                </div>
                <!--end::Sign in link-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Card-->
</div>
@endsection

@push('scripts')
<script>
"use strict";

// Geração automática do slug
let slugManuallyEdited = false;

document.getElementById('company_name').addEventListener('input', function(e) {
    if (!slugManuallyEdited) {
        const slug = generateSlug(e.target.value);
        document.getElementById('slug').value = slug;
        updateSlugPreview(slug);
    }
});

document.getElementById('slug').addEventListener('input', function(e) {
    slugManuallyEdited = e.target.value !== '';
    if (!slugManuallyEdited) {
        // Se limpar o slug, volta a gerar automaticamente
        const companyName = document.getElementById('company_name').value;
        const slug = generateSlug(companyName);
        e.target.value = slug;
    }
    updateSlugPreview(e.target.value);
});

function generateSlug(text) {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remove acentos
        .replace(/[^a-z0-9\s-]/g, '') // Remove caracteres especiais
        .trim()
        .replace(/\s+/g, '-') // Substitui espaços por hífen
        .replace(/-+/g, '-'); // Remove hífens duplicados
}

function updateSlugPreview(slug) {
    document.getElementById('slug-preview').textContent =
        (slug || 'seu-slug') + '.smartclick360.com';
}

// Máscaras de input usando Inputmask (já incluído no plugins.bundle.js)
if (typeof Inputmask !== 'undefined') {
    // Máscara de WhatsApp
    Inputmask({
        "mask": "(99) 99999-9999"
    }).mask("#whatsapp");

    // Máscara de CPF/CNPJ (auto-detecta)
    Inputmask({
        "mask": ["999.999.999-99", "99.999.999/9999-99"],
        "keepStatic": true
    }).mask("#cpf_cnpj");
}

// Seleção de plano
function selectPlan(plan) {
    // Remove seleção de todos os cards
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('selected', 'border-primary');
        card.querySelector('.plan-select-btn').classList.remove('btn-primary');
        card.querySelector('.plan-select-btn').classList.add('btn-outline', 'btn-outline-dashed', 'btn-active-light-primary');
        card.querySelector('.plan-select-btn').textContent = 'Selecionar';
    });

    // Adiciona seleção ao card clicado
    const selectedCard = document.querySelector(`[data-plan="${plan}"]`);
    selectedCard.classList.add('selected', 'border-primary');
    selectedCard.querySelector('.plan-select-btn').classList.add('btn-primary');
    selectedCard.querySelector('.plan-select-btn').classList.remove('btn-outline', 'btn-outline-dashed', 'btn-active-light-primary');
    selectedCard.querySelector('.plan-select-btn').textContent = 'Selecionado';

    // Atualiza input hidden
    document.getElementById('selected_plan').value = plan;
}

// Toggle de billing cycle (mensal/anual)
document.querySelectorAll('input[name="billing_toggle"]').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const cycle = this.value;
        document.getElementById('billing_cycle').value = cycle;

        // Atualiza preços exibidos
        if (cycle === 'monthly') {
            document.querySelectorAll('.plan-price-yearly, .plan-period-yearly').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.plan-price-monthly, .plan-period-monthly').forEach(el => el.classList.remove('d-none'));
        } else {
            document.querySelectorAll('.plan-price-monthly, .plan-period-monthly').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.plan-price-yearly, .plan-period-yearly').forEach(el => el.classList.remove('d-none'));
        }
    });
});

// Validação do formulário
const form = document.getElementById('kt_sign_up_form');
const submitButton = document.getElementById('kt_sign_up_submit');

form.addEventListener('submit', function(e) {
    let isValid = true;
    const errors = [];

    // Validar campos obrigatórios
    const requiredFields = [
        { id: 'company_name', name: 'Nome da empresa' },
        { id: 'slug', name: 'Slug' },
        { id: 'email', name: 'Email' },
        { id: 'password', name: 'Senha' },
        { id: 'first_name', name: 'Nome' },
        { id: 'surname', name: 'Sobrenome' },
        { id: 'whatsapp', name: 'WhatsApp' },
        { id: 'cpf_cnpj', name: 'CPF/CNPJ' }
    ];

    requiredFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input.value.trim()) {
            isValid = false;
            errors.push(`${field.name} é obrigatório`);
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });

    // Validar email
    const emailInput = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailInput.value && !emailRegex.test(emailInput.value)) {
        isValid = false;
        errors.push('Email inválido');
        emailInput.classList.add('is-invalid');
    }

    // Validar senha (mínimo 8 caracteres)
    const passwordInput = document.getElementById('password');
    if (passwordInput.value && passwordInput.value.length < 8) {
        isValid = false;
        errors.push('Senha deve ter no mínimo 8 caracteres');
        passwordInput.classList.add('is-invalid');
    }

    // Validar CPF/CNPJ (tamanho)
    const cpfCnpjInput = document.getElementById('cpf_cnpj');
    const cpfCnpjClean = cpfCnpjInput.value.replace(/\D/g, '');
    if (cpfCnpjClean && cpfCnpjClean.length !== 11 && cpfCnpjClean.length !== 14) {
        isValid = false;
        errors.push('CPF/CNPJ inválido');
        cpfCnpjInput.classList.add('is-invalid');
    }

    // Validar slug
    const slugInput = document.getElementById('slug');
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    if (slugInput.value && !slugRegex.test(slugInput.value)) {
        isValid = false;
        errors.push('Slug contém caracteres inválidos');
        slugInput.classList.add('is-invalid');
    }

    if (!isValid) {
        e.preventDefault();
        console.log('Erros de validação:', errors);
        return false;
    }

    // Mostrar loading no botão
    submitButton.setAttribute('data-kt-indicator', 'on');
    submitButton.disabled = true;
});

// Password strength meter (já incluído no Metronic)
if (typeof KTPasswordMeter !== 'undefined') {
    KTPasswordMeter.createInstances();
}
</script>
@endpush

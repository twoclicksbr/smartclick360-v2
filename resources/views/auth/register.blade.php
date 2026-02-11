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

                    <!--begin::Row-->
                    <div class="row g-4 mb-8">
                        <!--begin::Col - Company Name-->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Nome da empresa</label>
                            <input type="text" placeholder="Digite o nome da sua empresa" name="company_name" id="company_name" autocomplete="off" class="form-control bg-transparent @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required />
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Slug-->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Subdomínio (slug)</label>
                            <input type="text" placeholder="seu-slug" name="slug" id="slug" autocomplete="off" class="form-control bg-transparent @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required />
                            <div class="form-text">Seu site será: <strong id="slug-preview">seu-slug.smartclick360.com</strong></div>
                            <!--begin::Slug Availability Indicator-->
                            <div class="d-none mt-2" id="slug_availability_indicator">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-check-circle fs-3 text-success me-2 d-none" id="slug_available_icon"></i>
                                    <i class="ki-outline ki-cross-circle fs-3 text-danger me-2 d-none" id="slug_unavailable_icon"></i>
                                    <span class="spinner-border spinner-border-sm text-primary me-2 d-none" id="slug_checking_icon"></span>
                                    <span class="fs-7" id="slug_availability_text"></span>
                                </div>
                            </div>
                            <!--end::Slug Availability Indicator-->
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
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
                        <!--begin::Col - Phone-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">WhatsApp</label>
                            <input type="text" placeholder="(00) 00000-0000" name="phone" id="phone" autocomplete="off" class="form-control bg-transparent @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required />
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - Document-->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">CPF/CNPJ</label>
                            <input type="text" placeholder="000.000.000-00 ou 00.000.000/0000-00" name="document" id="document" autocomplete="off" class="form-control bg-transparent @error('document') is-invalid @enderror" value="{{ old('document') }}" required />
                            <!--begin::Document Availability Indicator-->
                            <div class="d-none mt-2" id="document_availability_indicator">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-check-circle fs-3 text-success me-2 d-none" id="document_available_icon"></i>
                                    <i class="ki-outline ki-cross-circle fs-3 text-danger me-2 d-none" id="document_unavailable_icon"></i>
                                    <span class="spinner-border spinner-border-sm text-primary me-2 d-none" id="document_checking_icon"></span>
                                    <span class="fs-7" id="document_availability_text"></span>
                                </div>
                            </div>
                            <!--end::Document Availability Indicator-->
                            @error('document')
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

                    <!--begin::Row - Email-->
                    <div class="row g-4 mb-8">
                        <!--begin::Col - Email-->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Email</label>
                            <input type="email" placeholder="seu@email.com" name="email" id="email" autocomplete="off" class="form-control bg-transparent @error('email') is-invalid @enderror" value="{{ old('email') }}" required />
                            <!--begin::Email Availability Indicator-->
                            <div class="d-none mt-2" id="email_availability_indicator">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-check-circle fs-3 text-success me-2 d-none" id="email_available_icon"></i>
                                    <i class="ki-outline ki-cross-circle fs-3 text-danger me-2 d-none" id="email_unavailable_icon"></i>
                                    <span class="spinner-border spinner-border-sm text-primary me-2 d-none" id="email_checking_icon"></span>
                                    <span class="fs-7" id="email_availability_text"></span>
                                </div>
                            </div>
                            <!--end::Email Availability Indicator-->
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->

                    <!--begin::Row - Senhas-->
                    <div class="row g-4 mb-8">
                        <!--begin::Col - Password-->
                        <div class="col-12 col-md-6" data-kt-password-meter="true">
                            <div class="mb-1">
                                <label class="form-label fw-semibold text-gray-900 fs-6">Senha</label>
                                <div class="position-relative mb-3">
                                    <input class="form-control bg-transparent" type="password" placeholder="Digite sua senha" name="password" id="password" autocomplete="off" required />
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
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Password Confirmation-->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-gray-900 fs-6">Confirmar Senha</label>
                            <div class="position-relative mb-3">
                                <input class="form-control bg-transparent" type="password" placeholder="Confirme sua senha" name="password_confirmation" id="password_confirmation" autocomplete="off" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" id="toggle_password_confirmation">
                                    <i class="ki-outline ki-eye-slash fs-2" id="icon_hide_confirmation"></i>
                                    <i class="ki-outline ki-eye fs-2 d-none" id="icon_show_confirmation"></i>
                                </span>
                            </div>
                            <!--begin::Match Indicator-->
                            <div class="d-none" id="password_match_indicator">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-check-circle fs-2 text-success me-2" id="match_icon"></i>
                                    <i class="ki-outline ki-cross-circle fs-2 text-danger me-2 d-none" id="mismatch_icon"></i>
                                    <span class="text-muted fs-7" id="match_text"></span>
                                </div>
                            </div>
                            <!--end::Match Indicator-->
                            @error('password')
                                <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
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

console.log('Register form script loaded');

// ========================================
// Limpar erros de validação do servidor quando o usuário começar a digitar
// ========================================
function clearServerValidationErrors() {
    const form = document.getElementById('kt_sign_up_form');
    if (!form) return;

    // Seleciona todos os inputs, selects e textareas do formulário
    const fields = form.querySelectorAll('input, select, textarea');

    fields.forEach(field => {
        field.addEventListener('input', function() {
            // Remove a classe is-invalid do campo
            this.classList.remove('is-invalid');

            // Encontra o container mais próximo - qualquer elemento com classe que contenha "col-"
            let container = this.closest('[class*="col-"]');

            if (container) {
                // Procura e esconde todas as mensagens de erro dentro do container
                // .invalid-feedback são sempre mensagens de erro do Laravel
                const invalidFeedbacks = container.querySelectorAll('.invalid-feedback');
                invalidFeedbacks.forEach(msg => {
                    msg.style.display = 'none';
                });

                // .text-danger também são mensagens de erro (usado no campo de senha)
                // Procura por qualquer div com classe text-danger que tenha fs-7 e mt-2
                const dangerMessages = container.querySelectorAll('div.text-danger.fs-7.mt-2');
                dangerMessages.forEach(msg => {
                    msg.style.display = 'none';
                });
            }
        });
    });
}

// Inicializa a limpeza de erros quando o DOM estiver pronto
clearServerValidationErrors();

// Geração automática do slug
let slugManuallyEdited = false;

const companyNameEl = document.getElementById('company_name');
const slugEl = document.getElementById('slug');

if (companyNameEl && slugEl) {
    console.log('Company name and slug elements found');

    companyNameEl.addEventListener('input', function(e) {
        if (!slugManuallyEdited) {
            const slug = generateSlug(e.target.value);
            slugEl.value = slug;
            updateSlugPreview(slug);
            checkSlugAvailability(slug);
        }
    });

    slugEl.addEventListener('input', function(e) {
        slugManuallyEdited = e.target.value !== '';
        if (!slugManuallyEdited) {
            // Se limpar o slug, volta a gerar automaticamente
            const companyName = companyNameEl.value;
            const slug = generateSlug(companyName);
            e.target.value = slug;
        }
        updateSlugPreview(e.target.value);
        checkSlugAvailability(e.target.value);
    });
} else {
    console.error('Company name or slug element not found');
}

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

// Validação de disponibilidade do slug em tempo real
const slugAvailabilityIndicator = document.getElementById('slug_availability_indicator');
const slugAvailableIcon = document.getElementById('slug_available_icon');
const slugUnavailableIcon = document.getElementById('slug_unavailable_icon');
const slugCheckingIcon = document.getElementById('slug_checking_icon');
const slugAvailabilityText = document.getElementById('slug_availability_text');
let slugCheckTimeout;

function checkSlugAvailability(slug) {
    // Limpa timeout anterior
    clearTimeout(slugCheckTimeout);

    // Se o slug estiver vazio ou inválido, esconde o indicador
    const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
    if (!slug || slug.length < 3 || !slugRegex.test(slug)) {
        slugAvailabilityIndicator.classList.add('d-none');
        return;
    }

    // Mostra "Verificando..." imediatamente
    slugAvailabilityIndicator.classList.remove('d-none');
    slugAvailableIcon.classList.add('d-none');
    slugUnavailableIcon.classList.add('d-none');
    slugCheckingIcon.classList.remove('d-none');
    slugAvailabilityText.textContent = 'Verificando...';
    slugAvailabilityText.className = 'fs-7 text-muted';

    // Debounce de 500ms
    slugCheckTimeout = setTimeout(() => {
        fetch('{{ route('register.checkSlug') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ slug: slug })
        })
        .then(response => response.json())
        .then(data => {
            slugCheckingIcon.classList.add('d-none');

            if (data.available) {
                // Slug disponível
                slugAvailableIcon.classList.remove('d-none');
                slugUnavailableIcon.classList.add('d-none');
                slugAvailabilityText.textContent = data.message;
                slugAvailabilityText.className = 'fs-7 text-success fw-semibold';

                // Remove erro do servidor se existir
                const slugInput = document.getElementById('slug');
                if (slugInput) {
                    slugInput.classList.remove('is-invalid');
                    const errorDiv = slugInput.parentElement.querySelector('.invalid-feedback');
                    if (errorDiv) errorDiv.style.display = 'none';
                }
            } else {
                // Slug indisponível
                slugAvailableIcon.classList.add('d-none');
                slugUnavailableIcon.classList.remove('d-none');
                slugAvailabilityText.textContent = data.message;
                slugAvailabilityText.className = 'fs-7 text-danger fw-semibold';
            }
        })
        .catch(error => {
            console.error('Erro ao verificar slug:', error);
            slugCheckingIcon.classList.add('d-none');
            slugAvailabilityIndicator.classList.add('d-none');
        });
    }, 500);
}

// Validação de disponibilidade do email em tempo real
const emailAvailabilityIndicator = document.getElementById('email_availability_indicator');
const emailAvailableIcon = document.getElementById('email_available_icon');
const emailUnavailableIcon = document.getElementById('email_unavailable_icon');
const emailCheckingIcon = document.getElementById('email_checking_icon');
const emailAvailabilityText = document.getElementById('email_availability_text');
let emailCheckTimeout;

function checkEmailAvailability(email) {
    // Limpa timeout anterior
    clearTimeout(emailCheckTimeout);

    // Validação básica de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        emailAvailabilityIndicator.classList.add('d-none');
        return;
    }

    // Mostra "Verificando..." imediatamente
    emailAvailabilityIndicator.classList.remove('d-none');
    emailAvailableIcon.classList.add('d-none');
    emailUnavailableIcon.classList.add('d-none');
    emailCheckingIcon.classList.remove('d-none');
    emailAvailabilityText.textContent = 'Verificando...';
    emailAvailabilityText.className = 'fs-7 text-muted';

    // Debounce de 500ms
    emailCheckTimeout = setTimeout(() => {
        fetch('{{ route('register.checkEmail') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            emailCheckingIcon.classList.add('d-none');

            if (data.available) {
                // Email disponível
                emailAvailableIcon.classList.remove('d-none');
                emailUnavailableIcon.classList.add('d-none');
                emailAvailabilityText.textContent = data.message;
                emailAvailabilityText.className = 'fs-7 text-success fw-semibold';

                // Remove erro do servidor se existir
                const emailInput = document.getElementById('email');
                if (emailInput) {
                    emailInput.classList.remove('is-invalid');
                    const errorDiv = emailInput.parentElement.querySelector('.invalid-feedback');
                    if (errorDiv) errorDiv.style.display = 'none';
                }
            } else {
                // Email já cadastrado
                emailAvailableIcon.classList.add('d-none');
                emailUnavailableIcon.classList.remove('d-none');
                emailAvailabilityText.textContent = data.message;
                emailAvailabilityText.className = 'fs-7 text-danger fw-semibold';
            }
        })
        .catch(error => {
            console.error('Erro ao verificar email:', error);
            emailCheckingIcon.classList.add('d-none');
            emailAvailabilityIndicator.classList.add('d-none');
        });
    }, 500);
}

// Validação de disponibilidade do CPF/CNPJ em tempo real
const documentAvailabilityIndicator = document.getElementById('document_availability_indicator');
const documentAvailableIcon = document.getElementById('document_available_icon');
const documentUnavailableIcon = document.getElementById('document_unavailable_icon');
const documentCheckingIcon = document.getElementById('document_checking_icon');
const documentAvailabilityText = document.getElementById('document_availability_text');
let documentCheckTimeout;

function checkDocumentAvailability(document) {
    // Limpa timeout anterior
    clearTimeout(documentCheckTimeout);

    // Remove caracteres não numéricos para validar o tamanho
    const documentClean = document.replace(/\D/g, '');

    // Se o documento estiver vazio ou com tamanho inválido, esconde o indicador
    if (!documentClean || (documentClean.length !== 11 && documentClean.length !== 14)) {
        documentAvailabilityIndicator.classList.add('d-none');
        return;
    }

    // Mostra "Verificando..." imediatamente
    documentAvailabilityIndicator.classList.remove('d-none');
    documentAvailableIcon.classList.add('d-none');
    documentUnavailableIcon.classList.add('d-none');
    documentCheckingIcon.classList.remove('d-none');
    documentAvailabilityText.textContent = 'Verificando...';
    documentAvailabilityText.className = 'fs-7 text-muted';

    // Debounce de 500ms
    documentCheckTimeout = setTimeout(() => {
        fetch('{{ route('register.checkDocument') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ document: documentClean })
        })
        .then(response => response.json())
        .then(data => {
            documentCheckingIcon.classList.add('d-none');

            if (data.available) {
                // Documento disponível
                documentAvailableIcon.classList.remove('d-none');
                documentUnavailableIcon.classList.add('d-none');
                documentAvailabilityText.textContent = data.message;
                documentAvailabilityText.className = 'fs-7 text-success fw-semibold';

                // Remove erro do servidor se existir
                const documentInput = document.getElementById('document');
                if (documentInput) {
                    documentInput.classList.remove('is-invalid');
                    const errorDiv = documentInput.parentElement.querySelector('.invalid-feedback');
                    if (errorDiv) errorDiv.style.display = 'none';
                }
            } else {
                // Documento já cadastrado
                documentAvailableIcon.classList.add('d-none');
                documentUnavailableIcon.classList.remove('d-none');
                documentAvailabilityText.textContent = data.message;
                documentAvailabilityText.className = 'fs-7 text-danger fw-semibold';
            }
        })
        .catch(error => {
            console.error('Erro ao verificar CPF/CNPJ:', error);
            documentCheckingIcon.classList.add('d-none');
            documentAvailabilityIndicator.classList.add('d-none');
        });
    }, 500);
}

// Máscaras de input usando Inputmask (já incluído no plugins.bundle.js)
if (typeof Inputmask !== 'undefined') {
    console.log('Inputmask library found, applying masks...');

    const phoneEl = document.getElementById('phone');
    const documentEl = document.getElementById('document');

    if (phoneEl) {
        // Máscara de WhatsApp/Phone
        Inputmask({
            "mask": "(99) 99999-9999"
        }).mask(phoneEl);
        console.log('Phone mask applied');
    } else {
        console.error('Phone element not found');
    }

    if (documentEl) {
        // Máscara de CPF/CNPJ (auto-detecta)
        Inputmask({
            "mask": ["999.999.999-99", "99.999.999/9999-99"],
            "keepStatic": true
        }).mask(documentEl);
        console.log('Document mask applied');
    } else {
        console.error('Document element not found');
    }
} else {
    console.error('Inputmask library not found');
}

// Event listeners para validação do email
const emailInputEl = document.getElementById('email');

if (emailInputEl) {
    console.log('Adding email validation listeners');

    // Valida quando o campo perde o foco (blur)
    emailInputEl.addEventListener('blur', function(e) {
        checkEmailAvailability(e.target.value);
    });

    // Valida em tempo real quando digita
    emailInputEl.addEventListener('input', function(e) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(e.target.value)) {
            checkEmailAvailability(e.target.value);
        }
    });
} else {
    console.error('Email input element not found for validation listeners');
}

// Event listeners para validação do documento (CPF/CNPJ)
const documentInputEl = document.getElementById('document');

if (documentInputEl) {
    console.log('Adding document validation listeners');

    // Valida quando o campo perde o foco (blur)
    documentInputEl.addEventListener('blur', function(e) {
        checkDocumentAvailability(e.target.value);
    });

    // Valida quando a máscara estiver completa (input event)
    documentInputEl.addEventListener('input', function(e) {
        const documentClean = e.target.value.replace(/\D/g, '');
        // CPF tem 11 dígitos, CNPJ tem 14 dígitos
        if (documentClean.length === 11 || documentClean.length === 14) {
            checkDocumentAvailability(e.target.value);
        }
    });
} else {
    console.error('Document input element not found for validation listeners');
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
    // Remove máscaras antes de enviar (gravar apenas números no banco)
    const phoneInput = document.getElementById('phone');
    const documentInput = document.getElementById('document');

    if (phoneInput) {
        phoneInput.value = phoneInput.value.replace(/\D/g, ''); // Remove tudo exceto números
    }

    if (documentInput) {
        documentInput.value = documentInput.value.replace(/\D/g, ''); // Remove tudo exceto números
    }

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
        { id: 'phone', name: 'WhatsApp' },
        { id: 'document', name: 'CPF/CNPJ' }
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
    const documentInputValidation = document.getElementById('document');
    if (documentInputValidation) {
        const documentClean = documentInputValidation.value.replace(/\D/g, '');
        if (documentClean && documentClean.length !== 11 && documentClean.length !== 14) {
            isValid = false;
            errors.push('CPF/CNPJ inválido');
            documentInputValidation.classList.add('is-invalid');
        }
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
    console.log('KTPasswordMeter found, initializing...');
    KTPasswordMeter.createInstances();
    console.log('KTPasswordMeter initialized');
} else {
    console.error('KTPasswordMeter not found');
}

console.log('Register form script initialization complete');

// Toggle password visibility para o campo de confirmação
const togglePasswordConfirmationEl = document.getElementById('toggle_password_confirmation');
const passwordConfirmationEl = document.getElementById('password_confirmation');
const iconHideConfirmationEl = document.getElementById('icon_hide_confirmation');
const iconShowConfirmationEl = document.getElementById('icon_show_confirmation');

if (togglePasswordConfirmationEl && passwordConfirmationEl && iconHideConfirmationEl && iconShowConfirmationEl) {
    console.log('Adding password confirmation toggle listener');

    togglePasswordConfirmationEl.addEventListener('click', function() {
        if (passwordConfirmationEl.type === 'password') {
            passwordConfirmationEl.type = 'text';
            iconHideConfirmationEl.classList.add('d-none');
            iconShowConfirmationEl.classList.remove('d-none');
        } else {
            passwordConfirmationEl.type = 'password';
            iconHideConfirmationEl.classList.remove('d-none');
            iconShowConfirmationEl.classList.add('d-none');
        }
    });
} else {
    console.error('Password confirmation toggle elements not found');
}

// Validação em tempo real de confirmação de senha
const passwordInput = document.getElementById('password');
const passwordConfirmationInput = document.getElementById('password_confirmation');
const matchIndicator = document.getElementById('password_match_indicator');
const matchIcon = document.getElementById('match_icon');
const mismatchIcon = document.getElementById('mismatch_icon');
const matchText = document.getElementById('match_text');

function validatePasswordMatch() {
    if (!passwordInput || !passwordConfirmationInput || !matchIndicator || !matchIcon || !mismatchIcon || !matchText) {
        return;
    }

    const password = passwordInput.value;
    const confirmation = passwordConfirmationInput.value;

    // Só mostra o indicador se o campo de confirmação tiver conteúdo
    if (confirmation.length === 0) {
        matchIndicator.classList.add('d-none');
        return;
    }

    matchIndicator.classList.remove('d-none');

    if (password === confirmation && confirmation.length >= 8) {
        // Senhas coincidem
        matchIcon.classList.remove('d-none');
        mismatchIcon.classList.add('d-none');
        matchText.textContent = 'As senhas coincidem';
        matchText.classList.remove('text-danger');
        matchText.classList.add('text-success');
    } else {
        // Senhas não coincidem
        matchIcon.classList.add('d-none');
        mismatchIcon.classList.remove('d-none');
        matchText.textContent = 'As senhas não coincidem';
        matchText.classList.remove('text-success');
        matchText.classList.add('text-danger');
    }
}

// Adiciona listeners para validação em tempo real
if (passwordInput && passwordConfirmationInput) {
    console.log('Adding password match validation listeners');
    passwordInput.addEventListener('input', validatePasswordMatch);
    passwordConfirmationInput.addEventListener('input', validatePasswordMatch);
} else {
    console.error('Password or password confirmation input not found for validation');
}
</script>
@endpush

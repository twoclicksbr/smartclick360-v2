@php
    // Configurar breadcrumbs e título para o toolbar
    $breadcrumbs = [
        ['label' => 'Módulos', 'url' => url('/modules'), 'segment' => 'modules'],
        ['label' => $module->name, 'url' => null, 'segment' => $module->slug],
    ];
    $pageTitle = $module->name;
    $pageDescription = $module->description_show ?? 'Configurações do módulo';
@endphp

@extends('landlord.layouts.app')

@section('title', $module->name . ' - Configurações')

@section('content')
    {{-- begin::Navbar --}}
    <div class="card mb-5">
        <div class="card-body pt-9 pb-0">
            {{-- begin::Details --}}
            <div class="d-flex flex-wrap flex-sm-nowrap mb-0">
                {{-- begin::Wrapper --}}
                <div class="flex-grow-1">
                    {{-- begin::Head --}}
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        {{-- Lado esquerdo --}}
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if($module->icon)
                                    <i class="{{ $module->icon }} fs-2x text-gray-600"></i>
                                @endif
                                <h2 class="mb-0 fw-bold">{{ $module->name }}</h2>
                                @if($module->status)
                                    <i class="ki-duotone ki-verify fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                @else
                                    <i class="ki-duotone ki-cross-circle fs-2x text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                @endif
                            </div>
                            <p class="text-muted mb-0">{{ $module->slug }}</p>
                        </div>

                        {{-- Lado direito --}}
                        <div class="d-flex align-items-center gap-3">
                            {{-- Badge Tipo --}}
                            @php
                                $typeLabels = ['module' => 'Módulo', 'submodule' => 'Submódulo', 'pivot' => 'Pivot'];
                            @endphp
                            <span class="btn btn-sm btn-light-info pe-none">
                                Tipo: {{ $typeLabels[$module->type] ?? $module->type }}
                            </span>

                            {{-- Badge Escopo --}}
                            @php
                                $scopeLabels = ['landlord' => 'SmartClick360°', 'tenant' => 'Clientes'];
                            @endphp
                            <span class="btn btn-sm btn-light-primary pe-none">
                                Escopo: {{ $scopeLabels[$module->scope] ?? $module->scope }}
                            </span>

                            {{-- Botão Fechar (volta pro index) --}}
                            <a href="{{ url('/modules') }}" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Fechar">
                                <i class="ki-outline ki-cross fs-3"></i>
                            </a>
                        </div>
                    </div>
                    {{-- end::Head --}}
                </div>
                {{-- end::Wrapper --}}
            </div>
            {{-- end::Details --}}
            <div class="separator"></div>
            {{-- begin::Nav --}}
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab" href="#tab_geral">Geral</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_campos">Campos</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_submodulos">Submódulos</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_seeds">Seeds</a>
                </li>
                {{-- end::Nav item --}}
            </ul>
            {{-- end::Nav --}}
        </div>
    </div>
    {{-- end::Navbar --}}

    {{-- begin::Tab content --}}
    <div class="tab-content">
        {{-- begin::Tab pane Geral --}}
        <div class="tab-pane fade show active" id="tab_geral">
            {{-- begin::Form --}}
            <form method="POST" action="{{ url('modules/' . encodeId($module->id)) }}">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                {{-- Card 1: Identificação --}}
                <div class="card mb-5">
                    <div class="card-header min-h-50px">
                        <h3 class="card-title">Identificação</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            {{-- Nome --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Nome</label>
                                <input type="text" class="form-control form-control-solid" name="name" id="name" value="{{ $module->name }}" required>
                            </div>

                            {{-- Slug --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Slug</label>
                                <input type="text" class="form-control form-control-solid" name="slug" id="slug" value="{{ $module->slug }}" required>
                            </div>

                            {{-- Tipo --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Tipo</label>
                                <select class="form-select form-select-solid" name="type" required>
                                    <option value="module" {{ $module->type === 'module' ? 'selected' : '' }}>Módulo</option>
                                    <option value="submodule" {{ $module->type === 'submodule' ? 'selected' : '' }}>Submódulo</option>
                                    <option value="pivot" {{ $module->type === 'pivot' ? 'selected' : '' }}>Pivot</option>
                                </select>
                            </div>

                            {{-- Escopo --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Escopo</label>
                                <select class="form-select form-select-solid" name="scope" required>
                                    <option value="landlord" {{ $module->scope === 'landlord' ? 'selected' : '' }}>SmartClick360° (landlord)</option>
                                    <option value="tenant" {{ $module->scope === 'tenant' ? 'selected' : '' }}>Clientes (tenant)</option>
                                </select>
                            </div>

                            {{-- Ícone --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Ícone</label>
                                <x-icon-picker name="icon" value="{{ $module->icon }}" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Listagem --}}
                <div class="card mb-5">
                    <div class="card-header min-h-50px">
                        <h3 class="card-title">Listagem</h3>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            {{-- Ordenação Padrão --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Ordenação Padrão</label>
                                <input type="text" class="form-control form-control-solid" name="default_sort_field" value="{{ $module->default_sort_field }}">
                            </div>

                            {{-- Direção --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Direção</label>
                                <select class="form-select form-select-solid" name="default_sort_direction">
                                    <option value="asc" {{ $module->default_sort_direction === 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ $module->default_sort_direction === 'desc' ? 'selected' : '' }}>Descendente</option>
                                </select>
                            </div>

                            {{-- Itens por Página --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Itens por Página</label>
                                <select class="form-select form-select-solid" name="per_page">
                                    <option value="25" {{ $module->per_page == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $module->per_page == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $module->per_page == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>

                            {{-- Exibir Drag --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Exibir Drag</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input switch-badge" type="checkbox" name="show_drag" value="1" {{ $module->show_drag ? 'checked' : '' }}>
                                    </div>
                                    <span class="badge badge-light-{{ $module->show_drag ? 'primary' : 'danger' }}">
                                        {{ $module->show_drag ? 'Sim' : 'Não' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Exibir Checkbox --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Exibir Checkbox</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input switch-badge" type="checkbox" name="show_checkbox" value="1" {{ $module->show_checkbox ? 'checked' : '' }}>
                                    </div>
                                    <span class="badge badge-light-{{ $module->show_checkbox ? 'primary' : 'danger' }}">
                                        {{ $module->show_checkbox ? 'Sim' : 'Não' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Exibir Ações --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Exibir Ações</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input switch-badge" type="checkbox" name="show_actions" value="1" {{ $module->show_actions ? 'checked' : '' }}>
                                    </div>
                                    <span class="badge badge-light-{{ $module->show_actions ? 'primary' : 'danger' }}">
                                        {{ $module->show_actions ? 'Sim' : 'Não' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descrições + Configurações Gerais (lado a lado) --}}
                <div class="row g-5 mb-5">
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header min-h-50px">
                                <h3 class="card-title">Descrições</h3>
                            </div>
                            <div class="card-body py-4">
                                <div class="row">
                                    {{-- Descrição Index --}}
                                    <div class="col-md-6 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Descrição Index</label>
                                        <textarea class="form-control form-control-solid" name="description_index" rows="3">{{ $module->description_index }}</textarea>
                                    </div>

                                    {{-- Descrição Show --}}
                                    <div class="col-md-6 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Descrição Show</label>
                                        <textarea class="form-control form-control-solid" name="description_show" rows="3">{{ $module->description_show }}</textarea>
                                    </div>

                                    {{-- Descrição Create --}}
                                    <div class="col-md-6 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Descrição Create</label>
                                        <textarea class="form-control form-control-solid" name="description_create" rows="3">{{ $module->description_create }}</textarea>
                                    </div>

                                    {{-- Descrição Edit --}}
                                    <div class="col-md-6 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Descrição Edit</label>
                                        <textarea class="form-control form-control-solid" name="description_edit" rows="3">{{ $module->description_edit }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header min-h-50px">
                                <h3 class="card-title">Configurações Gerais</h3>
                            </div>
                            <div class="card-body py-4">
                                <div class="row">
                                    {{-- Pré-marcado --}}
                                    <div class="col-md-12 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Pré-marcado</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input switch-badge" type="checkbox" name="default_checked" value="1" {{ $module->default_checked ? 'checked' : '' }}>
                                            </div>
                                            <span class="badge badge-light-{{ $module->default_checked ? 'primary' : 'danger' }}">
                                                {{ $module->default_checked ? 'Sim' : 'Não' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Origem --}}
                                    <div class="col-md-12 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Origem</label>
                                        <select class="form-select form-select-solid" name="origin">
                                            <option value="system" {{ $module->origin === 'system' ? 'selected' : '' }}>System</option>
                                            <option value="custom" {{ $module->origin === 'custom' ? 'selected' : '' }}>Custom</option>
                                        </select>
                                    </div>

                                    {{-- Ordem --}}
                                    <div class="col-md-12 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Ordem</label>
                                        <input type="number" class="form-control form-control-solid" name="order" value="{{ $module->order }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configurações Avançadas (colapsável) --}}
                <div class="mb-5">
                    <div class="d-flex align-items-center cursor-pointer ms-5" data-bs-toggle="collapse" data-bs-target="#advancedSettings" aria-expanded="false">
                        <h5 class="fw-semibold text-muted mb-0">Configurações Avançadas</h5>
                        <i class="ki-outline ki-down fs-4 ms-2 text-muted rotation" id="advancedArrow"></i>
                    </div>
                    <div class="collapse mt-5" id="advancedSettings">
                        <div class="row g-5">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header min-h-50px">
                                        <h3 class="card-title">Classes PHP</h3>
                                    </div>
                                    <div class="card-body py-4">
                                        <div class="row">
                                            {{-- Model --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Model</label>
                                                <input type="text" class="form-control form-control-solid" name="model" value="{{ $module->model }}">
                                            </div>

                                            {{-- Service --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Service</label>
                                                <input type="text" class="form-control form-control-solid" name="service" value="{{ $module->service }}">
                                            </div>

                                            {{-- Controller --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Controller</label>
                                                <input type="text" class="form-control form-control-solid" name="controller" value="{{ $module->controller }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header min-h-50px">
                                        <h3 class="card-title">Views Customizadas</h3>
                                    </div>
                                    <div class="card-body py-4">
                                        <div class="row">
                                            {{-- View Index --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">View Index</label>
                                                <input type="text" class="form-control form-control-solid" name="view_index" value="{{ $module->view_index }}">
                                            </div>

                                            {{-- View Show --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">View Show</label>
                                                <input type="text" class="form-control form-control-solid" name="view_show" value="{{ $module->view_show }}">
                                            </div>

                                            {{-- View Modal --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">View Modal</label>
                                                <input type="text" class="form-control form-control-solid" name="view_modal" value="{{ $module->view_modal }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header min-h-50px">
                                        <h3 class="card-title">Comportamento</h3>
                                    </div>
                                    <div class="card-body py-4">
                                        <div class="row">
                                            {{-- Após Criar --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Após Criar</label>
                                                <select class="form-select form-select-solid" name="after_store">
                                                    <option value="index" {{ $module->after_store === 'index' ? 'selected' : '' }}>Index</option>
                                                    <option value="show" {{ $module->after_store === 'show' ? 'selected' : '' }}>Show</option>
                                                    <option value="edit" {{ $module->after_store === 'edit' ? 'selected' : '' }}>Edit</option>
                                                </select>
                                            </div>

                                            {{-- Após Atualizar --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Após Atualizar</label>
                                                <select class="form-select form-select-solid" name="after_update">
                                                    <option value="index" {{ $module->after_update === 'index' ? 'selected' : '' }}>Index</option>
                                                    <option value="show" {{ $module->after_update === 'show' ? 'selected' : '' }}>Show</option>
                                                    <option value="edit" {{ $module->after_update === 'edit' ? 'selected' : '' }}>Edit</option>
                                                </select>
                                            </div>

                                            {{-- Após Restaurar --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Após Restaurar</label>
                                                <select class="form-select form-select-solid" name="after_restore">
                                                    <option value="index" {{ $module->after_restore === 'index' ? 'selected' : '' }}>Index</option>
                                                    <option value="show" {{ $module->after_restore === 'show' ? 'selected' : '' }}>Show</option>
                                                    <option value="edit" {{ $module->after_restore === 'edit' ? 'selected' : '' }}>Edit</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status e Botões --}}
                <div class="d-flex justify-content-between align-items-center mt-5">
                    {{-- Status à esquerda --}}
                    <div class="d-flex align-items-center gap-3 ms-5">
                        <label class="fs-6 fw-semibold">Status</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input switch-badge" type="checkbox" name="status" value="1" {{ $module->status ? 'checked' : '' }}>
                        </div>
                        <span class="badge badge-light-{{ $module->status ? 'primary' : 'danger' }}">
                            {{ $module->status ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    {{-- Botões à direita --}}
                    <div class="d-flex gap-3">
                        <a href="{{ url('/modules') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-outline ki-check fs-4"></i> Salvar
                        </button>
                    </div>
                </div>
            </form>
            {{-- end::Form --}}
        </div>
        {{-- end::Tab pane Geral --}}

        {{-- begin::Tab pane Campos --}}
        <div class="tab-pane fade" id="tab_campos">
            <div class="card">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-20">
                    <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                    <h3 class="text-gray-800 fw-bold mb-2">Em breve...</h3>
                    <p class="text-gray-500 fs-6 mb-0">Gestão de campos do módulo</p>
                </div>
            </div>
        </div>
        {{-- end::Tab pane Campos --}}

        {{-- begin::Tab pane Submódulos --}}
        <div class="tab-pane fade" id="tab_submodulos">
            <div class="card">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-20">
                    <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                    <h3 class="text-gray-800 fw-bold mb-2">Em breve...</h3>
                    <p class="text-gray-500 fs-6 mb-0">Gestão de submódulos vinculados</p>
                </div>
            </div>
        </div>
        {{-- end::Tab pane Submódulos --}}

        {{-- begin::Tab pane Seeds --}}
        <div class="tab-pane fade" id="tab_seeds">
            <div class="card">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-20">
                    <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                    <h3 class="text-gray-800 fw-bold mb-2">Em breve...</h3>
                    <p class="text-gray-500 fs-6 mb-0">Gestão de dados de seed do módulo</p>
                </div>
            </div>
        </div>
        {{-- end::Tab pane Seeds --}}
    </div>
    {{-- end::Tab content --}}
@endsection

@push('scripts')
<script>
"use strict";

// Geração automática do slug a partir do campo name
let slugManuallyEdited = false;

const nameEl = document.getElementById('name');
const slugEl = document.getElementById('slug');

if (nameEl && slugEl) {
    nameEl.addEventListener('input', function(e) {
        if (!slugManuallyEdited) {
            const slug = generateSlug(e.target.value);
            slugEl.value = slug;
        }
    });

    slugEl.addEventListener('input', function(e) {
        slugManuallyEdited = e.target.value !== '';
        if (!slugManuallyEdited) {
            // Se limpar o slug, volta a gerar automaticamente
            const name = nameEl.value;
            const slug = generateSlug(name);
            e.target.value = slug;
        }
    });
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

// Inicializar Select2 em todos os selects
if (typeof $.fn.select2 !== 'undefined') {
    $('select.form-select').select2({
        minimumResultsForSearch: -1  // esconde busca em selects pequenos
    });
}

// Atualizar badges dos switches dinamicamente
document.querySelectorAll('.switch-badge').forEach(function(input) {
    input.addEventListener('change', function() {
        const badge = this.closest('.d-flex').querySelector('.badge');
        if (this.checked) {
            badge.className = 'badge badge-light-primary';
            badge.textContent = 'Sim';
        } else {
            badge.className = 'badge badge-light-danger';
            badge.textContent = 'Não';
        }
    });
});

// Rotacionar seta do accordion de Configurações Avançadas
const advancedCollapse = document.getElementById('advancedSettings');
const advancedArrow = document.getElementById('advancedArrow');
if (advancedCollapse && advancedArrow) {
    advancedCollapse.addEventListener('show.bs.collapse', function() {
        advancedArrow.classList.add('active');
    });
    advancedCollapse.addEventListener('hide.bs.collapse', function() {
        advancedArrow.classList.remove('active');
    });
}
</script>

<style>
.btn-light-info.pe-none:hover {
    background-color: var(--bs-info) !important;
    color: #fff !important;
}

.btn-light-primary.pe-none:hover {
    background-color: var(--bs-primary) !important;
    color: #fff !important;
}

.rotation {
    transition: transform 0.3s ease;
}
.rotation.active {
    transform: rotate(180deg);
}
</style>
@endpush

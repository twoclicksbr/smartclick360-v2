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
    <div class="card mb-9">
        <div class="card-body pt-9 pb-0">
            {{-- begin::Details --}}
            <div class="d-flex flex-wrap flex-sm-nowrap mb-0">
                {{-- begin::Wrapper --}}
                <div class="flex-grow-1">
                    {{-- begin::Head --}}
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        {{-- begin::Details --}}
                        <div class="d-flex flex-column">
                            {{-- begin::Status --}}
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-gray-800 fs-2 fw-bold me-3">{{ $module->name }}</span>
                                <span class="badge badge-light-{{ $module->scope === 'tenant' ? 'primary' : 'warning' }} me-2">
                                    {{ $module->scope === 'tenant' ? 'Tenant' : 'Landlord' }}
                                </span>
                                <span class="badge badge-light-{{ $module->status ? 'success' : 'danger' }}">
                                    {{ $module->status ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            {{-- end::Status --}}
                            {{-- begin::Description --}}
                            <div class="d-flex flex-wrap fw-semibold mb-0 fs-5 text-gray-500">{{ $module->slug }}</div>
                            {{-- end::Description --}}
                        </div>
                        {{-- end::Details --}}
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
            {{-- begin::Card --}}
            <div class="card">
                {{-- begin::Card header --}}
                <div class="card-header">
                    {{-- begin::Card title --}}
                    <div class="card-title fs-3 fw-bold">Configurações do Módulo</div>
                    {{-- end::Card title --}}
                </div>
                {{-- end::Card header --}}
                {{-- begin::Form --}}
                <form method="POST" action="{{ url('modules/' . encodeId($module->id)) }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    {{-- begin::Card body --}}
                    <div class="card-body p-9">
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

                            {{-- Model --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Model</label>
                                <input type="text" class="form-control form-control-solid" name="model" value="{{ $module->model }}">
                            </div>

                            {{-- Service --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Service</label>
                                <input type="text" class="form-control form-control-solid" name="service" value="{{ $module->service }}">
                            </div>

                            {{-- Controller --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Controller</label>
                                <input type="text" class="form-control form-control-solid" name="controller" value="{{ $module->controller }}">
                            </div>

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

                            {{-- Descrição Index --}}
                            <div class="col-md-3 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Descrição Index</label>
                                <textarea class="form-control form-control-solid" name="description_index" rows="3">{{ $module->description_index }}</textarea>
                            </div>

                            {{-- Descrição Show --}}
                            <div class="col-md-3 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Descrição Show</label>
                                <textarea class="form-control form-control-solid" name="description_show" rows="3">{{ $module->description_show }}</textarea>
                            </div>

                            {{-- Descrição Create --}}
                            <div class="col-md-3 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Descrição Create</label>
                                <textarea class="form-control form-control-solid" name="description_create" rows="3">{{ $module->description_create }}</textarea>
                            </div>

                            {{-- Descrição Edit --}}
                            <div class="col-md-3 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Descrição Edit</label>
                                <textarea class="form-control form-control-solid" name="description_edit" rows="3">{{ $module->description_edit }}</textarea>
                            </div>

                            {{-- View Index --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">View Index</label>
                                <input type="text" class="form-control form-control-solid" name="view_index" value="{{ $module->view_index }}">
                            </div>

                            {{-- View Show --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">View Show</label>
                                <input type="text" class="form-control form-control-solid" name="view_show" value="{{ $module->view_show }}">
                            </div>

                            {{-- View Modal --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">View Modal</label>
                                <input type="text" class="form-control form-control-solid" name="view_modal" value="{{ $module->view_modal }}">
                            </div>

                            {{-- Após Criar --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Após Criar</label>
                                <select class="form-select form-select-solid" name="after_store">
                                    <option value="index" {{ $module->after_store === 'index' ? 'selected' : '' }}>Index</option>
                                    <option value="show" {{ $module->after_store === 'show' ? 'selected' : '' }}>Show</option>
                                    <option value="edit" {{ $module->after_store === 'edit' ? 'selected' : '' }}>Edit</option>
                                </select>
                            </div>

                            {{-- Após Atualizar --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Após Atualizar</label>
                                <select class="form-select form-select-solid" name="after_update">
                                    <option value="index" {{ $module->after_update === 'index' ? 'selected' : '' }}>Index</option>
                                    <option value="show" {{ $module->after_update === 'show' ? 'selected' : '' }}>Show</option>
                                    <option value="edit" {{ $module->after_update === 'edit' ? 'selected' : '' }}>Edit</option>
                                </select>
                            </div>

                            {{-- Após Restaurar --}}
                            <div class="col-md-4 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Após Restaurar</label>
                                <select class="form-select form-select-solid" name="after_restore">
                                    <option value="index" {{ $module->after_restore === 'index' ? 'selected' : '' }}>Index</option>
                                    <option value="show" {{ $module->after_restore === 'show' ? 'selected' : '' }}>Show</option>
                                    <option value="edit" {{ $module->after_restore === 'edit' ? 'selected' : '' }}>Edit</option>
                                </select>
                            </div>

                            {{-- Pré-marcado --}}
                            <div class="col-md-2 mb-7">
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
                            <div class="col-md-3 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Origem</label>
                                <select class="form-select form-select-solid" name="origin">
                                    <option value="system" {{ $module->origin === 'system' ? 'selected' : '' }}>System</option>
                                    <option value="custom" {{ $module->origin === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>

                            {{-- Ordem --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Ordem</label>
                                <input type="number" class="form-control form-control-solid" name="order" value="{{ $module->order }}">
                            </div>

                            {{-- Status --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2">Status</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input switch-badge" type="checkbox" name="status" value="1" {{ $module->status ? 'checked' : '' }}>
                                    </div>
                                    <span class="badge badge-light-{{ $module->status ? 'primary' : 'danger' }}">
                                        {{ $module->status ? 'Sim' : 'Não' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end::Card body --}}
                    {{-- begin::Card footer --}}
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ url('/modules') }}" class="btn btn-light btn-active-light-primary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                    {{-- end::Card footer --}}
                </form>
                {{-- end::Form --}}
            </div>
            {{-- end::Card --}}
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
</script>
@endpush

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
                {{-- begin::Header --}}
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Campos do Módulo</span>
                        <span class="text-muted mt-1 fw-semibold fs-7" id="fields_count">{{ $fields->where('deleted_at', null)->count() }} campos configurados</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-primary" id="btn_save_fields" disabled>
                            <i class="ki-outline ki-check fs-4"></i> Salvar Alterações
                        </button>
                    </div>
                </div>
                {{-- end::Header --}}
                {{-- begin::Body --}}
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="fields_table">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="w-25px ps-4"></th>
                                    <th class="min-w-200px">Nome</th>
                                    <th class="min-w-150px">Tipo</th>
                                    <th class="w-100px">Tamanho</th>
                                    <th class="w-50px text-center">Req</th>
                                    <th class="w-50px text-center">Null</th>
                                    <th class="w-50px text-center">Uniq</th>
                                    <th class="w-50px text-center">Idx</th>
                                    <th class="min-w-120px">Default</th>
                                    <th class="w-100px text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="fields_tbody">
                                @foreach($fields as $field)
                                    @if(!$field->trashed())
                                    <tr data-id="{{ encodeId($field->id) }}" class="field-row" data-origin="{{ $field->origin }}">
                                        {{-- Drag --}}
                                        <td class="ps-4">
                                            @if($field->origin === 'custom')
                                                <i class="ki-solid ki-abstract-16 fs-4 text-gray-500 cursor-grab sortable-handle"></i>
                                            @else
                                                <i class="ki-outline ki-lock fs-5 text-gray-400"></i>
                                            @endif
                                        </td>
                                        {{-- Name --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex justify-content-start flex-column">
                                                    <input type="text" class="form-control form-control-sm form-control-solid fw-bold field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}" name="name" value="{{ $field->name }}" data-original="{{ $field->name }}" placeholder="nome_coluna" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Type --}}
                                        <td>
                                            <select class="form-select form-select-sm form-select-solid field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}" name="type" data-original="{{ $field->type }}" style="min-width: 130px;" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                <option value="string" {{ $field->type === 'string' ? 'selected' : '' }}>STRING</option>
                                                <option value="text" {{ $field->type === 'text' ? 'selected' : '' }}>TEXT</option>
                                                <option value="integer" {{ $field->type === 'integer' ? 'selected' : '' }}>INTEGER</option>
                                                <option value="bigInteger" {{ $field->type === 'bigInteger' ? 'selected' : '' }}>BIGINT</option>
                                                <option value="decimal" {{ $field->type === 'decimal' ? 'selected' : '' }}>DECIMAL</option>
                                                <option value="boolean" {{ $field->type === 'boolean' ? 'selected' : '' }}>BOOLEAN</option>
                                                <option value="date" {{ $field->type === 'date' ? 'selected' : '' }}>DATE</option>
                                                <option value="datetime" {{ $field->type === 'datetime' ? 'selected' : '' }}>DATETIME</option>
                                                <option value="timestamp" {{ $field->type === 'timestamp' ? 'selected' : '' }}>TIMESTAMP</option>
                                                <option value="json" {{ $field->type === 'json' ? 'selected' : '' }}>JSON</option>
                                                <option value="foreignId" {{ $field->type === 'foreignId' ? 'selected' : '' }}>FK (foreignId)</option>
                                            </select>
                                        </td>
                                        {{-- Length --}}
                                        <td>
                                            <input type="text" class="form-control form-control-sm form-control-solid text-center field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}" name="length" value="{{ $field->precision ? $field->length . ',' . $field->precision : $field->length }}" data-original="{{ $field->precision ? $field->length . ',' . $field->precision : $field->length }}" placeholder="—" style="max-width: 80px;" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                        </td>
                                        {{-- Required --}}
                                        <td class="text-center">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                <input class="form-check-input field-input" type="checkbox" name="required" value="1" {{ $field->required ? 'checked' : '' }} data-original="{{ $field->required ? '1' : '0' }}" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        {{-- Nullable --}}
                                        <td class="text-center">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                <input class="form-check-input field-input" type="checkbox" name="nullable" value="1" {{ $field->nullable ? 'checked' : '' }} data-original="{{ $field->nullable ? '1' : '0' }}" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        {{-- Unique --}}
                                        <td class="text-center">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                <input class="form-check-input field-input" type="checkbox" name="unique" value="1" {{ $field->unique ? 'checked' : '' }} data-original="{{ $field->unique ? '1' : '0' }}" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        {{-- Index --}}
                                        <td class="text-center">
                                            <div class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                <input class="form-check-input field-input" type="checkbox" name="index" value="1" {{ $field->index ? 'checked' : '' }} data-original="{{ $field->index ? '1' : '0' }}" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </div>
                                        </td>
                                        {{-- Default --}}
                                        <td>
                                            <input type="text" class="form-control form-control-sm form-control-solid field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}" name="default" value="{{ $field->default }}" data-original="{{ $field->default }}" placeholder="NULL" {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                        </td>
                                        {{-- Actions --}}
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                @if($field->origin === 'custom')
                                                    <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-field" data-code="{{ encodeId($field->id) }}" data-bs-toggle="tooltip" title="Excluir">
                                                        <i class="ki-outline ki-trash fs-5"></i>
                                                    </button>
                                                @else
                                                    <span class="badge badge-light-secondary fs-8">system</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- begin::Add button --}}
                    <div class="d-flex justify-content-center py-6 border-top border-gray-200">
                        <button type="button" class="btn btn-light-primary btn-sm" id="btn_add_field">
                            <i class="ki-outline ki-plus fs-5"></i> Adicionar Campo
                        </button>
                    </div>
                    {{-- end::Add button --}}
                </div>
                {{-- end::Body --}}
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

// ============================================
// ABA CAMPOS — GRID INLINE BD
// ============================================

const moduleCode = '{{ encodeId($module->id) }}';
const fieldsBaseUrl = '/modules/' + moduleCode + '/fields';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const btnSaveFields = document.getElementById('btn_save_fields');

// --- Detectar mudanças (habilitar botão salvar) ---
function markDirty() {
    if (isDragging) return;
    btnSaveFields.disabled = false;
    btnSaveFields.classList.remove('btn-light-primary');
    btnSaveFields.classList.add('btn-primary');
}

document.getElementById('fields_tbody').addEventListener('input', markDirty);
document.getElementById('fields_tbody').addEventListener('change', markDirty);

// --- Adicionar Campo (nova row vazia) ---
document.getElementById('btn_add_field').addEventListener('click', function() {
    const tbody = document.getElementById('fields_tbody');
    const tempId = 'new_' + Date.now();

    const tr = document.createElement('tr');
    tr.dataset.id = tempId;
    tr.classList.add('field-row', 'table-active');
    tr.innerHTML = `
        <td class="ps-4">
            <i class="ki-solid ki-abstract-16 fs-5 text-gray-400 cursor-grab sortable-handle"></i>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm form-control-solid fw-bold field-input" name="name" value="" placeholder="nome_coluna" data-original="">
        </td>
        <td>
            <select class="form-select form-select-sm form-select-solid field-input" name="type" data-original="">
                <option value="string" selected>STRING</option>
                <option value="text">TEXT</option>
                <option value="integer">INTEGER</option>
                <option value="bigInteger">BIGINT</option>
                <option value="decimal">DECIMAL</option>
                <option value="boolean">BOOLEAN</option>
                <option value="date">DATE</option>
                <option value="datetime">DATETIME</option>
                <option value="timestamp">TIMESTAMP</option>
                <option value="json">JSON</option>
                <option value="foreignId">FK (foreignId)</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm form-control-solid text-center field-input" name="length" value="255" placeholder="—" data-original="">
        </td>
        <td class="text-center">
            <div class="form-check form-check-solid form-check-sm d-flex justify-content-center">
                <input class="form-check-input field-input" type="checkbox" name="required" value="1" data-original="0">
            </div>
        </td>
        <td class="text-center">
            <div class="form-check form-check-solid form-check-sm d-flex justify-content-center">
                <input class="form-check-input field-input" type="checkbox" name="nullable" value="1" data-original="0">
            </div>
        </td>
        <td class="text-center">
            <div class="form-check form-check-solid form-check-sm d-flex justify-content-center">
                <input class="form-check-input field-input" type="checkbox" name="unique" value="1" data-original="0">
            </div>
        </td>
        <td class="text-center">
            <div class="form-check form-check-solid form-check-sm d-flex justify-content-center">
                <input class="form-check-input field-input" type="checkbox" name="index" value="1" data-original="0">
            </div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm form-control-solid field-input" name="default" value="" placeholder="NULL" data-original="">
        </td>
        <td class="text-center pe-4">
            <button type="button" class="btn btn-icon btn-sm btn-bg-light btn-active-color-danger btn-delete-field" data-code="${tempId}" data-bs-toggle="tooltip" title="Excluir">
                <i class="ki-outline ki-trash fs-5"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    tr.querySelector('input[name="name"]').focus();
    markDirty();

    // Bind delete no novo botão
    tr.querySelector('.btn-delete-field').addEventListener('click', handleDelete);
});

// --- Salvar tudo (create novos + update existentes) ---
document.getElementById('btn_save_fields').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('🔵 Botão Salvar clicado');

    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Salvando...';
    btn.disabled = true;

    const rows = document.querySelectorAll('#fields_tbody tr.field-row');
    console.log('🔵 Total rows:', rows.length);

    let promises = [];

    rows.forEach(function(row, index) {
        if (row.dataset.origin === 'system') {
            console.log('⏭️ Pulando system:', row.querySelector('input[name="name"]')?.value);
            return;
        }

        const id = row.dataset.id;
        const nameVal = row.querySelector('input[name="name"]')?.value;
        const typeVal = row.querySelector('select[name="type"]')?.value;
        const lengthVal = row.querySelector('input[name="length"]')?.value;
        const reqVal = row.querySelector('input[name="required"]')?.checked;
        const nullVal = row.querySelector('input[name="nullable"]')?.checked;
        const uniqVal = row.querySelector('input[name="unique"]')?.checked;
        const idxVal = row.querySelector('input[name="index"]')?.checked;
        const defVal = row.querySelector('input[name="default"]')?.value;

        console.log('🔵 Campo:', id, '| name:', nameVal, '| type:', typeVal, '| length:', lengthVal);

        const data = {
            name: nameVal,
            type: typeVal,
            length: lengthVal || null,
            required: reqVal ? 1 : 0,
            nullable: nullVal ? 1 : 0,
            unique: uniqVal ? 1 : 0,
            index: idxVal ? 1 : 0,
            default: defVal || null,
        };

        let url, method;
        if (id.startsWith('new_')) {
            url = fieldsBaseUrl;
            method = 'POST';
        } else {
            url = fieldsBaseUrl + '/' + id;
            method = 'PUT';
        }

        console.log('🔵 URL:', url, '| Method:', method);
        console.log('🔵 Data:', JSON.stringify(data));
        console.log('🔵 Body:', JSON.stringify(method === 'PUT' ? {...data, _method: 'PUT'} : data));

        const fetchOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(method === 'PUT' ? {...data, _method: 'PUT'} : data)
        };

        console.log('🔵 CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content?.substring(0, 10) + '...');

        promises.push(
            fetch(url, fetchOptions)
                .then(function(r) {
                    console.log('🟢 Response status:', r.status, 'for', url);
                    return r.json();
                })
                .then(function(json) {
                    console.log('🟢 Response JSON:', JSON.stringify(json));
                    return json;
                })
                .catch(function(err) {
                    console.error('🔴 Fetch error for', url, ':', err.message);
                    return {success: false, message: err.message};
                })
        );
    });

    console.log('🔵 Total promises:', promises.length);

    if (promises.length === 0) {
        console.log('⚠️ Nenhum campo para salvar');
        btn.innerHTML = originalText;
        btn.disabled = false;
        return;
    }

    Promise.all(promises).then(function(results) {
        console.log('🔵 All results:', JSON.stringify(results));
        const errors = results.filter(r => !r.success);
        if (errors.length > 0) {
            console.log('🔴 Errors:', JSON.stringify(errors));
            btn.innerHTML = originalText;
            btn.disabled = false;
            Swal.fire('Erro', errors[0].message || 'Erro ao salvar campos', 'error');
        } else {
            console.log('✅ Tudo salvo com sucesso');
            var hadNewFields = results.some(function(r) { return r && r.data && r.data.id; });

            btn.innerHTML = originalText;
            btn.disabled = true;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-light-primary');

            Swal.fire({
                icon: 'success',
                title: 'Salvo!',
                text: 'Campos atualizados com sucesso',
                timer: 1500,
                showConfirmButton: false
            }).then(function() {
                if (hadNewFields) {
                    window.location.href = window.location.pathname + '#tab_campos';
                    window.location.reload();
                }
            });
        }
    }).catch(function(err) {
        console.error('🔴 Promise.all error:', err.message);
        btn.innerHTML = originalText;
        btn.disabled = false;
        Swal.fire('Erro', 'Erro de conexão', 'error');
    });
});

// --- Delete (soft delete ou remover row nova) ---
function handleDelete() {
    const code = this.dataset.code;
    const row = this.closest('tr');

    if (code.startsWith('new_')) {
        row.remove();
        return;
    }

    Swal.fire({
        title: 'Excluir campo?',
        text: 'O campo será removido (soft delete).',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch(fieldsBaseUrl + '/' + code, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    row.remove();
                    Swal.fire({ icon: 'success', title: 'Excluído!', timer: 1000, showConfirmButton: false });
                } else {
                    Swal.fire('Erro', data.message, 'error');
                }
            });
        }
    });
}

document.querySelectorAll('.btn-delete-field').forEach(function(btn) {
    btn.addEventListener('click', handleDelete);
});

let sortableInstance = null;
let isDragging = false;

function initSortableFields() {
    if (sortableInstance) {
        return;
    }

    const fieldsTbody = document.getElementById('fields_tbody');
    if (!fieldsTbody) {
        return;
    }

    if (typeof Sortable === 'undefined') {
        return;
    }

    sortableInstance = Sortable.create(fieldsTbody, {
        handle: '.sortable-handle',
        animation: 150,
        filter: '[data-origin="system"]',
        onMove: function(evt) {
            if (evt.related && evt.related.dataset && evt.related.dataset.origin === 'system') {
                return false;
            }
            return true;
        },
        onStart: function() {
            isDragging = true;
        },
        onEnd: function(evt) {
            isDragging = false;
            let ids = [];
            document.querySelectorAll('#fields_tbody tr.field-row[data-origin="custom"]').forEach(function(row) {
                if (!row.dataset.id.startsWith('new_')) {
                    ids.push(row.dataset.id);
                }
            });
            if (ids.length > 0) {
                fetch(`{{ route('landlord.modules.fields.reorder', encodeId($module->id)) }}`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({ids: ids})
                }).then(function(response) {
                    if (response.ok) {
                        const tbody = document.getElementById('fields_tbody');
                        const allRows = Array.from(tbody.querySelectorAll('tr.field-row'));

                        const idRow = allRows.find(r => r.dataset.origin === 'system' && r.querySelector('input[name="name"]')?.value === 'id');
                        const customRows = allRows.filter(r => r.dataset.origin === 'custom');
                        const systemRest = allRows.filter(r => r.dataset.origin === 'system' && r.querySelector('input[name="name"]')?.value !== 'id');

                        if (idRow) tbody.appendChild(idRow);
                        customRows.forEach(r => tbody.appendChild(r));
                        systemRest.forEach(r => tbody.appendChild(r));
                    }
                });
            }
        }
    });
}

// Inicializar em múltiplos momentos
// 1. Imediatamente (caso aba já esteja visível)
initSortableFields();

// 2. Via evento de aba
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function(e) {
        initSortableFields();
    });
});

// 3. Fallback com delay
setTimeout(function() {
    initSortableFields();
}, 1000);

// 4. Fallback mais agressivo
setTimeout(function() {
    initSortableFields();
}, 2000);

// --- Manter aba ativa após reload ---
const urlHash = window.location.hash;
if (urlHash) {
    const tabTrigger = document.querySelector('a[href="' + urlHash + '"]');
    if (tabTrigger) {
        const tab = new bootstrap.Tab(tabTrigger);
        tab.show();
    }
}
document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tabEl) {
    tabEl.addEventListener('shown.bs.tab', function(event) {
        history.replaceState(null, null, event.target.getAttribute('href'));
    });
});
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

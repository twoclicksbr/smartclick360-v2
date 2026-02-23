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
                                @if ($module->icon)
                                    <i class="{{ $module->icon }} fs-2x text-gray-600"></i>
                                @endif
                                <h2 class="mb-0 fw-bold">{{ $module->name }}</h2>
                                @if ($module->status)
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
                            <a href="{{ url('/modules') }}" class="btn btn-sm btn-icon btn-light-danger"
                                data-bs-toggle="tooltip" data-bs-placement="left" title="Fechar">
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
                    <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab"
                        href="#tab_modulo">Módulo</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_campos">Campos</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_grid">Grid</a>
                </li>
                {{-- end::Nav item --}}
                {{-- begin::Nav item --}}
                <li class="nav-item">
                    <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_form">Form</a>
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
        {{-- begin::Tab pane Módulo --}}
        <div class="tab-pane fade show active" id="tab_modulo">
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
                                <input type="text" class="form-control form-control-solid" name="name" id="name"
                                    value="{{ $module->name }}" required>
                            </div>

                            {{-- Slug --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Slug</label>
                                <input type="text" class="form-control form-control-solid" name="slug" id="slug"
                                    value="{{ $module->slug }}" required>
                            </div>

                            {{-- Tipo --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Tipo</label>
                                <select class="form-select form-select-solid" name="type" required>
                                    <option value="module" {{ $module->type === 'module' ? 'selected' : '' }}>Módulo
                                    </option>
                                    <option value="submodule" {{ $module->type === 'submodule' ? 'selected' : '' }}>
                                        Submódulo</option>
                                    <option value="pivot" {{ $module->type === 'pivot' ? 'selected' : '' }}>Pivot</option>
                                </select>
                            </div>

                            {{-- Escopo --}}
                            <div class="col-md-2 mb-7">
                                <label class="fs-6 fw-semibold mb-2 required">Escopo</label>
                                <select class="form-select form-select-solid" name="scope" required>
                                    <option value="landlord" {{ $module->scope === 'landlord' ? 'selected' : '' }}>
                                        SmartClick360° (landlord)</option>
                                    <option value="tenant" {{ $module->scope === 'tenant' ? 'selected' : '' }}>Clientes
                                        (tenant)</option>
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

                <!--begin::Card Submódulos-->
                <div class="card mt-6 mb-6" id="card_submodules" style="{{ $module->type !== 'module' ? 'display:none' : '' }}">
                    <div class="card-header">
                        <h3 class="card-title">Submódulos Vinculados</h3>
                    </div>
                    <div class="card-body">
                        @if ($allSubmodules->isEmpty())
                            <p class="text-muted">Nenhum submódulo cadastrado no sistema.</p>
                        @else
                            <div class="row g-4">
                                @foreach ($allSubmodules as $sub)
                                    <div class="col-md-3">
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input submodule-checkbox" type="checkbox"
                                                value="{{ $sub->id }}"
                                                {{ in_array($sub->id, $linkedSubmodules) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $sub->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <!--end::Card Submódulos-->

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
                                                <input class="form-check-input switch-badge" type="checkbox"
                                                    name="default_checked" value="1"
                                                    {{ $module->default_checked ? 'checked' : '' }}>
                                            </div>
                                            <span
                                                class="badge badge-light-{{ $module->default_checked ? 'primary' : 'danger' }}">
                                                {{ $module->default_checked ? 'Sim' : 'Não' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Origem --}}
                                    <div class="col-md-12 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Origem</label>
                                        <select class="form-select form-select-solid" name="origin">
                                            <option value="system" {{ $module->origin === 'system' ? 'selected' : '' }}>
                                                System</option>
                                            <option value="custom" {{ $module->origin === 'custom' ? 'selected' : '' }}>
                                                Custom</option>
                                        </select>
                                    </div>

                                    {{-- Ordem --}}
                                    <div class="col-md-12 mb-7">
                                        <label class="fs-6 fw-semibold mb-2">Ordem</label>
                                        <input type="number" class="form-control form-control-solid" name="order"
                                            value="{{ $module->order }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Configurações Avançadas (colapsável) --}}
                <div class="mb-5">
                    <div class="d-flex align-items-center cursor-pointer ms-5" data-bs-toggle="collapse"
                        data-bs-target="#advancedSettings" aria-expanded="false">
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
                                                <input type="text" class="form-control form-control-solid"
                                                    name="model" value="{{ $module->model }}">
                                            </div>

                                            {{-- Service --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Service</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    name="service" value="{{ $module->service }}">
                                            </div>

                                            {{-- Controller --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Controller</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    name="controller" value="{{ $module->controller }}">
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
                                                <input type="text" class="form-control form-control-solid"
                                                    name="view_index" value="{{ $module->view_index }}">
                                            </div>

                                            {{-- View Show --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">View Show</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    name="view_show" value="{{ $module->view_show }}">
                                            </div>

                                            {{-- View Modal --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">View Modal</label>
                                                <input type="text" class="form-control form-control-solid"
                                                    name="view_modal" value="{{ $module->view_modal }}">
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
                                                    <option value="index"
                                                        {{ $module->after_store === 'index' ? 'selected' : '' }}>Index
                                                    </option>
                                                    <option value="show"
                                                        {{ $module->after_store === 'show' ? 'selected' : '' }}>Show
                                                    </option>
                                                    <option value="edit"
                                                        {{ $module->after_store === 'edit' ? 'selected' : '' }}>Edit
                                                    </option>
                                                </select>
                                            </div>

                                            {{-- Após Atualizar --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Após Atualizar</label>
                                                <select class="form-select form-select-solid" name="after_update">
                                                    <option value="index"
                                                        {{ $module->after_update === 'index' ? 'selected' : '' }}>Index
                                                    </option>
                                                    <option value="show"
                                                        {{ $module->after_update === 'show' ? 'selected' : '' }}>Show
                                                    </option>
                                                    <option value="edit"
                                                        {{ $module->after_update === 'edit' ? 'selected' : '' }}>Edit
                                                    </option>
                                                </select>
                                            </div>

                                            {{-- Após Restaurar --}}
                                            <div class="col-md-12 mb-7">
                                                <label class="fs-6 fw-semibold mb-2">Após Restaurar</label>
                                                <select class="form-select form-select-solid" name="after_restore">
                                                    <option value="index"
                                                        {{ $module->after_restore === 'index' ? 'selected' : '' }}>Index
                                                    </option>
                                                    <option value="show"
                                                        {{ $module->after_restore === 'show' ? 'selected' : '' }}>Show
                                                    </option>
                                                    <option value="edit"
                                                        {{ $module->after_restore === 'edit' ? 'selected' : '' }}>Edit
                                                    </option>
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
                            <input class="form-check-input switch-badge" type="checkbox" name="status" value="1"
                                {{ $module->status ? 'checked' : '' }}>
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
        {{-- end::Tab pane Módulo --}}

        {{-- begin::Tab pane Campos --}}
        <div class="tab-pane fade" id="tab_campos">
            <div class="card">
                {{-- begin::Header --}}
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Campos do Módulo</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"
                            id="fields_count">{{ $fields->where('deleted_at', null)->count() }} campos configurados</span>
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
                                @foreach ($fields as $field)
                                    @if (!$field->trashed())
                                        <tr data-id="{{ encodeId($field->id) }}" class="field-row"
                                            data-origin="{{ $field->origin }}">
                                            {{-- Drag --}}
                                            <td class="ps-4">
                                                @if ($field->origin === 'custom')
                                                    <i
                                                        class="ki-solid ki-abstract-16 fs-4 text-gray-500 cursor-grab sortable-handle"></i>
                                                @else
                                                    <i class="ki-outline ki-lock fs-5 text-gray-400"></i>
                                                @endif
                                            </td>
                                            {{-- Name --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="d-flex justify-content-start flex-column">
                                                        <input type="text"
                                                            class="form-control form-control-sm form-control-solid fw-bold field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}"
                                                            name="name" value="{{ $field->name }}"
                                                            data-original="{{ $field->name }}" placeholder="nome_coluna"
                                                            {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- Type --}}
                                            <td>
                                                <select
                                                    class="form-select form-select-sm form-select-solid field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}"
                                                    name="type" data-original="{{ $field->type }}"
                                                    style="min-width: 130px;"
                                                    {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                    <option value="string"
                                                        {{ $field->type === 'string' ? 'selected' : '' }}>STRING</option>
                                                    <option value="text"
                                                        {{ $field->type === 'text' ? 'selected' : '' }}>TEXT</option>
                                                    <option value="integer"
                                                        {{ $field->type === 'integer' ? 'selected' : '' }}>INTEGER</option>
                                                    <option value="bigInteger"
                                                        {{ $field->type === 'bigInteger' ? 'selected' : '' }}>BIGINT
                                                    </option>
                                                    <option value="decimal"
                                                        {{ $field->type === 'decimal' ? 'selected' : '' }}>DECIMAL</option>
                                                    <option value="boolean"
                                                        {{ $field->type === 'boolean' ? 'selected' : '' }}>BOOLEAN</option>
                                                    <option value="date"
                                                        {{ $field->type === 'date' ? 'selected' : '' }}>DATE</option>
                                                    <option value="datetime"
                                                        {{ $field->type === 'datetime' ? 'selected' : '' }}>DATETIME
                                                    </option>
                                                    <option value="timestamp"
                                                        {{ $field->type === 'timestamp' ? 'selected' : '' }}>TIMESTAMP
                                                    </option>
                                                    <option value="json"
                                                        {{ $field->type === 'json' ? 'selected' : '' }}>JSON</option>
                                                    <option value="foreignId"
                                                        {{ $field->type === 'foreignId' ? 'selected' : '' }}>FK (foreignId)
                                                    </option>
                                                </select>
                                            </td>
                                            {{-- Length --}}
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm form-control-solid text-center field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}"
                                                    name="length"
                                                    value="{{ $field->precision ? $field->length . ',' . $field->precision : $field->length }}"
                                                    data-original="{{ $field->precision ? $field->length . ',' . $field->precision : $field->length }}"
                                                    placeholder="—" style="max-width: 80px;"
                                                    {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </td>
                                            {{-- Required --}}
                                            <td class="text-center">
                                                <div
                                                    class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                    <input class="form-check-input field-input" type="checkbox"
                                                        name="required" value="1"
                                                        {{ $field->required ? 'checked' : '' }}
                                                        data-original="{{ $field->required ? '1' : '0' }}"
                                                        {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            {{-- Nullable --}}
                                            <td class="text-center">
                                                <div
                                                    class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                    <input class="form-check-input field-input" type="checkbox"
                                                        name="nullable" value="1"
                                                        {{ $field->nullable ? 'checked' : '' }}
                                                        data-original="{{ $field->nullable ? '1' : '0' }}"
                                                        {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            {{-- Unique --}}
                                            <td class="text-center">
                                                <div
                                                    class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                    <input class="form-check-input field-input" type="checkbox"
                                                        name="unique" value="1"
                                                        {{ $field->unique ? 'checked' : '' }}
                                                        data-original="{{ $field->unique ? '1' : '0' }}"
                                                        {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            {{-- Index --}}
                                            <td class="text-center">
                                                <div
                                                    class="form-check form-check-custom form-check-solid form-check-sm d-flex justify-content-center">
                                                    <input class="form-check-input field-input" type="checkbox"
                                                        name="index" value="1"
                                                        {{ $field->index ? 'checked' : '' }}
                                                        data-original="{{ $field->index ? '1' : '0' }}"
                                                        {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            {{-- Default --}}
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm form-control-solid field-input {{ $field->origin === 'system' ? 'bg-transparent text-gray-500' : '' }}"
                                                    name="default" value="{{ $field->default }}"
                                                    data-original="{{ $field->default }}" placeholder="NULL"
                                                    {{ $field->origin === 'system' ? 'disabled' : '' }}>
                                            </td>
                                            {{-- Actions --}}
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end flex-shrink-0">
                                                    @if ($field->origin === 'custom')
                                                        <button type="button"
                                                            class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm btn-delete-field"
                                                            data-code="{{ encodeId($field->id) }}"
                                                            data-bs-toggle="tooltip" title="Excluir">
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

        {{-- begin::Tab pane Grid --}}
        <div class="tab-pane fade" id="tab_grid">
            <!--begin::Card Listagem (movido da aba Módulo)-->
            <div class="card mb-5">
                <div class="card-header min-h-50px">
                    <h3 class="card-title">Listagem</h3>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        {{-- Ordenação Padrão --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2">Ordenação Padrão</label>
                            <input type="text" class="form-control form-control-solid" name="default_sort_field"
                                value="{{ $module->default_sort_field }}">
                        </div>

                        {{-- Direção --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2">Direção</label>
                            <select class="form-select form-select-solid" name="default_sort_direction">
                                <option value="asc"
                                    {{ $module->default_sort_direction === 'asc' ? 'selected' : '' }}>Ascendente
                                </option>
                                <option value="desc"
                                    {{ $module->default_sort_direction === 'desc' ? 'selected' : '' }}>Descendente
                                </option>
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
                                    <input class="form-check-input switch-badge" type="checkbox" name="show_drag"
                                        value="1" {{ $module->show_drag ? 'checked' : '' }}>
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
                                    <input class="form-check-input switch-badge" type="checkbox" name="show_checkbox"
                                        value="1" {{ $module->show_checkbox ? 'checked' : '' }}>
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
                                    <input class="form-check-input switch-badge" type="checkbox" name="show_actions"
                                        value="1" {{ $module->show_actions ? 'checked' : '' }}>
                                </div>
                                <span class="badge badge-light-{{ $module->show_actions ? 'primary' : 'danger' }}">
                                    {{ $module->show_actions ? 'Sim' : 'Não' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card Listagem-->

            <!--begin::Card Preview da Grid-->
            <div class="card">
                <div class="card-body">
                    <!--begin::Grid Preview-->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div>
                            <h3 class="fw-bold mb-1">Preview da Grid</h3>
                            <p class="text-muted fs-7 mb-0">Clique no cabeçalho para configurar a coluna. Clique na célula
                                para configurar o dado.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-200 align-middle gs-3 gy-3">
                            <thead>
                                <tr class="fw-bold text-muted bg-light" style="white-space: nowrap;">
                                    @foreach ($fields as $field)
                                        @php
                                            $ui = $field->ui ?? null;
                                            $visibleIndex = $ui ? $ui->visible_index : false;
                                        @endphp
                                        <th class="cursor-pointer px-3 {{ !$visibleIndex ? 'opacity-25' : '' }}"
                                            data-field-id="{{ $field->id }}"
                                            data-field-code="{{ encodeId($field->id) }}"
                                            data-visible="{{ $visibleIndex ? '1' : '0' }}"
                                            data-searchable="{{ $ui && $ui->searchable ? '1' : '0' }}"
                                            data-sortable="{{ $ui && $ui->sortable ? '1' : '0' }}"
                                            data-width="{{ $ui->width_index ?? '' }}" title="Clique para configurar"
                                            style="{{ $ui && $ui->width_index ? 'width:' . $ui->width_index : '' }}">
                                            <span>{{ $field->ui->grid_label ?? $field->label }}</span>
                                            @if ($ui && $ui->sortable)
                                                <i class="ki-outline ki-arrow-up-down fs-7 ms-1 text-primary"></i>
                                            @endif
                                            @if ($ui && $ui->searchable)
                                                <i class="ki-outline ki-magnifier fs-7 ms-1 text-info"></i>
                                            @endif
                                            @if ($module->default_sort_field === $field->name)
                                                <i class="ki-outline ki-sort fs-7 ms-1 text-success"></i>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($row = 0; $row < 3; $row++)
                                    <tr>
                                        @foreach ($fields as $field)
                                            @php
                                                $ui = $field->ui ?? null;
                                                $visibleIndex = $ui ? $ui->visible_index : false;
                                                $value = $fakeData[$field->name][$row] ?? '—';
                                            @endphp
                                            <td class="cursor-pointer px-3 {{ !$visibleIndex ? 'opacity-25' : '' }}"
                                                data-field-id="{{ $field->id }}"
                                                data-field-code="{{ encodeId($field->id) }}"
                                                title="Clique para configurar">
                                                @if ($field->type === 'BOOLEAN')
                                                    @if ($value)
                                                        <span class="badge badge-light-success">Ativo</span>
                                                    @else
                                                        <span class="badge badge-light-danger">Inativo</span>
                                                    @endif
                                                @elseif($value === null)
                                                    <span class="text-muted">—</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <!--end::Grid Preview-->
                </div>
            </div>
        </div>
        {{-- end::Tab pane Grid --}}

        {{-- begin::Tab pane Form --}}
        <div class="tab-pane fade" id="tab_form">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Preview do Formulário</h3>
                    </div>
                    <div class="card-toolbar">
                        <span class="text-muted fs-7">Clique em qualquer campo para configurar</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($fields->isEmpty())
                        <div class="text-center text-muted py-10">
                            <i class="ki-duotone ki-notepad fs-3x mb-3"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span><span class="path4"></span><span
                                    class="path5"></span></i>
                            <p class="fs-6">Nenhum campo cadastrado.<br>Adicione campos na aba <strong>Campos</strong>
                                primeiro.</p>
                        </div>
                    @else
                        <div class="row g-5">
                            @foreach ($fields as $field)
                                @if (!$field->ui)
                                    @continue
                                @endif
                                @php
                                    $ui = $field->ui;
                                    $component = $ui->component ?? 'input';
                                    $gridCol = $ui->grid_col ?? 'col-md-12';
                                    $placeholder = $ui->placeholder ?? '';
                                    $isVisible = $ui->visible_create || $ui->visible_edit;
                                    $fieldCode = encodeId($field->id);
                                    $optionsData = is_array($ui->options)
                                        ? $ui->options
                                        : (is_string($ui->options)
                                            ? json_decode($ui->options, true)
                                            : []);
                                @endphp

                                <div class="{{ $gridCol }} {{ !$isVisible ? 'opacity-25' : '' }}"
                                    data-field-id="{{ $field->id }}" data-field-code="{{ $fieldCode }}"
                                    data-component="{{ $component }}" style="cursor: pointer;"
                                    title="Clique para configurar">

                                    @switch($component)
                                        @case('input')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input
                                                type="{{ $field->type === 'INTEGER' || $field->type === 'DECIMAL' || $field->type === 'BIGINT' ? 'number' : 'text' }}"
                                                class="form-control form-control-sm"
                                                placeholder="{{ $placeholder ?: $field->label }}" disabled>
                                            @if ($ui->mask)
                                                <span class="form-text text-muted">Máscara: {{ $ui->mask }}</span>
                                            @endif
                                        @break

                                        @case('textarea')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <textarea class="form-control form-control-sm" rows="3" placeholder="{{ $placeholder ?: $field->label }}"
                                                disabled></textarea>
                                        @break

                                        @case('select')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <select class="form-select form-select-sm" disabled>
                                                <option value="">{{ $placeholder ?: 'Selecione...' }}</option>
                                                @if (!empty($optionsData))
                                                    @foreach ($optionsData as $optVal => $optLabel)
                                                        @php
                                                            $parts = explode('|', $optLabel);
                                                            $label = $parts[0] ?? $optLabel;
                                                        @endphp
                                                        <option value="{{ $optVal }}">{{ $label }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @break

                                        @case('select_module')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <select class="form-select form-select-sm" disabled>
                                                <option value="">{{ $placeholder ?: 'Selecione...' }}</option>
                                                @if ($field->fk_table)
                                                    <option disabled>— Dados de: {{ $field->fk_table }}
                                                        ({{ $field->fk_label ?? 'name' }}) —</option>
                                                @endif
                                            </select>
                                            @if ($field->fk_table)
                                                <span class="form-text text-muted">FK:
                                                    {{ $field->fk_table }}.{{ $field->fk_column ?? 'id' }} →
                                                    {{ $field->fk_label ?? 'name' }}</span>
                                            @endif
                                        @break

                                        @case('switch')
                                            <label class="form-check form-switch form-check-custom form-check-sm mt-7">
                                                <input type="checkbox" class="form-check-input" disabled checked>
                                                <span class="form-check-label">
                                                    @if ($ui->icon)
                                                        <i class="{{ $ui->icon }} me-1"></i>
                                                    @endif
                                                    {{ $field->label }}
                                                </span>
                                            </label>
                                            @if (!empty($optionsData))
                                                <span class="form-text text-muted">
                                                    @foreach ($optionsData as $optVal => $optLabel)
                                                        @php $parts = explode('|', $optLabel); @endphp
                                                        <span
                                                            class="badge badge-light-{{ $parts[1] ?? 'primary' }} me-1">{{ $parts[0] }}</span>
                                                    @endforeach
                                                </span>
                                            @endif
                                        @break

                                        @case('checkbox')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <div>
                                                @if (!empty($optionsData))
                                                    @foreach ($optionsData as $optVal => $optLabel)
                                                        @php $parts = explode('|', $optLabel); @endphp
                                                        <label class="form-check form-check-custom form-check-sm mb-2">
                                                            <input type="checkbox" class="form-check-input" disabled>
                                                            <span class="form-check-label">{{ $parts[0] }}</span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <label class="form-check form-check-custom form-check-sm">
                                                        <input type="checkbox" class="form-check-input" disabled>
                                                        <span class="form-check-label">{{ $field->label }}</span>
                                                    </label>
                                                @endif
                                            </div>
                                        @break

                                        @case('radio')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <div>
                                                @if (!empty($optionsData))
                                                    @foreach ($optionsData as $optVal => $optLabel)
                                                        @php $parts = explode('|', $optLabel); @endphp
                                                        <label class="form-check form-check-custom form-check-sm mb-2">
                                                            <input type="radio" class="form-check-input"
                                                                name="preview_{{ $field->name }}" disabled>
                                                            <span class="form-check-label">{{ $parts[0] }}</span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <label class="form-check form-check-custom form-check-sm">
                                                        <input type="radio" class="form-check-input" disabled>
                                                        <span class="form-check-label">{{ $field->label }}</span>
                                                    </label>
                                                @endif
                                            </div>
                                        @break

                                        @case('date')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input type="text" class="form-control form-control-sm"
                                                placeholder="{{ $placeholder ?: 'DD/MM/AAAA' }}" disabled>
                                        @break

                                        @case('datetime')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input type="text" class="form-control form-control-sm"
                                                placeholder="{{ $placeholder ?: 'DD/MM/AAAA HH:MM' }}" disabled>
                                        @break

                                        @case('upload')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input type="file" class="form-control form-control-sm" disabled>
                                        @break

                                        @case('password')
                                            <label class="form-label {{ $field->required ? 'required' : '' }}">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input type="password" class="form-control form-control-sm"
                                                placeholder="{{ $placeholder ?: '••••••••' }}" disabled>
                                        @break

                                        @default
                                            <label class="form-label">
                                                @if ($ui->icon)
                                                    <i class="{{ $ui->icon }} me-1"></i>
                                                @endif
                                                {{ $field->label }}
                                            </label>
                                            <input type="text" class="form-control form-control-sm"
                                                placeholder="{{ $placeholder ?: $field->label }}" disabled>
                                    @endswitch

                                    @if ($ui->tooltip)
                                        <span class="form-text text-info">
                                            <i class="ki-duotone ki-information-3 fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span></i>
                                            {{ $ui->tooltip }}
                                        </span>
                                    @endif

                                    @if (!$isVisible)
                                        <span class="form-text text-warning">
                                            <i class="ki-duotone ki-eye-slash fs-7"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span><span
                                                    class="path4"></span></i>
                                            Oculto no formulário
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!--begin::Legenda-->
                        <div class="separator my-5"></div>
                        <div class="d-flex gap-4 text-muted fs-7">
                            <span><span class="text-danger">*</span> Campo obrigatório</span>
                            <span class="opacity-25">█</span> <span>Oculto no formulário</span>
                        </div>
                        <!--end::Legenda-->
                    @endif
                </div>
            </div>
        </div>
        {{-- end::Tab pane Form --}}

        {{-- begin::Tab pane Seeds --}}
        <div class="tab-pane fade" id="tab_seeds">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Seeds <span class="text-muted fs-7 ms-2" id="seeds_count">({{ $seeds->count() }}
                                registros)</span></h3>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-primary" id="btn_add_seed">
                            <i class="ki-duotone ki-plus fs-4"></i> Adicionar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if ($fields->isEmpty())
                        <div class="text-center text-muted py-10">
                            <i class="ki-duotone ki-notepad fs-3x mb-3"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span><span class="path4"></span><span
                                    class="path5"></span></i>
                            <p class="fs-6">Nenhum campo cadastrado.<br>Adicione campos na aba <strong>Campos</strong>
                                primeiro.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-200 align-middle gs-3 gy-3"
                                id="seeds_table">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th class="w-50px text-center">#</th>
                                        @foreach ($fields as $field)
                                            @if ($field->ui && $field->ui->visible_index)
                                                <th>{{ $field->label }}</th>
                                            @endif
                                        @endforeach
                                        <th class="w-100px text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="seeds_tbody">
                                    @forelse($seeds as $index => $seed)
                                        @php $seedData = is_array($seed->data) ? $seed->data : json_decode($seed->data, true); @endphp
                                        <tr data-seed-id="{{ $seed->id }}"
                                            data-seed-code="{{ encodeId($seed->id) }}">
                                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                                            @foreach ($fields as $field)
                                                @if ($field->ui && $field->ui->visible_index)
                                                    <td>
                                                        @php
                                                            $val = $seedData[$field->name] ?? null;
                                                            $optionsData = is_array($field->ui->options)
                                                                ? $field->ui->options
                                                                : (is_string($field->ui->options)
                                                                    ? json_decode($field->ui->options, true)
                                                                    : []);
                                                        @endphp
                                                        @if ($field->type === 'BOOLEAN' || !empty($optionsData))
                                                            @php
                                                                $key = is_bool($val)
                                                                    ? ($val
                                                                        ? 'true'
                                                                        : 'false')
                                                                    : (string) $val;
                                                                $optLabel =
                                                                    $optionsData[$key] ??
                                                                    ($optionsData[(string) $val] ?? null);
                                                            @endphp
                                                            @if ($optLabel)
                                                                @php $parts = explode('|', $optLabel); @endphp
                                                                <span
                                                                    class="badge badge-light-{{ $parts[1] ?? 'primary' }}">{{ $parts[0] }}</span>
                                                            @elseif(is_bool($val))
                                                                <span
                                                                    class="badge badge-light-{{ $val ? 'success' : 'danger' }}">{{ $val ? 'Ativo' : 'Inativo' }}</span>
                                                            @else
                                                                {{ $val ?? '—' }}
                                                            @endif
                                                        @elseif(is_null($val))
                                                            <span class="text-muted">—</span>
                                                        @else
                                                            {{ $val }}
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-light-primary btn-seed-edit"
                                                    data-seed-id="{{ $seed->id }}"
                                                    data-seed-code="{{ encodeId($seed->id) }}" title="Editar">
                                                    <i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span
                                                            class="path2"></span></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-light-danger btn-seed-delete"
                                                    data-seed-id="{{ $seed->id }}"
                                                    data-seed-code="{{ encodeId($seed->id) }}" title="Excluir">
                                                    <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span
                                                            class="path2"></span><span class="path3"></span><span
                                                            class="path4"></span><span class="path5"></span></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="seeds_empty_row">
                                            <td colspan="{{ $fields->filter(fn($f) => $f->ui && $f->ui->visible_index)->count() + 2 }}"
                                                class="text-center text-muted py-5">
                                                Nenhum seed cadastrado. Clique em "Adicionar" para criar o primeiro.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- end::Tab pane Seeds --}}
    </div>
    {{-- end::Tab content --}}

    <!--begin::Modal Grid Thead-->
    <div class="modal fade" id="modal_grid_thead" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header py-4">
                    <h3 class="modal-title">Configuração da Coluna</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="grid_thead_field_id">
                    <input type="hidden" id="grid_thead_field_code">

                    <h4 class="mb-0" id="grid_thead_field_title"></h4>
                    <span class="text-muted fs-7 mb-5 d-block" id="grid_thead_field_name"></span>

                    <!--begin::Grid Label-->
                    <div class="mb-5">
                        <label class="form-label">Label da coluna</label>
                        <input type="text" class="form-control form-control-sm" id="grid_thead_label" placeholder="Nome exibido no cabeçalho da grid">
                        <div class="form-text text-muted">Define o texto que aparece no cabeçalho da grid. Se vazio, usa o label padrão do campo.</div>
                    </div>
                    <!--end::Grid Label-->

                    <!--begin::Visible Index-->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fw-semibold">Visível na listagem</span>
                            <p class="text-muted fs-7 mb-0">Exibe esta coluna na grid</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="grid_thead_visible_index">
                        </div>
                    </div>
                    <!--end::Visible Index-->

                    <!--begin::Searchable-->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fw-semibold">Buscável</span>
                            <p class="text-muted fs-7 mb-0">Inclui na busca rápida (quick search)</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="grid_thead_searchable">
                        </div>
                    </div>
                    <!--end::Searchable-->

                    <!--begin::Sortable-->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fw-semibold">Ordenável</span>
                            <p class="text-muted fs-7 mb-0">Permite ordenar a grid por esta coluna</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="grid_thead_sortable">
                        </div>
                    </div>
                    <!--end::Sortable-->

                    <!--begin::Width-->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Largura da coluna</label>
                        <input type="text" class="form-control form-control-sm" id="grid_thead_width"
                            placeholder="Ex: 100px, 15%, auto (vazio = automática)">
                        <div class="form-text">Deixe vazio para largura automática</div>
                    </div>
                    <!--end::Width-->

                    <div class="separator my-4"></div>

                    <!--begin::Default Sort-->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fw-semibold">Ordenação padrão</span>
                            <p class="text-muted fs-7 mb-0">Define esta coluna como ordenação padrão da grid</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="grid_thead_default_sort">
                        </div>
                    </div>

                    <div class="mb-0" id="grid_thead_sort_direction_wrapper" style="display:none">
                        <label class="form-label fw-semibold">Direção da ordenação</label>
                        <select class="form-select form-select-sm" id="grid_thead_sort_direction">
                            <option value="asc">Crescente (A→Z, 0→9)</option>
                            <option value="desc">Decrescente (Z→A, 9→0)</option>
                        </select>
                    </div>
                    <!--end::Default Sort-->
                </div>
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn_save_grid_thead">
                        <i class="ki-outline ki-check fs-4 me-1"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Grid Thead-->

    <!--begin::Modal Grid Tbody-->
    <div class="modal fade" id="modal_grid_tbody" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header py-4">
                    <h3 class="modal-title">Configuração do Dado</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="grid_tbody_field_id">
                    <input type="hidden" id="grid_tbody_field_code">

                    <div class="fw-bold fs-5 mb-5" id="grid_tbody_field_label"></div>

                    <!--begin::Grid Template-->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Template</label>
                        <input type="text" class="form-control form-control-sm" id="grid_tbody_template"
                            placeholder="Ex: {first_name} {surname}">
                        <div class="form-text">Combine campos usando {nome_do_campo}. Deixe vazio para exibir o valor
                            direto.</div>
                    </div>
                    <!--end::Grid Template-->

                    <!--begin::Grid Link-->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Link</label>
                        <select class="form-select form-select-sm" id="grid_tbody_link">
                            <option value="">Nenhum (texto simples)</option>
                            <option value="{show}">Link para visualização (show)</option>
                            <option value="{edit}">Link para edição (edit)</option>
                        </select>
                        <div class="form-text">Transforma o valor em link clicável na grid.</div>
                    </div>
                    <!--end::Grid Link-->

                    <!--begin::Options (Badges)-->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Badges / Labels</label>
                        <textarea class="form-control form-control-sm" id="grid_tbody_options" rows="4"
                            placeholder='Ex: {"true":"Ativo|success","false":"Inativo|danger"}'></textarea>
                        <div class="form-text">JSON com mapeamento valor → "label|cor". Cores: success, danger, warning,
                            info, primary. Deixe vazio para sem badge.</div>
                    </div>
                    <!--end::Options-->

                    <!--begin::Grid Actions-->
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Ações customizadas</label>
                        <textarea class="form-control form-control-sm" id="grid_tbody_actions" rows="3"
                            placeholder='Ex: [{"label":"Ver","action":"show","icon":"eye"}]'></textarea>
                        <div class="form-text">JSON com ações extras na coluna. Deixe vazio para sem ações.</div>
                    </div>
                    <!--end::Grid Actions-->
                </div>
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn_save_grid_tbody">
                        <i class="ki-outline ki-check fs-4 me-1"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Grid Tbody-->

    <!--begin::Modal Form Field-->
    <div class="modal fade" id="modal_form_field" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-4">
                    <h3 class="modal-title">Configuração do Campo</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <!--begin::Hidden-->
                    <input type="hidden" id="form_field_id">
                    <input type="hidden" id="form_field_code">
                    <input type="hidden" id="form_field_component">
                    <input type="hidden" id="form_field_unique" value="0">
                    <!--end::Hidden-->

                    <!--begin::Info do campo-->
                    <div class="d-flex align-items-center gap-3 mb-5 p-3 bg-light-primary rounded">
                        <div>
                            <span class="fw-bold fs-5" id="form_field_label_display">—</span>
                            <span class="text-muted fs-7 ms-2" id="form_field_component_display">input</span>
                        </div>
                    </div>
                    <!--end::Info do campo-->

                    <!--begin::Campos comuns (sempre visíveis)-->
                    <div class="row g-4">
                        <!--begin::Componente-->
                        <div class="col-md-6">
                            <label class="form-label">Componente</label>
                            <select id="form_component" class="form-select form-select-sm">
                                <option value="input">Input (texto/número)</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select (opções manuais)</option>
                                <option value="select_module">Select Module (FK)</option>
                                <option value="switch">Switch (on/off)</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                                <option value="date">Date</option>
                                <option value="datetime">DateTime</option>
                                <option value="upload">Upload</option>
                                <option value="password">Password</option>
                            </select>
                            <span class="form-text text-muted">Tipo de componente visual no formulário</span>
                        </div>
                        <!--end::Componente-->

                        <!--begin::Grid Col-->
                        <div class="col-md-6">
                            <label class="form-label">Largura (grid_col)</label>
                            <select id="form_grid_col" class="form-select form-select-sm">
                                <option value="col-md-2">col-md-2 (1/6)</option>
                                <option value="col-md-3">col-md-3 (1/4)</option>
                                <option value="col-md-4">col-md-4 (1/3)</option>
                                <option value="col-md-6">col-md-6 (1/2)</option>
                                <option value="col-md-8">col-md-8 (2/3)</option>
                                <option value="col-md-12">col-md-12 (inteira)</option>
                            </select>
                            <span class="form-text text-muted">Largura do campo no formulário</span>
                        </div>
                        <!--end::Grid Col-->

                        <!--begin::Ícone-->
                        <div class="col-md-6">
                            <label class="form-label">Ícone</label>
                            <input type="text" id="form_icon" class="form-control form-control-sm"
                                placeholder="ki-duotone ki-user">
                            <span class="form-text text-muted">Classe CSS do ícone (KTIcon ou FontAwesome)</span>
                        </div>
                        <!--end::Ícone-->

                        <!--begin::Tooltip Direction-->
                        <div class="col-md-6">
                            <label class="form-label">Direção do tooltip</label>
                            <select id="form_tooltip_direction" class="form-select form-select-sm">
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <!--end::Tooltip Direction-->

                        <!--begin::Tooltip-->
                        <div class="col-md-12">
                            <label class="form-label">Tooltip</label>
                            <input type="text" id="form_tooltip" class="form-control form-control-sm"
                                placeholder="Texto de ajuda exibido abaixo do campo">
                            <span class="form-text text-muted">Texto informativo exibido abaixo do campo</span>
                        </div>
                        <!--end::Tooltip-->
                    </div>
                    <!--end::Campos comuns-->

                    <!--begin::Separator-->
                    <div class="separator my-5"></div>
                    <!--end::Separator-->

                    <!--begin::Visibilidade-->
                    <h5 class="mb-4">Visibilidade</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-check form-switch form-check-custom form-check-sm">
                                <input type="checkbox" class="form-check-input" id="form_visible_create">
                                <span class="form-check-label">Visível ao criar</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check form-switch form-check-custom form-check-sm">
                                <input type="checkbox" class="form-check-input" id="form_visible_edit">
                                <span class="form-check-label">Visível ao editar</span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="form-check form-switch form-check-custom form-check-sm">
                                <input type="checkbox" class="form-check-input" id="form_visible_show">
                                <span class="form-check-label">Visível ao visualizar</span>
                            </label>
                        </div>
                    </div>
                    <!--end::Visibilidade-->

                    <!--begin::Separator Condicional-->
                    <div class="separator my-5" id="form_section_separator_conditional"></div>
                    <!--end::Separator Condicional-->

                    <!--begin::Seção Placeholder (oculta para switch e upload)-->
                    <div id="form_section_placeholder" class="mb-4">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Placeholder</label>
                                <input type="text" id="form_placeholder" class="form-control form-control-sm"
                                    placeholder="Texto exibido quando o campo está vazio">
                            </div>
                        </div>
                    </div>
                    <!--end::Seção Placeholder-->

                    <!--begin::Seção Máscara (apenas para input)-->
                    <div id="form_section_mask" class="mb-4 d-none">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Máscara de entrada</label>
                                <input type="text" id="form_mask" class="form-control form-control-sm"
                                    placeholder="Ex: (99) 99999-9999 ou 999.999.999-99">
                                <span class="form-text text-muted">Use 9 para dígitos, a para letras, * para qualquer.
                                    Deixe vazio para sem máscara.</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Seção Máscara-->

                    <!--begin::Seção Options (select, switch, checkbox, radio)-->
                    <div id="form_section_options" class="mb-4 d-none">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label">Opções (JSON)</label>
                                <textarea id="form_options" class="form-control form-control-sm" rows="4"
                                    placeholder='{"valor":"Label|cor", "1":"Ativo|success", "0":"Inativo|danger"}'></textarea>
                                <span class="form-text text-muted">
                                    JSON: chave = valor gravado, valor = "Label|cor_badge".<br>
                                    Cores: success, danger, warning, info, primary, secondary
                                </span>
                            </div>
                        </div>
                    </div>
                    <!--end::Seção Options-->

                    <!--begin::Seção FK (apenas para select_module)-->
                    <div id="form_section_fk" class="mb-4 d-none">
                        <div class="separator my-5"></div>
                        <h5 class="mb-4">
                            <i class="ki-duotone ki-data fs-4 me-1"><span class="path1"></span><span
                                    class="path2"></span><span class="path3"></span><span class="path4"></span><span
                                    class="path5"></span></i>
                            Origem dos Dados (Foreign Key)
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label required">Módulo (tabela)</label>
                                <select id="form_fk_table" class="form-select form-select-sm">
                                    <option value="">Selecione o módulo...</option>
                                    @foreach (\App\Models\Landlord\Module::where('status', true)->orderBy('name')->get() as $mod)
                                        <option value="{{ $mod->slug }}">{{ $mod->name }}</option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted">Módulo que contém os dados</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Campo Chave</label>
                                <select id="form_fk_column" class="form-select form-select-sm">
                                    <option value="">Selecione...</option>
                                </select>
                                <span class="form-text text-muted">Coluna da FK (geralmente "id")</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Campo Label</label>
                                <select id="form_fk_label" class="form-select form-select-sm">
                                    <option value="">Selecione...</option>
                                </select>
                                <span class="form-text text-muted">Coluna exibida no select (geralmente "name")</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Seção FK-->

                    <!--begin::Seção Unique (apenas se campo tem unique=true)-->
                    <div id="form_section_unique" class="mb-4 d-none">
                        <div class="separator my-5"></div>
                        <h5 class="mb-4">
                            <i class="ki-duotone ki-shield-tick fs-4 me-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                            Validação de Unicidade Remota
                        </h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Módulo para validar</label>
                                <select id="form_unique_table" class="form-select form-select-sm">
                                    <option value="">Selecione o módulo...</option>
                                    @foreach (\App\Models\Landlord\Module::where('status', true)->orderBy('name')->get() as $mod)
                                        <option value="{{ $mod->slug }}">{{ $mod->name }}</option>
                                    @endforeach
                                </select>
                                <span class="form-text text-muted">Tabela onde verificar duplicatas</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Campo para comparar</label>
                                <select id="form_unique_column" class="form-select form-select-sm">
                                    <option value="">Selecione...</option>
                                </select>
                                <span class="form-text text-muted">Coluna para verificar unicidade</span>
                            </div>
                        </div>
                    </div>
                    <!--end::Seção Unique-->

                </div>
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn_save_form_field">
                        <i class="ki-duotone ki-check fs-4"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Form Field-->

    <!--begin::Modal Seed-->
    <div class="modal fade" id="modal_seed" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modal_seed_title">Adicionar Seed</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" id="seed_id" value="">
                    <input type="hidden" id="seed_code" value="">
                    <input type="hidden" id="seed_mode" value="create">

                    <div class="row g-4">
                        @foreach ($fields as $field)
                            @if ($field->ui)
                                @php
                                    $comp = $field->ui->component ?? 'input';
                                    $gridCol = $field->ui->grid_col ?? 'col-md-12';
                                    $placeholder = $field->ui->placeholder ?? '';
                                    $optionsRaw = $field->ui->options;
                                    $options = is_array($optionsRaw)
                                        ? $optionsRaw
                                        : (is_string($optionsRaw)
                                            ? json_decode($optionsRaw, true)
                                            : []);
                                    $isRequired = $field->required;
                                @endphp
                                <div class="{{ $gridCol }}" data-seed-field="{{ $field->name }}">
                                    @switch($comp)
                                        @case('switch')
                                            <label class="form-label">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input seed-field" type="checkbox"
                                                    id="seed_{{ $field->name }}" data-field-name="{{ $field->name }}"
                                                    data-field-type="{{ $field->type }}" data-component="{{ $comp }}"
                                                    value="1" checked>
                                                <label class="form-check-label" for="seed_{{ $field->name }}">
                                                    @if (!empty($options))
                                                        @php
                                                            $onLabel =
                                                                $options['1'] ?? ($options['true'] ?? 'Ativo|success');
                                                            $onParts = explode('|', $onLabel);
                                                        @endphp
                                                        {{ $onParts[0] }}
                                                    @else
                                                        Ativo
                                                    @endif
                                                </label>
                                            </div>
                                        @break

                                        @case('select')
                                            <label class="form-label" for="seed_{{ $field->name }}">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <select class="form-select form-select-sm seed-field" id="seed_{{ $field->name }}"
                                                data-field-name="{{ $field->name }}" data-field-type="{{ $field->type }}"
                                                data-component="{{ $comp }}">
                                                <option value="">Selecione...</option>
                                                @if (!empty($options))
                                                    @foreach ($options as $optKey => $optVal)
                                                        @php $optParts = explode('|', $optVal); @endphp
                                                        <option value="{{ $optKey }}">{{ $optParts[0] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @break

                                        @case('textarea')
                                            <label class="form-label" for="seed_{{ $field->name }}">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <textarea class="form-control form-control-sm seed-field" id="seed_{{ $field->name }}"
                                                data-field-name="{{ $field->name }}" data-field-type="{{ $field->type }}"
                                                data-component="{{ $comp }}" rows="3" placeholder="{{ $placeholder }}"></textarea>
                                        @break

                                        @case('checkbox')
                                            <label class="form-label">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            @if (!empty($options))
                                                @foreach ($options as $optKey => $optVal)
                                                    @php $optParts = explode('|', $optVal); @endphp
                                                    <div class="form-check form-check-custom form-check-solid mb-2">
                                                        <input
                                                            class="form-check-input seed-field seed-checkbox-{{ $field->name }}"
                                                            type="checkbox" data-field-name="{{ $field->name }}"
                                                            data-field-type="{{ $field->type }}"
                                                            data-component="{{ $comp }}" value="{{ $optKey }}"
                                                            id="seed_{{ $field->name }}_{{ $optKey }}">
                                                        <label class="form-check-label"
                                                            for="seed_{{ $field->name }}_{{ $optKey }}">{{ $optParts[0] }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @break

                                        @case('radio')
                                            <label class="form-label">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            @if (!empty($options))
                                                @foreach ($options as $optKey => $optVal)
                                                    @php $optParts = explode('|', $optVal); @endphp
                                                    <div class="form-check form-check-custom form-check-solid mb-2">
                                                        <input class="form-check-input seed-field" type="radio"
                                                            name="seed_radio_{{ $field->name }}"
                                                            data-field-name="{{ $field->name }}"
                                                            data-field-type="{{ $field->type }}"
                                                            data-component="{{ $comp }}" value="{{ $optKey }}"
                                                            id="seed_{{ $field->name }}_{{ $optKey }}">
                                                        <label class="form-check-label"
                                                            for="seed_{{ $field->name }}_{{ $optKey }}">{{ $optParts[0] }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @break

                                        @case('date')
                                            <label class="form-label" for="seed_{{ $field->name }}">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="date" class="form-control form-control-sm seed-field"
                                                id="seed_{{ $field->name }}" data-field-name="{{ $field->name }}"
                                                data-field-type="{{ $field->type }}" data-component="{{ $comp }}">
                                        @break

                                        @case('datetime')
                                            <label class="form-label" for="seed_{{ $field->name }}">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="datetime-local" class="form-control form-control-sm seed-field"
                                                id="seed_{{ $field->name }}" data-field-name="{{ $field->name }}"
                                                data-field-type="{{ $field->type }}" data-component="{{ $comp }}">
                                        @break

                                        @default
                                            <label class="form-label" for="seed_{{ $field->name }}">
                                                {{ $field->label }}
                                                @if ($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input
                                                type="{{ in_array($field->type, ['INTEGER', 'DECIMAL', 'BIGINT']) ? 'number' : 'text' }}"
                                                class="form-control form-control-sm seed-field" id="seed_{{ $field->name }}"
                                                data-field-name="{{ $field->name }}" data-field-type="{{ $field->type }}"
                                                data-component="{{ $comp }}" placeholder="{{ $placeholder }}"
                                                @if ($field->type === 'DECIMAL') step="0.01" @endif>
                                        @break
                                    @endswitch
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_save_seed">
                        <i class="ki-duotone ki-check fs-4"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Seed-->
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

        // Toggle card submódulos ao trocar type
        const typeSelect = document.querySelector('[name="type"]');
        const cardSubmodules = document.getElementById('card_submodules');

        if (typeSelect && cardSubmodules) {
            typeSelect.addEventListener('change', function() {
                cardSubmodules.style.display = this.value === 'module' ? '' : 'none';
            });
        }

        // Inicializar Select2 em todos os selects
        if (typeof $.fn.select2 !== 'undefined') {
            $('select.form-select').select2({
                minimumResultsForSearch: -1 // esconde busca em selects pequenos
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

                console.log('🔵 Campo:', id, '| name:', nameVal, '| type:', typeVal, '| length:',
                lengthVal);

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
                console.log('🔵 Body:', JSON.stringify(method === 'PUT' ? {
                    ...data,
                    _method: 'PUT'
                } : data));

                const fetchOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(method === 'PUT' ? {
                        ...data,
                        _method: 'PUT'
                    } : data)
                };

                console.log('🔵 CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content
                    ?.substring(0, 10) + '...');

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
                        return {
                            success: false,
                            message: err.message
                        };
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
                    var hadNewFields = results.some(function(r) {
                        return r && r.data && r.data.id;
                    });

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
                        .then(function(r) {
                            return r.json();
                        })
                        .then(function(data) {
                            if (data.success) {
                                row.remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Excluído!',
                                    timer: 1000,
                                    showConfirmButton: false
                                });
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
                    document.querySelectorAll('#fields_tbody tr.field-row[data-origin="custom"]').forEach(
                        function(row) {
                            if (!row.dataset.id.startsWith('new_')) {
                                ids.push(row.dataset.id);
                            }
                        });
                    if (ids.length > 0) {
                        fetch(`{{ route('landlord.modules.fields.reorder', encodeId($module->id)) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        }).then(function(response) {
                            if (response.ok) {
                                const tbody = document.getElementById('fields_tbody');
                                const allRows = Array.from(tbody.querySelectorAll('tr.field-row'));

                                const idRow = allRows.find(r => r.dataset.origin === 'system' && r
                                    .querySelector('input[name="name"]')?.value === 'id');
                                const customRows = allRows.filter(r => r.dataset.origin === 'custom');
                                const systemRest = allRows.filter(r => r.dataset.origin === 'system' &&
                                    r.querySelector('input[name="name"]')?.value !== 'id');

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

        // --- Modal Grid Thead ---
        @php
            $fieldsTheadMap = [];
            foreach ($fields as $f) {
                $ui = $f->ui;
                $fieldsTheadMap[$f->id] = [
                    'name'          => $f->name,
                    'label'         => $f->label,
                    'grid_label'    => $ui->grid_label ?? $f->label,
                    'visible_index' => $ui ? (bool)$ui->visible_index : true,
                    'searchable'    => $ui ? (bool)$ui->searchable : true,
                    'sortable'      => $ui ? (bool)$ui->sortable : true,
                    'width_index'   => $ui->width_index ?? '',
                ];
            }
        @endphp
        var fieldsTheadData = {!! json_encode($fieldsTheadMap) !!};

        // Abrir modal thead ao clicar no cabeçalho da grid
        document.querySelectorAll('#tab_grid thead th[data-field-id]').forEach(function(th) {
            th.addEventListener('click', function() {
                const fieldId = this.dataset.fieldId;
                const fieldCode = this.dataset.fieldCode;
                const label = this.querySelector('span').textContent.trim();

                const theadData = fieldsTheadData[fieldId] || {};

                document.getElementById('grid_thead_field_id').value = fieldId;
                document.getElementById('grid_thead_field_code').value = fieldCode;
                document.getElementById('grid_thead_field_title').textContent = theadData.label || theadData.name;
                document.getElementById('grid_thead_field_name').textContent = theadData.name || '';
                document.getElementById('grid_thead_label').value = theadData.grid_label || '';
                document.getElementById('grid_thead_visible_index').checked = this.dataset.visible === '1';
                document.getElementById('grid_thead_searchable').checked = this.dataset.searchable === '1';
                document.getElementById('grid_thead_sortable').checked = this.dataset.sortable === '1';
                document.getElementById('grid_thead_width').value = this.dataset.width || '';

                // Default sort
                const fieldName = '{{ $module->default_sort_field }}';
                const isDefaultSort = this.querySelector('.text-success') !== null;
                document.getElementById('grid_thead_default_sort').checked = isDefaultSort;
                document.getElementById('grid_thead_sort_direction').value =
                    '{{ $module->default_sort_direction }}';
                document.getElementById('grid_thead_sort_direction_wrapper').style.display = isDefaultSort ?
                    '' : 'none';

                var modal = new bootstrap.Modal(document.getElementById('modal_grid_thead'));
                modal.show();
            });
        });

        // Toggle direção ao marcar/desmarcar default sort
        document.getElementById('grid_thead_default_sort').addEventListener('change', function() {
            document.getElementById('grid_thead_sort_direction_wrapper').style.display = this.checked ? '' : 'none';
        });

        // Salvar configuração da coluna (thead)
        document.getElementById('btn_save_grid_thead').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const fieldCode = document.getElementById('grid_thead_field_code').value;
            const moduleCode = '{{ encodeId($module->id) }}';

            const payload = {
                grid_label: document.getElementById('grid_thead_label').value || null,
                visible_index: document.getElementById('grid_thead_visible_index').checked,
                searchable: document.getElementById('grid_thead_searchable').checked,
                sortable: document.getElementById('grid_thead_sortable').checked,
                width_index: document.getElementById('grid_thead_width').value || null,
                default_sort: document.getElementById('grid_thead_default_sort').checked,
                sort_direction: document.getElementById('grid_thead_sort_direction').value,
            };

            fetch(`/modules/${moduleCode}/fields/${fieldCode}/grid`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza data-attributes e ícones no thead
                        const th = document.querySelector(`#tab_grid thead th[data-field-code="${fieldCode}"]`);
                        if (th) {
                            th.dataset.visible = payload.visible_index ? '1' : '0';
                            th.dataset.searchable = payload.searchable ? '1' : '0';
                            th.dataset.sortable = payload.sortable ? '1' : '0';
                            th.dataset.width = payload.width_index || '';
                            th.style.width = payload.width_index || '';

                            // Toggle opacidade
                            th.classList.toggle('opacity-25', !payload.visible_index);

                            // Atualiza ícones
                            th.querySelectorAll('i').forEach(i => i.remove());
                            const span = th.querySelector('span');

                            // Atualizar texto do cabeçalho na grid preview
                            const fieldId = document.getElementById('grid_thead_field_id').value;
                            const newLabel = document.getElementById('grid_thead_label').value;
                            if (span && newLabel) {
                                span.textContent = newLabel;
                            }

                            // Atualizar mapa local para manter consistência
                            if (typeof fieldsTheadData !== 'undefined' && fieldsTheadData[fieldId]) {
                                fieldsTheadData[fieldId].grid_label = newLabel || fieldsTheadData[fieldId].label;
                            }

                            if (payload.sortable) {
                                span.insertAdjacentHTML('afterend',
                                    ' <i class="ki-outline ki-arrow-up-down fs-7 ms-1 text-primary"></i>');
                            }
                            if (payload.searchable) {
                                span.insertAdjacentHTML('afterend',
                                    ' <i class="ki-outline ki-magnifier fs-7 ms-1 text-info"></i>');
                            }
                            if (data.data.default_sort_field === data.data.field_name) {
                                span.insertAdjacentHTML('afterend',
                                    ' <i class="ki-outline ki-sort fs-7 ms-1 text-success"></i>');
                            }

                            // Atualiza opacidade das células do tbody na mesma coluna
                            const fieldIdForTbody = th.dataset.fieldId;
                            document.querySelectorAll(`#tab_grid tbody td[data-field-id="${fieldIdForTbody}"]`).forEach(
                                td => {
                                    td.classList.toggle('opacity-25', !payload.visible_index);
                                });

                            // Remove ícone de sort de outros cabeçalhos se este virou default
                            if (payload.default_sort) {
                                document.querySelectorAll('#tab_grid thead th .text-success').forEach(icon => {
                                    if (!icon.closest('th').dataset.fieldCode !== fieldCode) {
                                        icon.remove();
                                    }
                                });
                            }
                        }

                        bootstrap.Modal.getInstance(document.getElementById('modal_grid_thead')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Salvo!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao salvar configuração'
                    });
                });
        });

        // --- Modal Grid Tbody ---
        // Dados atuais dos fields UI para popular o modal tbody
        @php
            $fieldsUiMap = [];
            foreach ($fields as $f) {
                if (!$f->ui) {
                    continue;
                }
                $fieldsUiMap[$f->id] = [
                    'label' => $f->label,
                    'grid_template' => $f->ui->grid_template ?? '',
                    'grid_link' => $f->ui->grid_link ?? '',
                    'options' => $f->ui->options ? json_encode($f->ui->options) : '',
                    'grid_actions' => $f->ui->grid_actions ? json_encode($f->ui->grid_actions) : '',
                ];
            }
        @endphp
        var fieldsUiData = {!! json_encode($fieldsUiMap) !!};

        // Abrir modal tbody ao clicar nas células
        document.querySelectorAll('#tab_grid tbody td[data-field-id]').forEach(function(td) {
            td.addEventListener('click', function() {
                const fieldId = this.dataset.fieldId;
                const fieldCode = this.dataset.fieldCode;
                const data = fieldsUiData[fieldId] || {};

                document.getElementById('grid_tbody_field_id').value = fieldId;
                document.getElementById('grid_tbody_field_code').value = fieldCode;
                document.getElementById('grid_tbody_field_label').textContent = data.label || '';
                document.getElementById('grid_tbody_template').value = data.grid_template || '';
                document.getElementById('grid_tbody_link').value = data.grid_link || '';
                document.getElementById('grid_tbody_options').value = data.options || '';
                document.getElementById('grid_tbody_actions').value = data.grid_actions || '';

                var modal = new bootstrap.Modal(document.getElementById('modal_grid_tbody'));
                modal.show();
            });
        });

        // Salvar configuração do dado (tbody)
        document.getElementById('btn_save_grid_tbody').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const fieldCode = document.getElementById('grid_tbody_field_code').value;
            const fieldId = document.getElementById('grid_tbody_field_id').value;
            const moduleCode = '{{ encodeId($module->id) }}';

            // Validar JSON dos campos options e grid_actions
            const optionsVal = document.getElementById('grid_tbody_options').value.trim();
            const actionsVal = document.getElementById('grid_tbody_actions').value.trim();

            if (optionsVal) {
                try {
                    JSON.parse(optionsVal);
                } catch (err) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'JSON inválido',
                        text: 'O campo Badges/Labels contém JSON inválido.'
                    });
                    return;
                }
            }
            if (actionsVal) {
                try {
                    JSON.parse(actionsVal);
                } catch (err) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'JSON inválido',
                        text: 'O campo Ações contém JSON inválido.'
                    });
                    return;
                }
            }

            const payload = {
                grid_template: document.getElementById('grid_tbody_template').value || null,
                grid_link: document.getElementById('grid_tbody_link').value || null,
                options: optionsVal || null,
                grid_actions: actionsVal || null,
            };

            fetch(`/modules/${moduleCode}/fields/${fieldCode}/grid`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza dados locais
                        if (fieldsUiData[fieldId]) {
                            fieldsUiData[fieldId].grid_template = payload.grid_template || '';
                            fieldsUiData[fieldId].grid_link = payload.grid_link || '';
                            fieldsUiData[fieldId].options = payload.options || '';
                            fieldsUiData[fieldId].grid_actions = payload.grid_actions || '';
                        }

                        bootstrap.Modal.getInstance(document.getElementById('modal_grid_tbody')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Salvo!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao salvar configuração'
                    });
                });
        });

        // ============================================
        // ABA FORM — Modal de configuração de campo
        // ============================================

        // Mapa de dados dos fields com UI para a aba Form
        const fieldsFormData = {
            @foreach ($fields as $field)
                @if ($field->ui)
                    '{{ $field->id }}': {
                        code: '{{ encodeId($field->id) }}',
                        name: '{{ $field->name }}',
                        label: '{{ $field->label }}',
                        type: '{{ $field->type }}',
                        unique: {{ $field->unique ? 'true' : 'false' }},
                        fk_table: '{{ $field->fk_table ?? '' }}',
                        fk_column: '{{ $field->fk_column ?? '' }}',
                        fk_label: '{{ $field->fk_label ?? '' }}',
                        unique_table: '{{ $field->unique_table ?? '' }}',
                        unique_column: '{{ $field->unique_column ?? '' }}',
                        component: '{{ $field->ui->component ?? 'input' }}',
                        grid_col: '{{ $field->ui->grid_col ?? 'col-md-12' }}',
                        icon: '{{ $field->ui->icon ?? '' }}',
                        placeholder: '{{ $field->ui->placeholder ?? '' }}',
                        mask: '{{ $field->ui->mask ?? '' }}',
                        tooltip: `{{ $field->ui->tooltip ?? '' }}`,
                        tooltip_direction: '{{ $field->ui->tooltip_direction ?? 'top' }}',
                        options: '{!! $field->ui->options
                            ? (is_array($field->ui->options)
                                ? json_encode($field->ui->options)
                                : $field->ui->options)
                            : '' !!}',
                        visible_create: {{ $field->ui->visible_create ? 'true' : 'false' }},
                        visible_edit: {{ $field->ui->visible_edit ? 'true' : 'false' }},
                        visible_show: {{ $field->ui->visible_show ? 'true' : 'false' }},
                    },
                @endif
            @endforeach
        };

        // Função para adaptar seções condicionais por component
        function adaptFormSections(component, isUnique) {
            const placeholderSection = document.getElementById('form_section_placeholder');
            const maskSection = document.getElementById('form_section_mask');
            const optionsSection = document.getElementById('form_section_options');
            const fkSection = document.getElementById('form_section_fk');
            const uniqueSection = document.getElementById('form_section_unique');
            const separatorCond = document.getElementById('form_section_separator_conditional');

            // Placeholder: todos exceto switch e upload
            const showPlaceholder = !['switch', 'upload', 'checkbox', 'radio'].includes(component);
            placeholderSection.classList.toggle('d-none', !showPlaceholder);

            // Máscara: apenas input
            maskSection.classList.toggle('d-none', component !== 'input');

            // Options: select, switch, checkbox, radio
            const showOptions = ['select', 'switch', 'checkbox', 'radio'].includes(component);
            optionsSection.classList.toggle('d-none', !showOptions);

            // FK: apenas select_module
            fkSection.classList.toggle('d-none', component !== 'select_module');

            // Unique: apenas se o campo tem unique=true
            uniqueSection.classList.toggle('d-none', !isUnique);

            // Separador condicional: mostra se alguma seção condicional está visível
            const anyVisible = showPlaceholder || (component === 'input') || showOptions || (component ===
                'select_module') || isUnique;
            separatorCond.classList.toggle('d-none', !anyVisible);
        }

        // Função AJAX para carregar campos de um módulo
        function loadModuleFields(moduleSlug, targetColumnSelect, targetLabelSelect, selectedColumn, selectedLabel) {
            if (!moduleSlug) {
                targetColumnSelect.innerHTML = '<option value="">Selecione...</option>';
                if (targetLabelSelect) targetLabelSelect.innerHTML = '<option value="">Selecione...</option>';
                return;
            }

            fetch(`/modules/${moduleSlug}/fields-list`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(fields => {
                    let colOpts = '<option value="">Selecione...</option>';
                    let labelOpts = '<option value="">Selecione...</option>';
                    fields.forEach(f => {
                        colOpts +=
                            `<option value="${f.name}" ${f.name === selectedColumn ? 'selected' : ''}>${f.label} (${f.name})</option>`;
                        labelOpts +=
                            `<option value="${f.name}" ${f.name === selectedLabel ? 'selected' : ''}>${f.label} (${f.name})</option>`;
                    });
                    targetColumnSelect.innerHTML = colOpts;
                    if (targetLabelSelect) targetLabelSelect.innerHTML = labelOpts;
                })
                .catch(() => {
                    targetColumnSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                    if (targetLabelSelect) targetLabelSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                });
        }

        // Event listener: abrir modal ao clicar no campo do preview
        document.querySelectorAll('#pane_form [data-field-id]').forEach(el => {
            el.addEventListener('click', function() {
                const fieldId = this.dataset.fieldId;
                const data = fieldsFormData[fieldId];
                if (!data) return;

                // Popular hidden fields
                document.getElementById('form_field_id').value = fieldId;
                document.getElementById('form_field_code').value = data.code;
                document.getElementById('form_field_component').value = data.component;
                document.getElementById('form_field_unique').value = data.unique ? '1' : '0';

                // Info display
                document.getElementById('form_field_label_display').textContent = data.label;
                document.getElementById('form_field_component_display').textContent = data.component;

                // Campos comuns
                document.getElementById('form_component').value = data.component;
                document.getElementById('form_grid_col').value = data.grid_col;
                document.getElementById('form_icon').value = data.icon;
                document.getElementById('form_tooltip').value = data.tooltip;
                document.getElementById('form_tooltip_direction').value = data.tooltip_direction;

                // Visibilidade
                document.getElementById('form_visible_create').checked = data.visible_create;
                document.getElementById('form_visible_edit').checked = data.visible_edit;
                document.getElementById('form_visible_show').checked = data.visible_show;

                // Seções condicionais
                document.getElementById('form_placeholder').value = data.placeholder;
                document.getElementById('form_mask').value = data.mask;

                // Options: se string não-vazia, formatar como JSON indentado
                const optionsField = document.getElementById('form_options');
                if (data.options && data.options.trim()) {
                    try {
                        const parsed = JSON.parse(data.options);
                        optionsField.value = JSON.stringify(parsed, null, 2);
                    } catch (e) {
                        optionsField.value = data.options;
                    }
                } else {
                    optionsField.value = '';
                }

                // FK: popular selects
                document.getElementById('form_fk_table').value = data.fk_table;
                if (data.fk_table) {
                    loadModuleFields(
                        data.fk_table,
                        document.getElementById('form_fk_column'),
                        document.getElementById('form_fk_label'),
                        data.fk_column,
                        data.fk_label
                    );
                } else {
                    document.getElementById('form_fk_column').innerHTML =
                        '<option value="">Selecione...</option>';
                    document.getElementById('form_fk_label').innerHTML =
                        '<option value="">Selecione...</option>';
                }

                // Unique: popular selects
                document.getElementById('form_unique_table').value = data.unique_table;
                if (data.unique_table) {
                    loadModuleFields(
                        data.unique_table,
                        document.getElementById('form_unique_column'),
                        null,
                        data.unique_column,
                        null
                    );
                } else {
                    document.getElementById('form_unique_column').innerHTML =
                        '<option value="">Selecione...</option>';
                }

                // Adaptar seções visíveis
                adaptFormSections(data.component, data.unique);

                // Abrir modal
                new bootstrap.Modal(document.getElementById('modal_form_field')).show();
            });
        });

        // Event listener: ao trocar component no modal, re-adaptar seções
        document.getElementById('form_component').addEventListener('change', function() {
            const component = this.value;
            const isUnique = document.getElementById('form_field_unique').value === '1';
            adaptFormSections(component, isUnique);
            document.getElementById('form_field_component_display').textContent = component;
        });

        // Event listener: AJAX ao trocar módulo FK
        document.getElementById('form_fk_table').addEventListener('change', function() {
            loadModuleFields(
                this.value,
                document.getElementById('form_fk_column'),
                document.getElementById('form_fk_label'),
                '',
                ''
            );
        });

        // Event listener: AJAX ao trocar módulo Unique
        document.getElementById('form_unique_table').addEventListener('change', function() {
            loadModuleFields(
                this.value,
                document.getElementById('form_unique_column'),
                null,
                '',
                null
            );
        });

        // Salvar configuração do campo (Form)
        document.getElementById('btn_save_form_field').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const fieldId = document.getElementById('form_field_id').value;
            const fieldCode = document.getElementById('form_field_code').value;
            const moduleCode = '{{ encodeId($module->id) }}';
            const component = document.getElementById('form_component').value;
            const isUnique = document.getElementById('form_field_unique').value === '1';

            // Validar JSON do campo options se preenchido
            const optionsVal = document.getElementById('form_options').value.trim();
            if (optionsVal) {
                try {
                    JSON.parse(optionsVal);
                } catch (err) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'JSON inválido',
                        text: 'O campo Opções contém JSON inválido.'
                    });
                    return;
                }
            }

            // Montar payload
            const payload = {
                component: component,
                grid_col: document.getElementById('form_grid_col').value,
                icon: document.getElementById('form_icon').value || null,
                placeholder: document.getElementById('form_placeholder').value || null,
                mask: document.getElementById('form_mask').value || null,
                tooltip: document.getElementById('form_tooltip').value || null,
                tooltip_direction: document.getElementById('form_tooltip_direction').value,
                options: optionsVal || null,
                visible_create: document.getElementById('form_visible_create').checked,
                visible_edit: document.getElementById('form_visible_edit').checked,
                visible_show: document.getElementById('form_visible_show').checked,
            };

            // FK condicional
            if (component === 'select_module') {
                payload.fk_table = document.getElementById('form_fk_table').value || null;
                payload.fk_column = document.getElementById('form_fk_column').value || null;
                payload.fk_label = document.getElementById('form_fk_label').value || null;
            }

            // Unique condicional
            if (isUnique) {
                payload.unique_table = document.getElementById('form_unique_table').value || null;
                payload.unique_column = document.getElementById('form_unique_column').value || null;
            }

            fetch(`/modules/${moduleCode}/fields/${fieldCode}/form`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar dados locais
                        if (fieldsFormData[fieldId]) {
                            fieldsFormData[fieldId].component = payload.component;
                            fieldsFormData[fieldId].grid_col = payload.grid_col;
                            fieldsFormData[fieldId].icon = payload.icon || '';
                            fieldsFormData[fieldId].placeholder = payload.placeholder || '';
                            fieldsFormData[fieldId].mask = payload.mask || '';
                            fieldsFormData[fieldId].tooltip = payload.tooltip || '';
                            fieldsFormData[fieldId].tooltip_direction = payload.tooltip_direction;
                            fieldsFormData[fieldId].options = payload.options || '';
                            fieldsFormData[fieldId].visible_create = payload.visible_create;
                            fieldsFormData[fieldId].visible_edit = payload.visible_edit;
                            fieldsFormData[fieldId].visible_show = payload.visible_show;
                            if (payload.fk_table !== undefined) fieldsFormData[fieldId].fk_table = payload
                                .fk_table || '';
                            if (payload.fk_column !== undefined) fieldsFormData[fieldId].fk_column = payload
                                .fk_column || '';
                            if (payload.fk_label !== undefined) fieldsFormData[fieldId].fk_label = payload
                                .fk_label || '';
                            if (payload.unique_table !== undefined) fieldsFormData[fieldId].unique_table =
                                payload.unique_table || '';
                            if (payload.unique_column !== undefined) fieldsFormData[fieldId].unique_column =
                                payload.unique_column || '';
                        }

                        // Atualizar preview visual do campo
                        const fieldEl = document.querySelector(`#pane_form [data-field-id="${fieldId}"]`);
                        if (fieldEl) {
                            // Atualizar grid_col
                            fieldEl.className = fieldEl.className.replace(/col-md-\d+/g, '');
                            fieldEl.classList.add(payload.grid_col);

                            // Atualizar opacidade
                            const isVisible = payload.visible_create || payload.visible_edit;
                            fieldEl.classList.toggle('opacity-25', !isVisible);

                            // Atualizar data-component
                            fieldEl.dataset.component = payload.component;
                        }

                        bootstrap.Modal.getInstance(document.getElementById('modal_form_field')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Salvo!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao salvar configuração do campo'
                    });
                });
        });

        // =============================================
        // ABA SEEDS — HANDLERS
        // =============================================

        // Helper: coletar valores dos campos do modal seed
        function collectSeedData() {
            const data = {};
            document.querySelectorAll('#modal_seed .seed-field').forEach(el => {
                const name = el.dataset.fieldName;
                const type = el.dataset.fieldType;
                const comp = el.dataset.component;

                if (comp === 'switch') {
                    data[name] = el.checked;
                } else if (comp === 'checkbox') {
                    // Checkboxes múltiplos: coletar array de valores marcados
                    if (!data[name]) data[name] = [];
                    if (el.checked) data[name].push(el.value);
                } else if (comp === 'radio') {
                    // Radio: pegar o selecionado
                    if (el.checked) data[name] = el.value;
                } else {
                    let val = el.value;
                    // Converter tipos numéricos
                    if (val !== '' && ['INTEGER', 'BIGINT'].includes(type)) {
                        val = parseInt(val, 10);
                    } else if (val !== '' && type === 'DECIMAL') {
                        val = parseFloat(val);
                    } else if (val === '') {
                        val = null;
                    }
                    data[name] = val;
                }
            });
            return data;
        }

        // Helper: popular campos do modal seed com dados
        function populateSeedModal(seedData) {
            document.querySelectorAll('#modal_seed .seed-field').forEach(el => {
                const name = el.dataset.fieldName;
                const comp = el.dataset.component;
                const val = seedData[name];

                if (comp === 'switch') {
                    el.checked = val === true || val === 1 || val === '1' || val === 'true';
                } else if (comp === 'checkbox') {
                    const vals = Array.isArray(val) ? val.map(String) : [];
                    el.checked = vals.includes(el.value);
                } else if (comp === 'radio') {
                    el.checked = (String(val) === el.value);
                } else {
                    el.value = (val !== null && val !== undefined) ? val : '';
                }
            });
        }

        // Helper: limpar campos do modal seed
        function clearSeedModal() {
            document.querySelectorAll('#modal_seed .seed-field').forEach(el => {
                const comp = el.dataset.component;
                if (comp === 'switch') {
                    el.checked = true; // default ativo
                } else if (comp === 'checkbox' || comp === 'radio') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            });
        }

        // Mapa pré-compilado de configuração dos campos visíveis no grid de seeds
        @php
            $seedGridColumnsMap = [];
            foreach ($fields as $f) {
                if (!$f->ui || !$f->ui->visible_index) {
                    continue;
                }
                $opts = $f->ui->options;
                if (is_string($opts)) {
                    $opts = json_decode($opts, true);
                }
                if (!is_array($opts)) {
                    $opts = [];
                }
                $seedGridColumnsMap[] = [
                    'name' => $f->name,
                    'type' => $f->type,
                    'label' => $f->label,
                    'options' => $opts,
                    'has_options' => !empty($opts) || $f->type === 'BOOLEAN',
                ];
            }
        @endphp
        const seedGridColumns = {!! json_encode($seedGridColumnsMap) !!};

        // Helper: gerar HTML de uma linha de seed para a tabela
        function buildSeedRow(seed, index) {
            const seedData = typeof seed.data === 'string' ? JSON.parse(seed.data) : seed.data;
            const seedCode = seed.encoded_id || '';

            let cells = `<td class="text-center text-muted">${index}</td>`;

            seedGridColumns.forEach(col => {
                const val = seedData[col.name];

                if (col.has_options) {
                    let key = val;
                    if (typeof val === 'boolean') key = val ? 'true' : 'false';
                    else if (val !== null && val !== undefined) key = String(val);

                    const optLabel = col.options[key] || col.options[String(val)] || null;

                    if (optLabel) {
                        const parts = optLabel.split('|');
                        cells +=
                            `<td><span class="badge badge-light-${parts[1] || 'primary'}">${parts[0]}</span></td>`;
                    } else if (typeof val === 'boolean') {
                        cells +=
                            `<td><span class="badge badge-light-${val ? 'success' : 'danger'}">${val ? 'Ativo' : 'Inativo'}</span></td>`;
                    } else {
                        cells += `<td>${val !== null && val !== undefined ? val : '—'}</td>`;
                    }
                } else {
                    cells +=
                        `<td>${val !== null && val !== undefined ? val : '<span class="text-muted">—</span>'}</td>`;
                }
            });

            cells += `<td class="text-center">
        <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-seed-edit"
                data-seed-id="${seed.id}" data-seed-code="${seedCode}" title="Editar">
            <i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i>
        </button>
        <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-seed-delete"
                data-seed-id="${seed.id}" data-seed-code="${seedCode}" title="Excluir">
            <i class="ki-duotone ki-trash fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
        </button>
    </td>`;

            return `<tr data-seed-id="${seed.id}" data-seed-code="${seedCode}">${cells}</tr>`;
        }

        // Helper: atualizar contador de seeds
        function updateSeedsCount() {
            const rows = document.querySelectorAll('#seeds_tbody tr:not(#seeds_empty_row)').length;
            document.getElementById('seeds_count').textContent = `(${rows} registros)`;
        }

        // ADICIONAR SEED — Abrir modal limpo
        document.getElementById('btn_add_seed').addEventListener('click', function() {
            document.getElementById('seed_id').value = '';
            document.getElementById('seed_code').value = '';
            document.getElementById('seed_mode').value = 'create';
            document.getElementById('modal_seed_title').textContent = 'Adicionar Seed';
            clearSeedModal();
            new bootstrap.Modal(document.getElementById('modal_seed')).show();
        });

        // EDITAR SEED — Abrir modal com dados
        document.getElementById('seeds_tbody').addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-seed-edit');
            if (!editBtn) return;

            const seedId = editBtn.dataset.seedId;
            const seedCode = editBtn.dataset.seedCode;
            const moduleCode = '{{ encodeId($module->id) }}';

            // Buscar dados via AJAX
            fetch(`/modules/${moduleCode}/seeds`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const seed = data.data.find(s => s.id == seedId);
                        if (seed) {
                            const seedData = typeof seed.data === 'string' ? JSON.parse(seed.data) : seed.data;

                            document.getElementById('seed_id').value = seedId;
                            document.getElementById('seed_code').value = seedCode;
                            document.getElementById('seed_mode').value = 'edit';
                            document.getElementById('modal_seed_title').textContent = 'Editar Seed';

                            populateSeedModal(seedData);
                            new bootstrap.Modal(document.getElementById('modal_seed')).show();
                        }
                    }
                });
        });

        // SALVAR SEED — Create ou Update
        document.getElementById('btn_save_seed').addEventListener('click', function() {
            const mode = document.getElementById('seed_mode').value;
            const seedCode = document.getElementById('seed_code').value;
            const moduleCode = '{{ encodeId($module->id) }}';
            const seedData = collectSeedData();

            let url, method;
            if (mode === 'edit' && seedCode) {
                url = `/modules/${moduleCode}/seeds/${seedCode}`;
                method = 'PUT';
            } else {
                url = `/modules/${moduleCode}/seeds`;
                method = 'POST';
            }

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        data: seedData
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modal_seed')).hide();

                        if (mode === 'edit') {
                            // Atualizar linha existente
                            const existingRow = document.querySelector(
                                `#seeds_tbody tr[data-seed-id="${document.getElementById('seed_id').value}"]`
                                );
                            if (existingRow) {
                                const rowIndex = existingRow.querySelector('td').textContent;
                                const seed = data.data;
                                seed.encoded_id = seedCode;
                                existingRow.outerHTML = buildSeedRow(seed, rowIndex);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Atualizado!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            // Adicionar nova linha
                            const emptyRow = document.getElementById('seeds_empty_row');
                            if (emptyRow) emptyRow.remove();

                            const currentRows = document.querySelectorAll('#seeds_tbody tr').length;
                            const seed = data.data;
                            seed.encoded_id = btoa(String(seed.id)).replace(/\+/g, '-').replace(/\//g, '_')
                                .replace(/=+$/, '');
                            document.getElementById('seeds_tbody').insertAdjacentHTML('beforeend', buildSeedRow(
                                seed, currentRows + 1));

                            Swal.fire({
                                icon: 'success',
                                title: 'Criado!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }

                        updateSeedsCount();
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Falha ao salvar seed.'
                    });
                });
        });

        // EXCLUIR SEED — Confirmação + Delete
        document.getElementById('seeds_tbody').addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.btn-seed-delete');
            if (!deleteBtn) return;

            const seedCode = deleteBtn.dataset.seedCode;
            const seedId = deleteBtn.dataset.seedId;
            const moduleCode = '{{ encodeId($module->id) }}';

            Swal.fire({
                title: 'Confirmar exclusão?',
                text: 'O seed será removido permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/modules/${moduleCode}/seeds/${seedCode}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                const row = document.querySelector(
                                    `#seeds_tbody tr[data-seed-id="${seedId}"]`);
                                if (row) row.remove();

                                // Renumerar linhas
                                document.querySelectorAll('#seeds_tbody tr').forEach((tr, i) => {
                                    const firstTd = tr.querySelector('td');
                                    if (firstTd) firstTd.textContent = i + 1;
                                });

                                // Se não tem mais linhas, mostrar empty state
                                if (document.querySelectorAll('#seeds_tbody tr').length === 0) {
                                    const colSpan = document.querySelectorAll('#seeds_table thead th')
                                        .length;
                                    document.getElementById('seeds_tbody').innerHTML = `
                            <tr id="seeds_empty_row">
                                <td colspan="${colSpan}" class="text-center text-muted py-5">
                                    Nenhum seed cadastrado. Clique em "Adicionar" para criar o primeiro.
                                </td>
                            </tr>`;
                                }

                                updateSeedsCount();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Excluído!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Falha ao excluir seed.'
                            });
                        });
                }
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

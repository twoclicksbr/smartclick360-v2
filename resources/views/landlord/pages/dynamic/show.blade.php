@extends('landlord.layouts.app')

@section('title', $config->name . ' - Detalhes')

@section('toolbar')
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            {{ $config->name }}
        </h1>
    </div>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ url('/' . $module) }}" class="btn btn-sm btn-light">
            <i class="ki-outline ki-arrow-left fs-2"></i> Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="card mb-5 mb-xl-10">
        <div class="card-header">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Informações</h3>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-light-primary" id="btn_edit_show" data-code="{{ $code }}">
                    <i class="ki-outline ki-pencil fs-5"></i> Editar
                </button>
            </div>
        </div>
        <div class="card-body p-9">
            <div class="row">
                @foreach($showFields as $field)
                    <div class="col-md-6 mb-7">
                        <label class="fw-semibold text-muted fs-6">{{ $field->label }}</label>
                        <div class="fw-bold fs-6 text-gray-800 mt-1">
                            @php
                                $value = $record->{$field->name} ?? '';
                                $displayValue = $value;

                                // Formatar por tipo
                                if ($field->type === 'date' && $value) {
                                    $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y');
                                } elseif ($field->type === 'datetime' && $value) {
                                    $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
                                } elseif ($field->type === 'decimal' && $value !== '') {
                                    $displayValue = number_format((float)$value, $field->precision ?? 2, ',', '.');
                                }
                            @endphp

                            {{-- Badge (boolean ou options) --}}
                            @if($field->ui_options && isset($field->ui_options[(string)$value]))
                                @php $opt = $field->ui_options[(string)$value]; @endphp
                                <span class="badge badge-light-{{ $opt['badge'] ?? 'primary' }}">{{ $opt['label'] ?? $value }}</span>
                            @elseif($value === '' || $value === null)
                                <span class="text-muted">—</span>
                            @else
                                {{ $displayValue }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

{{-- Incluir o modal para edição --}}
@include('landlord.pages.dynamic._modal', [
    'module' => $module,
    'config' => $config,
    'fields' => $fieldsWithUi
])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSlug = '{{ $module }}';
    const modal = document.getElementById('modal_dynamic_create');
    const form = document.getElementById('form_dynamic');
    const formMethod = document.getElementById('form_method');
    const recordCode = document.getElementById('record_code');
    const modalTitle = document.getElementById('modal_dynamic_title');

    // Botão editar abre o modal
    document.getElementById('btn_edit_show')?.addEventListener('click', function(e) {
        e.preventDefault();
        const code = this.dataset.code;

        if (!modal) {
            console.error('Modal não encontrado');
            return;
        }

        // Configurar modal para edição ANTES de abrir
        formMethod.value = 'PUT';
        recordCode.value = code;
        modalTitle.textContent = 'Editar {{ $config->name }}';

        // Carregar dados do registro via AJAX
        fetch(`/${moduleSlug}/${code}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.data) {
                const record = data.data || data;

                // Preencher campos do formulário
                for (const [key, value] of Object.entries(record)) {
                    const field = form.querySelector(`[name="${key}"]`);
                    if (!field) continue;

                    if (field.type === 'checkbox' && field.closest('.form-switch')) {
                        field.checked = value == 1 || value === true;
                    } else if (field.tagName === 'SELECT') {
                        field.value = value;
                        if (typeof $(field).select2 === 'function') {
                            $(field).val(value).trigger('change');
                        }
                    } else {
                        field.value = value ?? '';
                    }
                }

                // Abrir modal
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                modalInstance.show();
            }
        })
        .catch(error => {
            console.error('Erro ao carregar dados:', error);
            alert('Erro ao carregar dados do registro');
        });
    });
});
</script>
@endpush

@extends('tenant.layouts.app')

@section('title', $config->name . ' - Detalhes')

@section('toolbar')
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            {{ $config->name }}
        </h1>
        @if($config->description_show)
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">{{ $config->description_show }}</li>
            </ul>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="{{ route('tenant.module.index', ['slug' => session('tenant_slug'), 'module' => $module]) }}" class="btn btn-sm btn-light">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSlug = '{{ $module }}';

    // Botão editar redireciona para index com modal aberto (futuro: inline edit)
    document.getElementById('btn_edit_show')?.addEventListener('click', function() {
        const code = this.dataset.code;
        window.location.href = `/${moduleSlug}?edit=${code}`;
    });
});
</script>
@endpush

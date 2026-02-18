@extends('tenant.layouts.app')

@section('title', $config->name)

@section('toolbar')
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            {{ $config->name }}
        </h1>
        @if($config->description_index)
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">{{ $config->description_index }}</li>
            </ul>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <x-tenant-quick-search :module="$module" />
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal_dynamic_create">
            <i class="ki-outline ki-plus fs-2"></i> Novo
        </button>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_dynamic">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            @if($config->show_drag)
                                <th class="w-1"></th>
                            @endif
                            @if($config->show_checkbox)
                                <th class="w-1">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" id="check_all" />
                                    </div>
                                </th>
                            @endif
                            @foreach($indexFields as $field)
                                <th class="{{ $field->width_index ?? '' }}">
                                    @if($field->sortable)
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => $field->name, 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === $field->name ? 'desc' : 'asc']) }}" class="text-muted">
                                            {{ $field->label }}
                                            @if(request('sort_by') === $field->name)
                                                <i class="ki-outline ki-arrow-{{ request('sort_direction') === 'desc' ? 'down' : 'up' }} fs-7"></i>
                                            @endif
                                        </a>
                                    @else
                                        {{ $field->label }}
                                    @endif
                                </th>
                            @endforeach
                            @if($config->show_actions)
                                <th class="w-10 text-end">Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold" id="kt_table_body">
                        @forelse($records as $record)
                            <tr data-id="{{ $record->id }}">
                                @if($config->show_drag)
                                    <td>
                                        <i class="ki-outline ki-abstract-14 fs-4 text-gray-400 cursor-move sortable-handle"></i>
                                    </td>
                                @endif
                                @if($config->show_checkbox)
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input row-check" type="checkbox" value="{{ $record->id }}" />
                                        </div>
                                    </td>
                                @endif
                                @foreach($indexFields as $field)
                                    <td>
                                        @php
                                            $value = $record->{$field->name} ?? '';
                                            $displayValue = $value;

                                            // grid_template: combina campos
                                            if ($field->grid_template) {
                                                $displayValue = $field->grid_template;
                                                preg_match_all('/\{(\w+)\}/', $field->grid_template, $matches);
                                                foreach ($matches[1] as $placeholder) {
                                                    $displayValue = str_replace('{' . $placeholder . '}', $record->{$placeholder} ?? '', $displayValue);
                                                }
                                            }

                                            // Formatar por tipo
                                            if (!$field->grid_template) {
                                                if ($field->type === 'date' && $value) {
                                                    $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y');
                                                } elseif ($field->type === 'datetime' && $value) {
                                                    $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
                                                } elseif ($field->type === 'decimal' && $value !== '') {
                                                    $displayValue = number_format((float)$value, $field->precision ?? 2, ',', '.');
                                                }
                                            }
                                        @endphp

                                        {{-- Badge (boolean ou options) --}}
                                        @if($field->ui_options && isset($field->ui_options[(string)$value]))
                                            @php $opt = $field->ui_options[(string)$value]; @endphp
                                            <span class="badge badge-light-{{ $opt['badge'] ?? 'primary' }}">{{ $opt['label'] ?? $value }}</span>

                                        {{-- Link --}}
                                        @elseif($field->grid_link)
                                            @php
                                                $link = $field->grid_link;
                                                $code = encodeId($record->id);
                                                $link = str_replace('{show}', route('tenant.module.show', ['slug' => session('tenant_slug'), 'module' => $module, 'code' => $code]), $link);
                                                $link = str_replace('{edit}', route('tenant.module.show', ['slug' => session('tenant_slug'), 'module' => $module, 'code' => $code]) . '/edit', $link);
                                                $link = str_replace('{value}', $value, $link);
                                                $isExternal = str_starts_with($link, 'http');
                                            @endphp
                                            <a href="{{ $link }}" @if($isExternal) target="_blank" @endif class="text-gray-800 text-hover-primary">
                                                {{ $displayValue }}
                                            </a>

                                        {{-- Texto normal --}}
                                        @else
                                            {{ $displayValue }}
                                        @endif
                                    </td>
                                @endforeach
                                @if($config->show_actions)
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-edit"
                                                data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Editar">
                                                <i class="ki-outline ki-pencil fs-5"></i>
                                            </button>
                                            @if($record->trashed ?? false)
                                                <button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore"
                                                    data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Restaurar">
                                                    <i class="ki-outline ki-arrow-circle-left fs-5"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete"
                                                    data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-5"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="99" class="text-center text-muted py-10">
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($records->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <div class="text-muted fs-7">
                        Mostrando {{ $records->firstItem() }} a {{ $records->lastItem() }} de {{ $records->total() }} registros
                    </div>
                    {{ $records->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @include('tenant.pages.dynamic._modal', ['fieldsWithUi' => $fieldsWithUi, 'module' => $module, 'config' => $config])
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSlug = '{{ $module }}';
    const tenantSlug = '{{ session("tenant_slug") }}';

    // Check All
    const checkAll = document.getElementById('check_all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        });
    }

    // Delete
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            if (confirm('Tem certeza que deseja excluir este registro?')) {
                fetch(`/${moduleSlug}/${code}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                });
            }
        });
    });

    // Restore
    document.querySelectorAll('.btn-restore').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            fetch(`/${moduleSlug}/${code}/restore`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
        });
    });

    // Sortable (drag and drop)
    @if($config->show_drag)
    if (typeof Sortable !== 'undefined') {
        new Sortable(document.getElementById('kt_table_body'), {
            handle: '.sortable-handle',
            animation: 150,
            onEnd: function() {
                const items = [];
                document.querySelectorAll('#kt_table_body tr[data-id]').forEach((row, index) => {
                    items.push({ id: parseInt(row.dataset.id), order: index + 1 });
                });
                fetch(`/${moduleSlug}/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ items })
                });
            }
        });
    }
    @endif
});
</script>
@endpush

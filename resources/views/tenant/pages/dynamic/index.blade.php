@extends('tenant.layouts.app')

@php
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => url('/dashboard/main')],
        ['label' => $config->name, 'url' => null],
    ];
    $pageTitle = $config->name;
    $pageDescription = $config->description_index ?? 'Gerencie os registros';
@endphp

@section('title', $config->name)

@section('content')
    <!--begin::Card-->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" class="form-control form-control-solid w-250px ps-12"
                        id="quick_search_input" placeholder="Buscar {{ strtolower($config->name) }}..." />
                </div>
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-table-toolbar="base">
                    <!--begin::Bulk actions (hidden by default)-->
                    <div class="d-none" id="bulk-actions-container">
                        <div class="fw-bold me-5">
                            <span id="bulk-actions-text"></span>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-bulk-delete">
                            <i class="ki-outline ki-trash fs-4"></i> Excluir Selecionados
                        </button>
                    </div>
                    <!--end::Bulk actions-->

                    <button type="button" class="btn btn-sm btn-light-primary me-2" id="btn-search">
                        <i class="bi bi-search fs-4 me-1"></i> Pesquisa Avançada
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-new" data-bs-toggle="modal" data-bs-target="#modal_dynamic_create">
                        <i class="ki-outline ki-plus fs-2"></i> Novo
                    </button>
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_dynamic">
                    <thead class="fw-bold text-muted bg-light">
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            @if($config->show_drag)
                                <th class="w-1 ps-4 rounded-start">
                                    <i class="ki-duotone ki-abstract-16 fs-5"><span class="path1"></span><span class="path2"></span></i>
                                </th>
                            @endif
                            @if($config->show_checkbox)
                                <th class="w-1 {{ !$config->show_drag ? 'rounded-start' : '' }}">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" id="check_all" />
                                    </div>
                                </th>
                            @endif
                            @foreach($indexFields as $field)
                                <th class="{{ $field->width_index ?? '' }}">
                                    @if($field->sortable)
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => $field->name, 'sort_direction' => request('sort_direction') === 'asc' && request('sort_by') === $field->name ? 'desc' : 'asc']) }}" class="text-muted text-hover-primary">
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
                                <th class="w-10 text-end pe-4 rounded-end">Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold" id="kt_table_body">
                        @include('tenant.pages.dynamic._tbody', ['config' => $config, 'indexFields' => $indexFields, 'records' => $records, 'module' => $module])
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($records->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-5" id="pagination_container">
                    <div class="fs-6 fw-semibold text-gray-700">
                        Mostrando {{ $records->firstItem() }} a {{ $records->lastItem() }} de {{ $records->total() }} registros
                    </div>
                    {{ $records->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Modal Create/Edit --}}
    @include('tenant.pages.dynamic._modal', ['fieldsWithUi' => $fieldsWithUi, 'module' => $module, 'config' => $config])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSlug = '{{ $module }}';
    const tableId = 'kt_table_dynamic';
    var searchTimeout;
    var currentSortable = null;

    // ==============================================
    // Flash Messages
    // ==============================================
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    // ==============================================
    // Quick Search AJAX (recarrega só o tbody)
    // ==============================================
    var quickSearchInput = document.getElementById('quick_search_input');

    function performSearch() {
        var searchValue = quickSearchInput.value;
        var url = new URL(window.location.href);

        if (searchValue.trim() !== '') {
            url.searchParams.set('quick_search', searchValue);
        } else {
            url.searchParams.delete('quick_search');
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('kt_table_body').innerHTML = html;

            // Atualiza URL sem reload
            window.history.pushState({}, '', url.toString());

            // Restaura foco no input
            quickSearchInput.focus();
            var length = quickSearchInput.value.length;
            quickSearchInput.setSelectionRange(length, length);

            // Reinicializa componentes
            initSortable();
            initBulkActions();
            initActions();
            initTooltips();
        })
        .catch(error => console.error('Erro na busca:', error));
    }

    if (quickSearchInput) {
        quickSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
    }

    // ==============================================
    // Sortable - Drag and Drop
    // ==============================================
    function initSortable() {
        if (currentSortable) {
            currentSortable.destroy();
            currentSortable = null;
        }

        @if($config->show_drag)
        var tableBody = document.getElementById('kt_table_body');
        if (tableBody && typeof Sortable !== 'undefined') {
            currentSortable = new Sortable(tableBody, {
                handle: '[data-kt-sortable-handle="true"]',
                animation: 150,
                ghostClass: 'bg-light-primary',
                dragClass: 'bg-light-warning',
                chosenClass: 'bg-light-info',
                onEnd: function() {
                    var items = [];
                    tableBody.querySelectorAll('tr[data-id]').forEach(function(row, index) {
                        items.push({ id: parseInt(row.dataset.id), order: index + 1 });
                    });
                    fetch('/' + moduleSlug + '/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ items: items })
                    });
                }
            });
        }
        @endif
    }

    // ==============================================
    // Bulk Actions
    // ==============================================
    function updateBulkActions() {
        var checkboxes = document.querySelectorAll('#' + tableId + ' tbody .row-check');
        var checkedCount = 0;
        checkboxes.forEach(function(cb) { if (cb.checked) checkedCount++; });

        var container = document.getElementById('bulk-actions-container');
        var textElement = document.getElementById('bulk-actions-text');
        var btnSearch = document.getElementById('btn-search');
        var btnNew = document.getElementById('btn-new');

        if (checkedCount > 0) {
            container.classList.remove('d-none');
            if (btnSearch) btnSearch.classList.add('d-none');
            if (btnNew) btnNew.classList.add('d-none');
            textElement.textContent = checkedCount + (checkedCount === 1 ? ' selecionado' : ' selecionados');
        } else {
            container.classList.add('d-none');
            if (btnSearch) btnSearch.classList.remove('d-none');
            if (btnNew) btnNew.classList.remove('d-none');
        }
    }

    function initBulkActions() {
        document.querySelectorAll('#' + tableId + ' tbody .row-check').forEach(function(cb) {
            cb.addEventListener('change', updateBulkActions);
        });

        var checkAll = document.getElementById('check_all');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                document.querySelectorAll('#' + tableId + ' tbody .row-check').forEach(function(cb) {
                    cb.checked = checkAll.checked;
                });
                updateBulkActions();
            });
        }
    }

    // ==============================================
    // Delete / Restore Actions
    // ==============================================
    function initActions() {
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var code = this.dataset.code;
                if (confirm('Tem certeza que deseja excluir este registro?')) {
                    fetch('/' + moduleSlug + '/' + code, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(r => r.json())
                    .then(data => { if (data.success) location.reload(); });
                }
            });
        });

        document.querySelectorAll('.btn-restore').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var code = this.dataset.code;
                fetch('/' + moduleSlug + '/' + code + '/restore', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => { if (data.success) location.reload(); });
            });
        });
    }

    // ==============================================
    // Tooltips
    // ==============================================
    function initTooltips() {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    }

    // ==============================================
    // Inicialização
    // ==============================================
    initSortable();
    initBulkActions();
    initActions();
    initTooltips();
});
</script>
@endpush

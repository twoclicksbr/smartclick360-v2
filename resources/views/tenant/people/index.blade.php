@extends('tenant.layouts.app')

@php
    $breadcrumbs = [['label' => $tenant->name, 'url' => url('/dashboard/main')], ['label' => 'Pessoas', 'url' => null]];
    $pageTitle = 'Pessoas';
    $pageDescription = 'Gerencie o cadastro de pessoas';
@endphp

@section('title', 'Pessoas - ' . $tenant->name)

@php
    // Função temporária para formatar telefone (até rodar composer dump-autoload)
    if (!function_exists('format_phone_temp')) {
        function format_phone_temp($phone)
        {
            if (empty($phone)) {
                return '';
            }
            $phone = preg_replace('/[^0-9]/', '', $phone);
            $length = strlen($phone);
            if ($length == 11) {
                return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
            } elseif ($length == 10) {
                return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
            }
            return $phone;
        }
    }
@endphp

@section('content')
    <!--begin::Card-->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <x-tenant.quick-search placeholder="Buscar pessoa" />
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <x-tenant.bulk-actions />
                    <x-tenant.action-button label="Pesquisa Avançada" icon="bi bi-search" modal="modal_search" id="btn-search" class="me-2" />
                    <x-tenant.action-button label="Novo" icon="ki-duotone ki-plus" id="btn-new" />
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4" id="people_table_container">
            @include('tenant.people._table', ['people' => $people])
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Modal de Pesquisa Avançada --}}
    @include('tenant.search.modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // Live Search via AJAX - Busca sem reload
            // ==============================================
            var quickSearchInput = document.getElementById('quick_search_input');
            var quickSearchForm = document.getElementById('quick_search_form');
            var peopleTableContainer = document.getElementById('people_table_container');
            var searchTimeout;
            var currentSortable = null; // Guardar referência do Sortable

            function performSearch() {
                var searchValue = quickSearchInput.value;
                console.log('🔍 Busca AJAX disparada:', searchValue);

                // Monta a URL com o parâmetro de busca
                var url = new URL(window.location.href);
                if (searchValue.trim() !== '') {
                    url.searchParams.set('quick_search', searchValue);
                } else {
                    url.searchParams.delete('quick_search');
                }

                // Faz requisição AJAX
                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Atualiza apenas o container da tabela
                    peopleTableContainer.innerHTML = html;

                    // Atualiza a URL sem reload
                    window.history.pushState({}, '', url.toString());

                    // Restaura o foco e cursor
                    quickSearchInput.focus();
                    var length = quickSearchInput.value.length;
                    quickSearchInput.setSelectionRange(length, length);

                    // Reinicializa o Sortable na nova tabela
                    initSortable();

                    // Reinicializa os event listeners de bulk actions
                    initBulkActions();

                    console.log('✓ Tabela atualizada via AJAX');
                })
                .catch(error => {
                    console.error('✗ Erro na busca AJAX:', error);
                });
            }

            if (quickSearchInput && quickSearchForm) {
                // Previne submit normal do formulário
                quickSearchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    performSearch();
                });

                // Busca em tempo real com debounce
                quickSearchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(performSearch, 500);
                });

                console.log('✓ Live search AJAX ativado (debounce: 500ms)');
            }

            // ==============================================
            // Sortable - Drag and Drop (função reutilizável)
            // ==============================================
            function initSortable() {
                // Destroi instância anterior se existir
                if (currentSortable) {
                    currentSortable.destroy();
                    currentSortable = null;
                }

                var table = document.querySelector('#kt_table_people tbody');

                if (table && typeof Sortable !== 'undefined') {
                    try {
                        currentSortable = new Sortable(table, {
                            handle: '[data-kt-sortable-handle="true"]',
                            animation: 150,
                            ghostClass: 'bg-light-primary',
                            dragClass: 'bg-light-warning',
                            chosenClass: 'bg-light-info',
                            onEnd: function(evt) {
                                // Captura a nova ordem dos IDs
                                var order = [];
                                var rows = table.querySelectorAll('tr[data-id]');

                                rows.forEach(function(row, index) {
                                    var id = row.getAttribute('data-id');
                                    if (id) {
                                        order.push({
                                            id: id,
                                            order: index + 1
                                        });
                                    }
                                });

                                // Envia a nova ordem para o backend
                                fetch('/people/reorder', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ order: order })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        console.log('✓ Ordem atualizada');
                                    }
                                })
                                .catch(error => {
                                    console.error('✗ Erro ao atualizar ordem:', error);
                                });
                            }
                        });
                        console.log('✓ Sortable inicializado');
                    } catch (error) {
                        console.error('✗ Erro ao inicializar Sortable:', error);
                    }
                }
            }

            // ==============================================
            // Bulk Actions (função reutilizável)
            // ==============================================
            function updateBulkActions() {
                var checkboxes = document.querySelectorAll('#kt_table_people tbody .form-check-input');
                var checkedCount = 0;

                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        checkedCount++;
                    }
                });

                var container = document.getElementById('bulk-actions-container');
                var textElement = document.getElementById('bulk-actions-text');
                var btnSearch = document.getElementById('btn-search');
                var btnNew = document.getElementById('btn-new');

                if (checkedCount > 0) {
                    container.classList.remove('d-none');
                    btnSearch.classList.add('d-none');
                    btnNew.classList.add('d-none');

                    if (checkedCount === 1) {
                        textElement.textContent = '1 ação em massa';
                    } else {
                        textElement.textContent = checkedCount + ' ações em massa';
                    }
                } else {
                    container.classList.add('d-none');
                    btnSearch.classList.remove('d-none');
                    btnNew.classList.remove('d-none');
                }
            }

            function initBulkActions() {
                // Monitora APENAS os checkboxes do tbody
                var bodyCheckboxes = document.querySelectorAll('#kt_table_people tbody .form-check-input');
                bodyCheckboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', updateBulkActions);
                });

                // Monitora o checkbox "selecionar todos" do thead
                var headerCheckbox = document.querySelector('#kt_table_people thead .form-check-input');
                if (headerCheckbox) {
                    headerCheckbox.addEventListener('change', function() {
                        setTimeout(updateBulkActions, 50);
                    });
                }

                updateBulkActions();
            }

            // Inicializa na primeira carga
            initSortable();
            initBulkActions();
        });
    </script>
@endpush

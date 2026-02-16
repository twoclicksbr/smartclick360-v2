@extends('tenant.layouts.app')

@php
    $breadcrumbs = [['label' => $tenant->name, 'url' => url('/dashboard/main')], ['label' => 'Pessoas', 'url' => null]];
    $pageTitle = 'Pessoas';
    $pageDescription = 'Gerencie o cadastro de pessoas';
@endphp

@section('title', 'Pessoas - ' . $tenant->name)

@php
    // Função temporária para formatar telefone (até rodar composer dump-autoload)
    if (!function_exists('format_phone')) {
        function format_phone($phone)
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
            <!--begin::Loading skeleton-->
            <div class="text-center py-20">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-gray-600 mt-3">Carregando pessoas...</p>
            </div>
            <!--end::Loading skeleton-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Modal de Pesquisa Avançada --}}
    <x-tenant.search-modal />

    {{-- Modal - Adicionar/Editar Pessoa --}}
    @include('tenant.layouts.modals.modal-module', ['module' => 'people', 'modalSize' => 'mw-800px'])
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Função helper para encode de ID (igual ao PHP)
        function encodeId(id) {
            // Converte para string antes de encodar (btoa só aceita strings)
            return btoa(String(id)).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // Flash Messages - Notificações de sucesso/erro
            // ==============================================
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
            // ==============================================
            // Live Search via AJAX - Busca sem reload
            // ==============================================
            var quickSearchInput = document.getElementById('quick_search_input');
            var quickSearchForm = document.getElementById('quick_search_form');
            var peopleTableContainer = document.getElementById('people_table_container');
            var searchTimeout;
            var currentSortable = null; // Guardar referência do Sortable

            // Função para gerar HTML de uma linha da tabela
            function buildPersonRow(person) {
                const avatar = person.files?.find(f => f.name === 'avatar');
                const whatsapp = person.contacts?.[0];
                const avatarHtml = avatar
                    ? `<img src="/storage/${avatar.path}" alt="${person.first_name} ${person.surname}" />`
                    : `<div class="symbol-label fs-6 fw-semibold text-success bg-light-success">
                        ${person.first_name.charAt(0).toUpperCase()}${person.surname.charAt(0).toUpperCase()}
                       </div>`;

                const whatsappHtml = whatsapp
                    ? `<a href="https://wa.me/55${whatsapp.value}" target="_blank" class="text-gray-800 text-hover-success fw-bold">
                        <i class="ki-solid ki-whatsapp fs-4 me-1 text-success"></i>
                        ${formatPhone(whatsapp.value)}
                       </a>`
                    : `<span class="text-gray-400 text-muted">-</span>`;

                const encodedId = encodeId(person.id);
                const statusBadge = person.status == 1
                    ? '<span class="badge badge-light-success">Ativo</span>'
                    : '<span class="badge badge-light-danger">Inativo</span>';

                return `
                    <tr data-id="${person.id}">
                        <td class="ps-4">
                            <div class="d-flex justify-content-center align-items-center" data-kt-sortable-handle="true" style="cursor: move;" data-bs-toggle="tooltip" data-bs-placement="right" title="Arrastar para reordenar">
                                <i class="ki-duotone ki-abstract-16 fs-5 text-gray-400">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${person.id}" />
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-start flex-column">${person.id}</div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px symbol-circle me-3">${avatarHtml}</div>
                                <div class="d-flex justify-content-start flex-column">
                                    <a href="/people/${encodedId}" class="text-gray-800 text-hover-primary fw-bold mb-1">
                                        ${person.first_name} ${person.surname}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>${whatsappHtml}</td>
                        <td>${statusBadge}</td>
                        <td class="text-end">
                            <a href="/people/${encodedId}" class="btn btn-icon btn-light-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                <i class="ki-outline ki-pencil fs-4"></i>
                            </a>
                            <a href="#" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="Deletar">
                                <i class="ki-outline ki-trash fs-4"></i>
                            </a>
                        </td>
                    </tr>
                `;
            }

            // Função para formatar telefone
            function formatPhone(phone) {
                if (!phone) return '';
                phone = phone.replace(/\D/g, '');
                if (phone.length === 11) {
                    return `(${phone.substr(0,2)}) ${phone.substr(2,5)}-${phone.substr(7,4)}`;
                } else if (phone.length === 10) {
                    return `(${phone.substr(0,2)}) ${phone.substr(2,4)}-${phone.substr(6,4)}`;
                }
                return phone;
            }

            function performSearch() {
                var searchValue = quickSearchInput.value;
                console.log('🔍 Busca API disparada:', searchValue);

                // Monta a URL da API com parâmetros
                var apiUrl = new URL('/api/v1/people', window.location.origin);
                var params = new URLSearchParams(window.location.search);

                if (searchValue.trim() !== '') {
                    params.set('quick_search', searchValue);
                } else {
                    params.delete('quick_search');
                }

                apiUrl.search = params.toString();

                // Faz requisição AJAX para API
                fetch(apiUrl.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const people = data.data.people.data;
                        const pagination = data.data.people;

                        // Constrói HTML da tabela
                        let tableHTML = `
                            <div class="table-responsive">
                                <table id="kt_table_people" class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead class="fw-bold text-muted bg-light">
                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                            <th class="w-1 ps-4 rounded-start">
                                                <i class="ki-duotone ki-abstract-16 fs-5"><span class="path1"></span><span class="path2"></span></i>
                                            </th>
                                            <th class="w-1">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_people tbody .form-check-input" value="1" />
                                                </div>
                                            </th>
                                            <th class="w-5">#</th>
                                            <th class="">Nome</th>
                                            <th class="w-20">WhatsApp</th>
                                            <th class="w-10">Status</th>
                                            <th class="text-end w-10 pe-4 rounded-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-semibold" data-kt-sortable="true">
                        `;

                        if (people.length > 0) {
                            people.forEach(person => {
                                tableHTML += buildPersonRow(person);
                            });
                        } else {
                            tableHTML += `
                                <tr>
                                    <td colspan="7" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                                            <h3 class="text-gray-800 fw-bold mb-2">Nenhuma pessoa encontrada</h3>
                                            <p class="text-gray-500 fs-6 mb-0">Tente ajustar os filtros de busca</p>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }

                        tableHTML += `
                                    </tbody>
                                </table>
                            </div>
                            <div class="pt-5">
                                <div class="d-flex flex-stack flex-wrap">
                                    <div class="fs-6 fw-semibold text-gray-700">
                                        Mostrando ${pagination.from || 0} a ${pagination.to || 0} de ${pagination.total} resultados
                                    </div>
                                </div>
                            </div>
                        `;

                        // Atualiza o container
                        peopleTableContainer.innerHTML = tableHTML;

                        // Atualiza URL sem reload
                        var newUrl = new URL(window.location.href);
                        if (searchValue.trim() !== '') {
                            newUrl.searchParams.set('quick_search', searchValue);
                        } else {
                            newUrl.searchParams.delete('quick_search');
                        }
                        window.history.pushState({}, '', newUrl.toString());

                        // Restaura foco
                        quickSearchInput.focus();
                        var length = quickSearchInput.value.length;
                        quickSearchInput.setSelectionRange(length, length);

                        // Reinicializa
                        initSortable();
                        initBulkActions();

                        // Inicializa tooltips
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });

                        console.log('✓ Tabela atualizada via API');
                    }
                })
                .catch(error => {
                    console.error('✗ Erro na busca API:', error);
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

            // Carrega dados iniciais via API
            performSearch();

            // ==============================================
            // Modal - Adicionar/Editar Pessoa
            // ==============================================
            var personModal = document.getElementById('kt_modal_add_person');
            var personForm = document.getElementById('kt_modal_add_person_form');
            var modalTitle = document.getElementById('modal_person_title');

            // Função para resetar o formulário
            function resetPersonForm() {
                personForm.reset();
                personForm.action = "{{ url('/people') }}";
                document.getElementById('person_form_method').value = 'POST';
                document.getElementById('person_id').value = '';
                document.getElementById('person_first_name').value = '';
                document.getElementById('person_surname').value = '';
                document.getElementById('person_status_switch').checked = true;
                document.getElementById('person_status_hidden').value = '1';
                modalTitle.textContent = 'Adicionar Pessoa';
            }

            // Botão "Novo" - Modo Adicionar
            var btnNew = document.getElementById('btn-new');
            if (btnNew) {
                btnNew.addEventListener('click', function() {
                    resetPersonForm();
                    var modal = new bootstrap.Modal(personModal);
                    modal.show();
                });
            }

            // Função global para editar pessoa (chamada de outras páginas)
            window.editPerson = function(id, firstName, surname, birthDate, avatarUrl) {
                resetPersonForm();

                // Configura modo edição
                modalTitle.textContent = 'Editar Pessoa';
                document.getElementById('person_form_method').value = 'PUT';
                document.getElementById('person_id').value = id;
                var encodedId = encodeId(id);
                personForm.action = "{{ url('/people') }}/" + encodedId;
                console.log('ID:', id, '→ Encoded:', encodedId, '→ Action:', personForm.action);

                // Preenche os campos
                document.getElementById('person_first_name').value = firstName;
                document.getElementById('person_surname').value = surname;
                document.getElementById('person_birth_date').value = birthDate || '';

                // Atualiza o preview do avatar se houver
                if (avatarUrl) {
                    var imageInputWrapper = personForm.querySelector('.image-input-wrapper');
                    if (imageInputWrapper) {
                        imageInputWrapper.style.backgroundImage = `url('${avatarUrl}')`;
                    }
                } else {
                    // Se não houver avatar, usa a imagem padrão
                    var imageInputWrapper = personForm.querySelector('.image-input-wrapper');
                    if (imageInputWrapper) {
                        imageInputWrapper.style.backgroundImage = "url('/assets/media/avatars/blank.png')";
                    }
                }

                // Marca como ativo (sempre ativo ao editar uma pessoa existente)
                document.getElementById('person_status_switch').checked = true;
                document.getElementById('person_status_hidden').value = '1';

                // Abre o modal
                var modal = new bootstrap.Modal(personModal);
                modal.show();
            };
        });
    </script>
@endpush

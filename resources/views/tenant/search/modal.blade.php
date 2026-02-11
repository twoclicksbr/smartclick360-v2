{{--
    Modal de Pesquisa Avançada - Componente Genérico

    USO BÁSICO (usa "Nome" como padrão):
    @include('tenant.search.modal')

    USO COM CUSTOMIZAÇÃO:
    @include('tenant.search.modal', [
        'searchFieldPlaceholder' => 'Nome do Produto'  // Produtos
        'searchFieldPlaceholder' => 'Razão Social'     // Fornecedores
        'searchFieldPlaceholder' => 'Número da Venda'  // Vendas
    ])

    CAMPOS PADRÃO (funcionam em todos os módulos):
    - ID
    - Operador (Contém, Início exato, Exato)
    - Campo Principal (parametrizável via $searchFieldPlaceholder)
    - Datas (Criado em, Atualizado em, Deletado em)
    - Período (daterangepicker com ranges em português)
    - Por Página (10, 25, 50, 100, 250)
    - Status (Ativo/Inativo)
    - Exibir deletados (checkbox)
--}}

<!--begin::Modal - Pesquisa Avançada-->
<div class="modal fade" id="modal_search" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header pb-1" id="modal_search_header">
                <h2 class="fw-bold">Pesquisa Avançada</h2>
                <div class="btn btn-sm btn-icon btn-light btn-active-light-danger" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Fechar" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-abstract-11">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-3 my-5 mt-0">
                <!--begin::Form-->
                <form id="modal_search_form" class="form" method="GET" action="{{ url()->current() }}">
                    <!--begin::Row 1-->
                    <div class="row g-3 mb-7">
                        <!--begin::Col - ID-->
                        <div class="col-1">
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-solid" placeholder="ID"
                                    name="search_id" id="search_id" value="{{ request('search_id') }}" />
                                <span
                                    class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 d-none"
                                    data-clear-input="search_id">
                                    <i class="ki-outline ki-cross fs-2 text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Operador-->
                        <div class="col-2">
                            <select class="form-select form-select-solid" name="search_operator"
                                data-placeholder="Operador">
                                <option value="contains"
                                    {{ request('search_operator', 'contains') == 'contains' ? 'selected' : '' }}>Contém
                                </option>
                                <option value="starts_with"
                                    {{ request('search_operator') == 'starts_with' ? 'selected' : '' }}>Início exato
                                </option>
                                <option value="exact" {{ request('search_operator') == 'exact' ? 'selected' : '' }}>
                                    Exato</option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Campo Principal-->
                        <div class="col-4">
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="{{ $searchFieldPlaceholder ?? 'Nome' }}" name="search_name"
                                    id="search_name" value="{{ request('search_name') }}" />
                                <span
                                    class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 d-none"
                                    data-clear-input="search_name">
                                    <i class="ki-outline ki-cross fs-2 text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Campo de Data-->
                        <div class="col-2">
                            <select class="form-select form-select-solid" name="search_date_field"
                                data-placeholder="Campo">
                                <option value="created_at"
                                    {{ request('search_date_field', 'created_at') == 'created_at' ? 'selected' : '' }}>
                                    Criado em</option>
                                <option value="updated_at"
                                    {{ request('search_date_field') == 'updated_at' ? 'selected' : '' }}>Atualizado em
                                </option>
                                <option value="deleted_at"
                                    {{ request('search_date_field') == 'deleted_at' ? 'selected' : '' }}>Deletado em
                                </option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Date Range-->
                        <div class="col-3">
                            <div class="position-relative">
                                <input type="text" class="form-control form-control-solid" placeholder="Período"
                                    name="search_date_range" id="kt_daterangepicker"
                                    value="{{ request('search_date_range') }}" readonly />
                                <span
                                    class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2 d-none"
                                    data-clear-input="kt_daterangepicker">
                                    <i class="ki-outline ki-cross fs-2 text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row 1-->

                    <!--begin::Row 2-->
                    <div class="row g-3 align-items-center">
                        <!--begin::Col - Per Page-->
                        <div class="col-2">
                            <select class="form-select form-select-solid" name="search_per_page"
                                data-placeholder="Por Página">
                                <option value="10" {{ request('search_per_page') == '10' ? 'selected' : '' }}>10
                                    registros</option>
                                <option value="25"
                                    {{ request('search_per_page', '25') == '25' ? 'selected' : '' }}>25 registros
                                </option>
                                <option value="50" {{ request('search_per_page') == '50' ? 'selected' : '' }}>50
                                    registros</option>
                                <option value="100" {{ request('search_per_page') == '100' ? 'selected' : '' }}>100
                                    registros</option>
                                <option value="250" {{ request('search_per_page') == '250' ? 'selected' : '' }}>250
                                    registros</option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Status-->
                        <div class="col-2">
                            <select class="form-select form-select-solid" name="search_status">
                                <option value=""
                                    {{ !request()->has('search_status') || request('search_status') === '' ? 'selected' : '' }}>
                                    Todos Status</option>
                                <option value="1" {{ request('search_status') === '1' ? 'selected' : '' }}>Ativo
                                </option>
                                <option value="0" {{ request('search_status') === '0' ? 'selected' : '' }}>Inativo
                                </option>
                            </select>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Delete (Deletados)-->
                        <div class="col-2">
                            <div class="form-check form-switch form-check-custom form-check-danger form-check-custom form-check-solid">
                                <input class="form-check-input h-25px w-45px" type="checkbox" value="1"
                                    id="search_deleted" name="search_deleted"
                                    {{ request('search_deleted') == '1' ? 'checked' : '' }} />
                                <label class="form-check-label" for="search_deleted">
                                    Exibir deletados
                                </label>
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Actions-->
                        <div class="col-6 text-end">
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-light-danger me-3">
                                <i class="ki-duotone ki-cross-circle fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Limpar Filtros
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="indicator-label"><i class="bi bi-search me-2"></i>Pesquisar</span>
                                <span class="indicator-progress">Aguarde...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row 2-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
    </div>
</div>
<!--end::Modal - Pesquisa Avançada-->

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==============================================
            // Modal de Pesquisa Avançada
            // ==============================================

            // Inicializa o Select2 em todos os selects do formulário
            $('#modal_search_form select').select2({
                minimumResultsForSearch: Infinity, // Remove a barra de busca
                dropdownParent: $('#modal_search')
            });

            // Inicializa o daterangepicker com ranges predefinidos
            const dateRangePicker = document.getElementById('kt_daterangepicker');
            if (dateRangePicker) {
                $("#kt_daterangepicker").daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Aplicar',
                        cancelLabel: 'Limpar',
                        fromLabel: 'De',
                        toLabel: 'Até',
                        customRangeLabel: 'Personalizado',
                        weekLabel: 'S',
                        daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho',
                            'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                        ],
                        firstDay: 0
                    },
                    ranges: {
                        "Hoje": [moment(), moment()],
                        "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                        "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                        "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                        "Este mês": [moment().startOf("month"), moment().endOf("month")],
                        "Mês passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                            "month").endOf("month")]
                    }
                });

                // Preenche o campo apenas quando o usuário seleciona um range
                $("#kt_daterangepicker").on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                        'DD/MM/YYYY'));
                });

                // Limpa o campo quando o usuário cancela
                $("#kt_daterangepicker").on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });
            }

            // ==============================================
            // Remove campos vazios antes do submit
            // ==============================================
            $('#modal_search_form').on('submit', function(e) {
                // Remove inputs e selects vazios para limpar a URL
                $(this).find('input, select').each(function() {
                    const $field = $(this);
                    const value = $field.val();

                    // Remove se estiver vazio ou for valor padrão não selecionado
                    if (value === '' || value === null ||
                        ($field.is('select') && value === '' && !$field.prop('required'))) {
                        $field.prop('disabled', true);
                    }
                });
            });

            // ==============================================
            // Botões de Limpar Campos (X)
            // ==============================================

            // Função para mostrar/ocultar botão X baseado no valor do campo
            function toggleClearButton(input) {
                const inputId = input.id;
                const clearBtn = document.querySelector(`[data-clear-input="${inputId}"]`);

                if (clearBtn) {
                    if (input.value.trim() !== '') {
                        clearBtn.classList.remove('d-none');
                    } else {
                        clearBtn.classList.add('d-none');
                    }
                }
            }

            // Monitora inputs com botão de limpar
            const clearableInputs = ['search_id', 'search_name', 'kt_daterangepicker'];

            clearableInputs.forEach(function(inputId) {
                const input = document.getElementById(inputId);

                if (input) {
                    // Verifica ao carregar
                    toggleClearButton(input);

                    // Verifica ao digitar
                    input.addEventListener('input', function() {
                        toggleClearButton(input);
                    });

                    // Verifica ao alterar (para o daterangepicker)
                    input.addEventListener('change', function() {
                        toggleClearButton(input);
                    });
                }
            });

            // Evento de click nos botões X
            document.querySelectorAll('[data-clear-input]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const inputId = this.getAttribute('data-clear-input');
                    const input = document.getElementById(inputId);

                    if (input) {
                        input.value = '';
                        toggleClearButton(input);
                        input.focus();
                    }
                });
            });
        });
    </script>
@endpush

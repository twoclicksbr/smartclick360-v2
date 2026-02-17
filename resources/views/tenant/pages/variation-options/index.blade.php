@extends('tenant.layouts.app')

@php
    $breadcrumbs = [
        ['label' => $tenant->name, 'url' => url('/dashboard/main')],
        ['label' => 'Opções de Variação', 'url' => null]
    ];
    $pageTitle = 'Opções de Variação';
    $pageDescription = 'Gerencie as opções de cada tipo de variação (ex: Vermelho, Azul para Cor)';
@endphp

@section('title', 'Opções de Variação - ' . $tenant->name)

@section('content')
    <!--begin::Card-->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" id="quick_search" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Buscar opção de variação" />
                </div>
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-new">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Novo
                    </button>
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="variation_options_table">
                <!--begin::Table head-->
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" id="check-all" />
                            </div>
                        </th>
                        <th class="min-w-50px">ID</th>
                        <th class="min-w-300px">Nome</th>
                        <th class="min-w-200px">Tipo de Variação</th>
                        <th class="min-w-100px">Status</th>
                        <th class="text-end min-w-100px">Ações</th>
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="text-gray-600 fw-semibold">
                    @forelse ($variationOptions as $variationOption)
                        <tr data-id="{{ $variationOption->id }}">
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input row-checkbox" type="checkbox"
                                        value="{{ $variationOption->id }}" />
                                </div>
                            </td>
                            <td>{{ $variationOption->id }}</td>
                            <td class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 mb-1">{{ $variationOption->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-info">{{ $variationOption->variationType->name }}</span>
                            </td>
                            <td>
                                <x-tenant.status-badge :status="$variationOption->status" />
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Ações
                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                </a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                    data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-edit"
                                            data-code="{{ encodeId($variationOption->id) }}">Editar</a>
                                    </div>
                                    @if ($variationOption->deleted_at)
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 btn-restore"
                                                data-code="{{ encodeId($variationOption->id) }}">Restaurar</a>
                                        </div>
                                    @else
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3 text-danger btn-delete"
                                                data-code="{{ encodeId($variationOption->id) }}">Excluir</a>
                                        </div>
                                    @endif
                                </div>
                                <!--end::Menu-->
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <div class="text-gray-600">Nenhuma opção de variação encontrada</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    {{-- Modal - Adicionar/Editar Opção de Variação --}}
    @include('tenant.layouts.modals.modal-variation-option')
@endsection

@push('scripts')
    <script>
        // Função helper para encode de ID (igual ao PHP)
        function encodeId(id) {
            return btoa(id).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        }

        // Função helper para decode de ID
        function decodeId(encoded) {
            let base64 = encoded.replace(/-/g, '+').replace(/_/g, '/');
            while (base64.length % 4) {
                base64 += '=';
            }
            return atob(base64);
        }

        $(document).ready(function() {
            const slug = '{{ $tenant->slug }}';

            // Check all checkboxes
            $('#check-all').on('change', function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Quick search
            $('#quick_search').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('#variation_options_table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Botão Novo
            $('#btn-new').on('click', function() {
                $('#modal_variation_option_form')[0].reset();
                $('#modal_variation_option_title').text('Nova Opção de Variação');
                $('#modal_variation_option_code').val('');
                $('#modal_variation_option').modal('show');
            });

            // Botão Editar
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const code = $(this).data('code');

                $.ajax({
                    url: `/${slug}/variation-options/${code}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            $('#modal_variation_option_title').text('Editar Opção de Variação');
                            $('#modal_variation_option_code').val(code);
                            $('#modal_variation_option_variation_type_id').val(data.variation_type_id);
                            $('#modal_variation_option_name').val(data.name);
                            $('#modal_variation_option_status').val(data.status ? 1 : 0);
                            $('#modal_variation_option').modal('show');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Erro ao carregar os dados');
                    }
                });
            });

            // Botão Excluir
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const code = $(this).data('code');

                Swal.fire({
                    text: "Tem certeza que deseja excluir esta opção de variação?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Cancelar",
                    customClass: {
                        confirmButton: "btn btn-danger",
                        cancelButton: "btn btn-secondary"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/${slug}/variation-options/${code}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    location.reload();
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Erro ao excluir a opção de variação');
                            }
                        });
                    }
                });
            });

            // Botão Restaurar
            $(document).on('click', '.btn-restore', function(e) {
                e.preventDefault();
                const code = $(this).data('code');

                $.ajax({
                    url: `/${slug}/variation-options/${code}/restore`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Erro ao restaurar a opção de variação');
                    }
                });
            });

            // Submit do formulário
            $('#modal_variation_option_form').on('submit', function(e) {
                e.preventDefault();

                const code = $('#modal_variation_option_code').val();
                const url = code ?
                    `/${slug}/variation-options/${code}` :
                    `/${slug}/variation-options`;
                const method = code ? 'PUT' : 'POST';

                const formData = {
                    variation_type_id: $('#modal_variation_option_variation_type_id').val(),
                    name: $('#modal_variation_option_name').val(),
                    status: $('#modal_variation_option_status').val(),
                };

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#modal_variation_option').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Erro ao salvar a opção de variação');
                        }
                    }
                });
            });
        });
    </script>
@endpush

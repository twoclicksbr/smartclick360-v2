@php
    // Configurações por tipo de módulo
    $config = [
        'people' => [
            'title' => 'Pessoa',
            'titleId' => 'modal_person_title',
            'modalId' => 'kt_modal_add_person',
            'formId' => 'kt_modal_add_person_form',
            'methodFieldId' => 'person_form_method',
            'recordFieldId' => 'person_id',
            'statusSwitchId' => 'person_status_switch',
            'statusHiddenId' => 'person_status_hidden',
        ],
        'products' => [
            'title' => 'Produto',
            'titleId' => 'modal_product_title',
            'modalId' => 'kt_modal_add_product',
            'formId' => 'kt_modal_add_product_form',
            'methodFieldId' => 'product_form_method',
            'recordFieldId' => 'product_id',
            'statusSwitchId' => 'product_status_switch',
            'statusHiddenId' => 'product_status_hidden',
        ],
        'sales' => [
            'title' => 'Venda',
            'titleId' => 'modal_sale_title',
            'modalId' => 'kt_modal_add_sale',
            'formId' => 'kt_modal_add_sale_form',
            'methodFieldId' => 'sale_form_method',
            'recordFieldId' => 'sale_id',
            'statusSwitchId' => 'sale_status_switch',
            'statusHiddenId' => 'sale_status_hidden',
        ],
    ];

    $c = $config[$module];

    // Define o tamanho do modal (padrão: mw-650px)
    // Opções: modal-sm, modal-lg, modal-xl, mw-300px, mw-400px, mw-450px, mw-500px,
    //         mw-550px, mw-600px, mw-650px, mw-700px, mw-750px, mw-800px, mw-900px, mw-1000px
    $modalSize = $modalSize ?? 'mw-650px';
@endphp

{{-- Modal - Adicionar/Editar {{ $c['title'] }} --}}
<div class="modal fade" id="{{ $c['modalId'] }}" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered {{ $modalSize }}">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header px-5 px-lg-10 pb-0">
                <!--begin::Modal title-->
                <h2 class="fw-bold" id="{{ $c['titleId'] }}">Adicionar {{ $c['title'] }}</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body my-7">
                <!--begin::Form-->
                <form id="{{ $c['formId'] }}" class="form" method="POST" action="{{ url('/' . $module) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="{{ $c['methodFieldId'] }}" value="POST">
                    <input type="hidden" name="{{ rtrim($module, 's') }}_id" id="{{ $c['recordFieldId'] }}" value="">
                    <input type="hidden" name="status" id="{{ $c['statusHiddenId'] }}" value="1">
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10">
                        {{-- Inclui o formulário específico do módulo --}}
                        @include('tenant.pages.' . rtrim($module, 's') . '.forms.' . rtrim($module, 's'))
                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-between px-5 px-lg-10">
                        <!--begin::Status-->
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="{{ $c['statusSwitchId'] }}" checked
                                       onchange="document.getElementById('{{ $c['statusHiddenId'] }}').value = this.checked ? '1' : '0';" />
                                <span class="form-check-label fw-semibold text-muted">
                                    Ativo
                                </span>
                            </label>
                        </div>
                        <!--end::Status-->
                        <!--begin::Buttons-->
                        <div>
                            <button type="reset" class="btn btn-sm btn-light-danger me-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="indicator-label">Salvar</span>
                                <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!--end::Buttons-->
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Adicionar/Editar {{ $c['title'] }} -->

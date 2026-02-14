@php
    // Configurações por tipo de submódulo
    $config = [
        'contact' => [
            'title' => 'Contato',
            'titleId' => 'modal_contact_title',
            'modalId' => 'kt_modal_add_contact',
            'formId' => 'kt_modal_add_contact_form',
            'methodFieldId' => 'contact_form_method',
            'recordFieldId' => 'contact_id',
            'statusSwitchId' => 'contact_status_switch',
            'statusHiddenId' => 'contact_status_hidden',
        ],
        'document' => [
            'title' => 'Documento',
            'titleId' => 'modal_document_title',
            'modalId' => 'kt_modal_add_document',
            'formId' => 'kt_modal_add_document_form',
            'methodFieldId' => 'document_form_method',
            'recordFieldId' => 'document_id',
            'statusSwitchId' => 'document_status_switch',
            'statusHiddenId' => 'document_status_hidden',
        ],
        'address' => [
            'title' => 'Endereço',
            'titleId' => 'modal_address_title',
            'modalId' => 'kt_modal_add_address',
            'formId' => 'kt_modal_add_address_form',
            'methodFieldId' => 'address_form_method',
            'recordFieldId' => 'address_id',
            'statusSwitchId' => 'address_status_switch',
            'statusHiddenId' => 'address_status_hidden',
        ],
        'note' => [
            'title' => 'Nota',
            'titleId' => 'modal_note_title',
            'modalId' => 'kt_modal_add_note',
            'formId' => 'kt_modal_add_note_form',
            'methodFieldId' => 'note_form_method',
            'recordFieldId' => 'note_id',
            'statusSwitchId' => 'note_status_switch',
            'statusHiddenId' => 'note_status_hidden',
        ],
        'file' => [
            'title' => 'Arquivo',
            'titleId' => 'modal_file_title',
            'modalId' => 'kt_modal_add_file',
            'formId' => 'kt_modal_add_file_form',
            'methodFieldId' => 'file_form_method',
            'recordFieldId' => 'file_id',
            'statusSwitchId' => 'file_status_switch',
            'statusHiddenId' => 'file_status_hidden',
        ],
    ];

    $c = $config[$submodule];
    $pluralSubmodule = $submodule . 's'; // contacts, documents, addresses, notes

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
                <form id="{{ $c['formId'] }}" class="form" method="POST" action="{{ url('/' . $moduleSlug . '/' . $recordId . '/' . $pluralSubmodule) }}" @if($submodule === 'file') enctype="multipart/form-data" @endif>
                    @csrf
                    <input type="hidden" name="_method" id="{{ $c['methodFieldId'] }}" value="POST">
                    <input type="hidden" name="{{ $submodule }}_id" id="{{ $c['recordFieldId'] }}" value="">
                    <input type="hidden" name="status" id="{{ $c['statusHiddenId'] }}" value="1">
                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10">
                        {{-- Inclui o formulário específico do submódulo --}}
                        @include('tenant.layouts.modals.forms.' . $submodule)
                    </div>
                    <!--end::Scroll-->
                    <!--begin::Actions-->
                    <div class="d-flex justify-content-between px-5 px-lg-10">
                        <!--begin::Status-->
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="{{ $c['statusSwitchId'] }}" checked />
                                <span class="form-check-label fw-semibold text-muted">
                                    Ativo
                                </span>
                            </label>
                        </div>
                        <!--end::Status-->
                        <!--begin::Buttons-->
                        <div>
                            <button type="{{ $submodule == 'contact' ? 'reset' : 'button' }}" class="btn btn-sm btn-light-danger me-3" data-bs-dismiss="modal">Cancelar</button>
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

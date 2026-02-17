<!--begin::Modal - Unidade-->
<div class="modal fade" id="modal_unit" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="modal_unit_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold" id="modal_unit_title">Nova Unidade</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body px-10 py-10">
                <!--begin::Form-->
                <form id="modal_unit_form" class="form">
                    <input type="hidden" id="modal_unit_code" name="code" />

                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="modal_unit_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#modal_unit_header"
                        data-kt-scroll-wrappers="#modal_unit_scroll" data-kt-scroll-offset="300px">

                        <!--begin::Input group - Nome-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nome</label>
                            <input type="text" id="modal_unit_name" name="name"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Nome da unidade"
                                required />
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Abreviação-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Abreviação</label>
                            <input type="text" id="modal_unit_abbreviation" name="abbreviation"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Ex: kg, L, un"
                                maxlength="10" required />
                            <div class="form-text">Máximo 10 caracteres</div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Casas Decimais-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Casas Decimais</label>
                            <input type="number" id="modal_unit_decimal_places" name="decimal_places"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="0"
                                min="0" max="8" value="0" required />
                            <div class="form-text">0 = inteiro, 1-8 = fracionado</div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Status-->
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Status</label>
                            <select id="modal_unit_status" name="status" class="form-select form-select-solid">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                        <!--end::Input group-->

                    </div>
                    <!--end::Scroll-->

                    <!--begin::Actions-->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Salvando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
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
<!--end::Modal - Unidade-->

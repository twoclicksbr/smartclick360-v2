<!--begin::Modal - Canal de Venda-->
<div class="modal fade" id="modal_sales_channel" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="modal_sales_channel_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold" id="modal_sales_channel_title">Novo Canal de Venda</h2>
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
                <form id="modal_sales_channel_form" class="form">
                    <input type="hidden" id="modal_sales_channel_code" name="code" />

                    <!--begin::Scroll-->
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="modal_sales_channel_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#modal_sales_channel_header"
                        data-kt-scroll-wrappers="#modal_sales_channel_scroll" data-kt-scroll-offset="300px">

                        <!--begin::Input group - Nome-->
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nome</label>
                            <input type="text" id="modal_sales_channel_name" name="name"
                                class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Ex: Loja Física, E-commerce, Marketplace"
                                required />
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Tabela de Preço-->
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Tabela de Preço</label>
                            <select id="modal_sales_channel_price_list_id" name="price_list_id" class="form-select form-select-solid">
                                <option value="">Sem tabela de preço</option>
                                @foreach ($priceLists as $priceList)
                                    <option value="{{ $priceList->id }}">{{ $priceList->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Status-->
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Status</label>
                            <select id="modal_sales_channel_status" name="status" class="form-select form-select-solid">
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
<!--end::Modal - Canal de Venda-->

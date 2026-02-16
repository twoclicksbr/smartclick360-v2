{{-- Navbar da Pessoa - Migrado para API --}}
{{-- Parâmetros: $code, $activeTab --}}

<!--begin::Navbar-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!--begin::Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <img id="navbar-avatar-img" src="" alt="Avatar"
                         class="symbol-label"
                         style="display: none; object-fit: cover;" />
                    <div id="navbar-avatar-initials" class="symbol-label fs-2 fw-semibold text-success bg-light-success">
                        ...
                    </div>
                </div>
            </div>
            <!--end::Pic-->

            <!--begin::Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <i id="navbar-status-icon" class="ki-duotone ki-check-circle fs-2 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <a href="{{ url('/people/' . $code) }}" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                <span id="navbar-full-name">Carregando...</span>
                            </a>
                        </div>
                        <!--end::Name-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <div id="navbar-birth-date-container" class="d-flex align-items-center text-gray-500 me-5 mb-2" style="display: none !important;">
                                <i class="ki-outline ki-gift fs-4 me-1"></i>
                                <span id="navbar-birth-date">...</span>
                            </div>

                            <a id="navbar-whatsapp-container" href="#" target="_blank"
                                class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2" style="display: none !important;">
                                <i class="ki-outline ki-whatsapp fs-4 me-1"></i>
                                <span id="navbar-whatsapp">...</span>
                            </a>

                            <a id="navbar-email-container" href="#"
                                class="d-flex align-items-center text-gray-500 text-hover-primary mb-2" style="display: none !important;">
                                <i class="ki-outline ki-sms fs-4 me-1"></i>
                                <span id="navbar-email">...</span>
                            </a>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->

                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        <button type="button" id="navbar-edit-btn"
                            class="btn btn-sm btn-primary me-3">
                            <i class="ki-outline ki-pencil fs-3"></i>
                            Editar
                        </button>

                        <div id="navbar-restore-option" style="display: none;">
                            <a href="#" onclick="restorePerson(); return false;" class="btn btn-sm btn-light-warning me-2">
                                <i class="ki-duotone ki-arrow-circle-left fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Restaurar
                            </a>
                        </div>

                        <!--begin::Menu-->
                        <div class="me-0">
                            <button class="btn btn-sm btn-icon btn-light-danger"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-cross fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ url('/people') }}" class="menu-link px-3">Fechar</a>
                                </div>
                                <div id="navbar-delete-option">
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-3">
                                        <a href="#" onclick="deletePerson(); return false;" class="menu-link px-3 text-danger">Excluir</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Title-->

                <!--begin::Stats-->
                <div class="d-flex flex-wrap justify-content-between">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap gap-3">
                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-contacts-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Contatos</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-documents-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Documentos</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-addresses-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Endereços</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-notes-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Observações</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-files-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Arquivos</div>
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->

                        <!--begin::Stats Line 2-->
                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-purchases-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Compras</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-sales-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Vendas</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-payables-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Pagar</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="navbar-receivables-count">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Receber</div>
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats Line 2-->
                    </div>
                    <!--end::Wrapper-->

                    <!--begin::Info-->
                    <div class="d-flex align-items-end w-200px w-sm-300px flex-column mt-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="text-end me-2">
                                <div class="fs-7 text-gray-600">Cadastrado em</div>
                                <div class="fs-6 fw-semibold text-gray-800" id="navbar-created-at">...</div>
                            </div>
                            <i class="ki-outline ki-calendar fs-3 text-primary"></i>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-end me-2">
                                <div class="fs-7 text-gray-600">Última atualização</div>
                                <div class="fs-6 fw-semibold text-gray-800" id="navbar-updated-at">...</div>
                            </div>
                            <i class="ki-outline ki-time fs-3 text-primary"></i>
                        </div>
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->

        <!--begin::Navs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'overview' ? 'active' : '' }}"
                   href="{{ url('/people/' . $code) }}">Visão Geral</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'contacts' ? 'active' : '' }}"
                   href="{{ url('/people/' . $code . '/contacts') }}">Contatos</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'documents' ? 'active' : '' }}"
                   href="{{ url('/people/' . $code . '/documents') }}">Documentos</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'addresses' ? 'active' : '' }}"
                   href="{{ url('/people/' . $code . '/addresses') }}">Endereços</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'files' ? 'active' : '' }}"
                   href="{{ url('/people/' . $code . '/files') }}">Arquivos</a>
            </li>
        </ul>
        <!--end::Navs-->
    </div>
</div>
<!--end::Navbar-->

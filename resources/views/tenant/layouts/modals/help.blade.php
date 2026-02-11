<!--begin::Modal - Ajuda-->
<div class="modal fade" id="kt_modal_help" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_help_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Central de Ajuda</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative mb-7">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text"
                        class="form-control form-control-solid ps-13"
                        placeholder="Buscar ajuda..."
                        id="help-search-input"
                    />
                </div>
                <!--end::Search-->

                <!--begin::Tabs-->
                <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_help_page">Sobre esta página</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_help_faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_help_contact">Contato</a>
                    </li>
                </ul>
                <!--end::Tabs-->

                <!--begin::Tab content-->
                <div class="tab-content" id="kt_help_tab_content">
                    <!--begin::Tab pane - Sobre esta página-->
                    <div class="tab-pane fade show active" id="kt_tab_help_page" role="tabpanel">
                        <div class="d-flex flex-column gap-5" id="help-page-content">
                            <!--begin::Título da página-->
                            <div class="d-flex align-items-center">
                                <i class="ki-outline ki-information-4 fs-2x text-primary me-3"></i>
                                <div>
                                    <h4 class="mb-1" id="help-current-page-title">{{ $pageTitle ?? 'Página Atual' }}</h4>
                                    <p class="text-gray-600 mb-0" id="help-current-page-desc">
                                        {{ $pageDescription ?? 'Descrição da página atual' }}
                                    </p>
                                </div>
                            </div>

                            <!--begin::Conteúdo dinâmico-->
                            <div id="help-dynamic-content">
                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                    <i class="ki-outline ki-information-5 fs-2tx text-primary me-4"></i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Bem-vindo à Central de Ajuda!</h4>
                                            <div class="fs-6 text-gray-700">
                                                Aqui você encontra informações sobre como usar esta página e suas funcionalidades.
                                                Use a busca acima para encontrar respostas rápidas ou navegue pelas abas.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Conteúdo dinâmico-->
                        </div>
                    </div>
                    <!--end::Tab pane-->

                    <!--begin::Tab pane - FAQ-->
                    <div class="tab-pane fade" id="kt_tab_help_faq" role="tabpanel">
                        <!--begin::Accordion-->
                        <div class="accordion accordion-icon-toggle" id="kt_accordion_help_faq">
                            <!--begin::Item-->
                            <div class="mb-5">
                                <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_faq_1">
                                    <span class="accordion-icon">
                                        <i class="ki-outline ki-plus-square fs-3 accordion-icon-off"></i>
                                        <i class="ki-outline ki-minus-square fs-3 accordion-icon-on"></i>
                                    </span>
                                    <h3 class="fs-4 fw-semibold mb-0 ms-4">Como fixar uma página nos Quick Links?</h3>
                                </div>
                                <div id="kt_accordion_faq_1" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_help_faq">
                                    <p class="text-gray-600">
                                        Para fixar uma página nos Quick Links, basta clicar no botão "Fixar"
                                        que aparece no canto superior direito de qualquer página. A página será
                                        adicionada ao seu painel de links rápidos, acessível pelo ícone de camadas
                                        no topo da tela.
                                    </p>
                                </div>
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="mb-5">
                                <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_faq_2">
                                    <span class="accordion-icon">
                                        <i class="ki-outline ki-plus-square fs-3 accordion-icon-off"></i>
                                        <i class="ki-outline ki-minus-square fs-3 accordion-icon-on"></i>
                                    </span>
                                    <h3 class="fs-4 fw-semibold mb-0 ms-4">Como alterar o tema (claro/escuro)?</h3>
                                </div>
                                <div id="kt_accordion_faq_2" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_help_faq">
                                    <p class="text-gray-600">
                                        Clique na sua foto de perfil no canto superior direito, depois em "Mode".
                                        Você pode escolher entre Light (claro), Dark (escuro) ou System (automático
                                        baseado no sistema operacional).
                                    </p>
                                </div>
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="mb-5">
                                <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_faq_3">
                                    <span class="accordion-icon">
                                        <i class="ki-outline ki-plus-square fs-3 accordion-icon-off"></i>
                                        <i class="ki-outline ki-minus-square fs-3 accordion-icon-on"></i>
                                    </span>
                                    <h3 class="fs-4 fw-semibold mb-0 ms-4">Como navegar entre as páginas?</h3>
                                </div>
                                <div id="kt_accordion_faq_3" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_help_faq">
                                    <p class="text-gray-600">
                                        Use o menu lateral à esquerda para acessar os diferentes módulos do sistema.
                                        Você também pode usar o breadcrumb (trilha de navegação) no topo para voltar
                                        às páginas anteriores ou clicar no ícone de casa para voltar ao dashboard.
                                    </p>
                                </div>
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="mb-5">
                                <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_faq_4">
                                    <span class="accordion-icon">
                                        <i class="ki-outline ki-plus-square fs-3 accordion-icon-off"></i>
                                        <i class="ki-outline ki-minus-square fs-3 accordion-icon-on"></i>
                                    </span>
                                    <h3 class="fs-4 fw-semibold mb-0 ms-4">Como editar meu perfil?</h3>
                                </div>
                                <div id="kt_accordion_faq_4" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_help_faq">
                                    <p class="text-gray-600">
                                        Clique na sua foto de perfil no canto superior direito e selecione "Meu Perfil".
                                        Lá você poderá editar suas informações pessoais, senha e preferências.
                                    </p>
                                </div>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Accordion-->
                    </div>
                    <!--end::Tab pane-->

                    <!--begin::Tab pane - Contato-->
                    <div class="tab-pane fade" id="kt_tab_help_contact" role="tabpanel">
                        <div class="d-flex flex-column gap-7">
                            <!--begin::Notice-->
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                <i class="ki-outline ki-information-5 fs-2tx text-info me-4"></i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">Precisa de mais ajuda?</h4>
                                        <div class="fs-6 text-gray-700">
                                            Nossa equipe de suporte está pronta para ajudar você!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Notice-->

                            <!--begin::Opções de contato-->
                            <div class="row g-5">
                                <!--begin::Email-->
                                <div class="col-12">
                                    <a href="mailto:suporte@smartclick360.com" class="d-flex align-items-center p-5 rounded bg-hover-light-primary border border-dashed border-gray-300">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-outline ki-sms fs-2x text-primary"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">Email</h5>
                                            <p class="text-gray-600 mb-0">suporte@smartclick360.com</p>
                                        </div>
                                    </a>
                                </div>
                                <!--end::Email-->

                                <!--begin::WhatsApp-->
                                <div class="col-12">
                                    <a href="https://wa.me/5512997698040" target="_blank" class="d-flex align-items-center p-5 rounded bg-hover-light-success border border-dashed border-gray-300">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-outline ki-whatsapp fs-2x text-success"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">WhatsApp</h5>
                                            <p class="text-gray-600 mb-0">(12) 99769-8040</p>
                                        </div>
                                    </a>
                                </div>
                                <!--end::WhatsApp-->

                                <!--begin::Documentação-->
                                <div class="col-12">
                                    <a href="#" class="d-flex align-items-center p-5 rounded bg-hover-light-warning border border-dashed border-gray-300">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-warning">
                                                <i class="ki-outline ki-book fs-2x text-warning"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">Documentação</h5>
                                            <p class="text-gray-600 mb-0">Central de conhecimento</p>
                                        </div>
                                    </a>
                                </div>
                                <!--end::Documentação-->
                            </div>
                            <!--end::Opções de contato-->

                            <!--begin::Horário de atendimento-->
                            <div class="bg-light rounded p-5">
                                <h5 class="mb-3">Horário de Atendimento</h5>
                                <div class="d-flex flex-column gap-2 text-gray-700">
                                    <div class="d-flex justify-content-between">
                                        <span>Segunda a Sexta:</span>
                                        <span class="fw-bold">08:00 - 18:00</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Sábado:</span>
                                        <span class="fw-bold">09:00 - 13:00</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-gray-500">
                                        <span>Domingo e Feriados:</span>
                                        <span class="fw-bold">Fechado</span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Horário de atendimento-->
                        </div>
                    </div>
                    <!--end::Tab pane-->
                </div>
                <!--end::Tab content-->
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal-->

{{-- Navbar da Pessoa - Componente Reutilizável --}}
{{-- Parâmetros: $person, $activeTab --}}

<!--begin::Navbar-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!--begin::Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    @php
                        $avatar = $person->files->where('name', 'avatar')->first();
                    @endphp

                    @if ($avatar)
                        <img src="{{ asset('storage/' . $avatar->path) }}" alt="{{ $person->first_name }} {{ $person->surname }}" />
                    @else
                        <div class="symbol-label fs-2 fw-semibold text-success bg-light-success">
                            {{ strtoupper(substr($person->first_name, 0, 1)) }}{{ strtoupper(substr($person->surname, 0, 1)) }}
                        </div>
                    @endif
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
                            @if ($person->status == 'active' || $person->status == 1 || $person->status === true)
                                <i class="ki-duotone ki-check-circle fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @else
                                <i class="ki-duotone ki-cross-circle fs-2 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            @endif
                            <a href="{{ url('/people/' . encodeId($person->id)) }}" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                {{ $person->first_name }} {{ $person->surname }}
                            </a>
                        </div>
                        <!--end::Name-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            @php
                                $whatsapp = $person->contacts->where('typeContact.name', 'WhatsApp')->first();
                                $email = $person->contacts->where('typeContact.name', 'Email')->first();
                            @endphp

                            @if ($person->birth_date)
                                <div class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                    <i class="ki-outline ki-gift fs-4 me-1"></i>
                                    {{ $person->birth_date->format('d/m/Y') }} ({{ $person->birth_date->age }} anos)
                                </div>
                            @endif

                            @if ($whatsapp)
                                <a href="https://wa.me/55{{ $whatsapp->value }}" target="_blank"
                                    class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                    <i class="ki-outline ki-whatsapp fs-4 me-1"></i>
                                    {{ format_phone($whatsapp->value) }}
                                </a>
                            @endif

                            @if ($email)
                                <a href="mailto:{{ $email->value }}"
                                    class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                    <i class="ki-outline ki-sms fs-4 me-1"></i>
                                    {{ $email->value }}
                                </a>
                            @endif
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->

                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        @php
                            $avatarForEdit = $person->files->where('name', 'avatar')->first();
                            $avatarUrl = $avatarForEdit ? asset('storage/' . $avatarForEdit->path) : '';
                        @endphp
                        <button type="button"
                            onclick="editPerson({{ $person->id }}, '{{ $person->first_name }}', '{{ $person->surname }}', '{{ $person->birth_date?->format('Y-m-d') }}', '{{ $avatarUrl }}', {{ $person->status ? 'true' : 'false' }})"
                            class="btn btn-sm btn-primary me-3">
                            <i class="ki-outline ki-pencil fs-3"></i>
                            Editar
                        </button>

                        <!--begin::Menu-->
                        <div class="me-0">
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="ki-solid ki-dots-horizontal fs-2x"></i>
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Adicionar Nota</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" onclick="addFile(); return false;" class="menu-link px-3">Adicionar Arquivo</a>
                                </div>
                                <div class="separator my-2"></div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger">Excluir</a>
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
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="contacts_counter">
                                        {{ $person->contacts->count() }}
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Contatos</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="documents_counter">
                                        {{ $person->documents->count() }}
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Documentos</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="addresses_counter">
                                        {{ $person->addresses->count() }}
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Endereços</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="files_counter">
                                        {{ $person->files->count() }}
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Arquivos</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="notes_counter">
                                        {{ $person->notes->count() }}
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Observações</div>
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->

                        <!--begin::Stats Line 2-->
                        <div class="d-flex flex-wrap gap-3 mt-3">
                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="purchases_counter">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Compras</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="sales_counter">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Vendas</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="payables_counter">
                                        0
                                    </span>
                                </div>
                                <div class="fs-8 text-gray-600">Pagar</div>
                            </div>
                            <!--end::Stat-->

                            <!--begin::Stat-->
                            <div class="d-flex align-items-center me-2">
                                <div class="symbol symbol-30px me-2">
                                    <span class="symbol-label bg-light fs-6 fw-bold text-gray-800" id="receivables_counter">
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
                                <div class="fs-6 fw-semibold text-gray-800">
                                    {{ $person->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <i class="ki-outline ki-calendar fs-3 text-primary"></i>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="text-end me-2">
                                <div class="fs-7 text-gray-600">Última atualização</div>
                                <div class="fs-6 fw-semibold text-gray-800">
                                    {{ $person->updated_at->format('d/m/Y H:i') }}</div>
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
                   href="{{ url('/people/' . encodeId($person->id)) }}">Visão Geral</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'contacts' ? 'active' : '' }}"
                   href="{{ url('/people/' . encodeId($person->id) . '/contacts') }}">Contatos</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'documents' ? 'active' : '' }}"
                   href="{{ url('/people/' . encodeId($person->id) . '/documents') }}">Documentos</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'addresses' ? 'active' : '' }}"
                   href="{{ url('/people/' . encodeId($person->id) . '/addresses') }}">Endereços</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab === 'files' ? 'active' : '' }}"
                   href="{{ url('/people/' . encodeId($person->id) . '/files') }}">Arquivos</a>
            </li>
        </ul>
        <!--end::Navs-->
    </div>
</div>
<!--end::Navbar-->

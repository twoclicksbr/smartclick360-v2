@extends('tenant.layouts.app')

@php
    // Função temporária para formatar telefone
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

    $breadcrumbs = [
        ['label' => $tenant->name, 'url' => url('/dashboard/main')],
        ['label' => 'Pessoas', 'url' => url('/people')],
        ['label' => $person->first_name . ' ' . $person->surname, 'url' => null],
    ];
    $pageTitle = $person->first_name . ' ' . $person->surname;
    $pageDescription = 'Detalhes da pessoa';
@endphp

@section('title', $person->first_name . ' ' . $person->surname . ' - ' . $tenant->name)

@section('content')
    @include('tenant.pages.people._navbar', ['person' => $person, 'activeTab' => 'overview'])

    <!--begin::Content-->
    <div class="row gx-9 gy-6">
        <!--begin::Col Left-->
        <div class="col-xl-5">
            <!--begin::Contatos-->
            <div class="card mb-6" id="contacts_card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Contatos</span>
                        <span class="text-muted mt-1 fw-semibold fs-7" id="contacts_subtitle">{{ $person->contacts->count() }}
                            {{ $person->contacts->count() == 1 ? 'contato' : 'contatos' }}
                            cadastrado{{ $person->contacts->count() == 1 ? '' : 's' }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" onclick="addContact()" class="btn btn-sm btn-light-primary">
                            <i class="ki-outline ki-plus fs-3"></i>
                            Adicionar
                        </button>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    @if ($person->contacts->count() > 0)
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle gs-0 gy-3" id="contacts_list">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th class="p-0 w-50px"></th>
                                        <th class="p-0 min-w-200px"></th>
                                        <th class="p-0 min-w-100px"></th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach ($person->contacts as $contact)
                                        <tr data-contact-id="{{ $contact->id }}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span
                                                        class="symbol-label
                                                        @if ($contact->typeContact->name == 'WhatsApp') bg-light-success
                                                        @elseif($contact->typeContact->name == 'Email') bg-light-primary
                                                        @elseif($contact->typeContact->name == 'Telefone') bg-light-info
                                                        @elseif($contact->typeContact->name == 'Celular') bg-light-warning
                                                        @else bg-light-secondary @endif">
                                                        @if ($contact->typeContact->name == 'WhatsApp')
                                                            <i class="ki-outline ki-whatsapp fs-2x text-success"></i>
                                                        @elseif($contact->typeContact->name == 'Email')
                                                            <i class="ki-outline ki-sms fs-2x text-primary"></i>
                                                        @elseif($contact->typeContact->name == 'Telefone' || $contact->typeContact->name == 'Celular')
                                                            <i
                                                                class="ki-outline ki-phone fs-2x
                                                                @if ($contact->typeContact->name == 'Telefone') text-info
                                                                @else text-warning @endif"></i>
                                                        @else
                                                            <i class="ki-outline ki-message-text fs-2x text-secondary"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="event.preventDefault(); editContact({{ $contact->id }});"
                                                    class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block @if (!$contact->status) text-decoration-line-through opacity-50 @endif">
                                                    @if ($contact->typeContact->name == 'WhatsApp' || $contact->typeContact->mask)
                                                        {{ format_phone($contact->value) }}
                                                    @else
                                                        {{ $contact->value }}
                                                    @endif
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="text-muted fw-semibold fs-7">{{ $contact->typeContact->name }}</span>
                                                    @if (!$contact->status)
                                                        <span class="badge badge-light-danger badge-sm">Inativo</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    onclick="event.preventDefault(); editContact({{ $contact->id }});"
                                                    class="btn btn-sm btn-icon btn-light-primary me-2"
                                                    data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#"
                                                    onclick="event.preventDefault(); deleteContact({{ $contact->id }});"
                                                    class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    @else
                        <div class="text-center text-muted py-10" id="contacts_empty_state">
                            <i class="ki-outline ki-phone fs-3x text-gray-400 mb-3"></i>
                            <div class="fw-semibold">Nenhum contato cadastrado</div>
                        </div>
                    @endif
                </div>
                <!--end::Body-->
            </div>
            <!--end::Contatos-->

            <!--begin::Endereços-->
            <div class="card mb-6" id="addresses_card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Endereços</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"
                            id="addresses_subtitle">{{ $person->addresses->count() }}
                            {{ $person->addresses->count() == 1 ? 'endereço' : 'endereços' }}
                            cadastrado{{ $person->addresses->count() == 1 ? '' : 's' }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" onclick="addAddress()" class="btn btn-sm btn-light-primary">
                            <i class="ki-outline ki-plus fs-3"></i>
                            Adicionar
                        </button>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    @if ($person->addresses->count() > 0)
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle gs-0 gy-3" id="addresses_list">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th class="p-0 w-50px"></th>
                                        <th class="p-0 min-w-200px"></th>
                                        <th class="p-0 min-w-100px"></th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach ($person->addresses as $address)
                                        <tr data-address-id="{{ $address->id }}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-success">
                                                        <i class="ki-outline ki-geolocation fs-2x text-success"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="event.preventDefault(); editAddress({{ $address->id }});"
                                                    class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block @if (!$address->status) text-decoration-line-through opacity-50 @endif">
                                                    {{ $address->street }},
                                                    {{ $address->number }}{{ $address->complement ? ' - ' . $address->complement : '' }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span
                                                        class="badge badge-light-success badge-sm">{{ $address->typeAddress->name }}</span>
                                                    @if ($address->is_main)
                                                        <span class="badge badge-success badge-sm">Principal</span>
                                                    @endif
                                                    <span class="text-muted fw-semibold fs-7">{{ $address->neighborhood }}
                                                        - {{ $address->city }}/{{ $address->state }}</span>
                                                    <span class="text-muted fw-semibold fs-7">CEP:
                                                        {{ substr($address->zip_code, 0, 5) }}-{{ substr($address->zip_code, 5) }}</span>
                                                    @if (!$address->status)
                                                        <span class="badge badge-light-danger badge-sm">Inativo</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    onclick="event.preventDefault(); editAddress({{ $address->id }});"
                                                    class="btn btn-sm btn-icon btn-light-primary me-2"
                                                    data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#"
                                                    onclick="event.preventDefault(); deleteAddress({{ $address->id }}, '{{ $address->typeAddress->name }}');"
                                                    class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    @else
                        <div class="text-center text-muted py-10" id="addresses_empty_state">
                            <i class="ki-outline ki-geolocation fs-3x text-gray-400 mb-3"></i>
                            <div class="fw-semibold">Nenhum endereço cadastrado</div>
                        </div>
                    @endif
                </div>
                <!--end::Body-->
            </div>
            <!--end::Endereços-->

            <!--begin::Documentos-->
            <div class="card mb-6" id="documents_card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Documentos</span>
                        <span class="text-muted mt-1 fw-semibold fs-7"
                            id="documents_subtitle">{{ $person->documents->count() }}
                            {{ $person->documents->count() == 1 ? 'documento' : 'documentos' }}
                            cadastrado{{ $person->documents->count() == 1 ? '' : 's' }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" onclick="addDocument()" class="btn btn-sm btn-light-primary">
                            <i class="ki-outline ki-plus fs-3"></i>
                            Adicionar
                        </button>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    @if ($person->documents->count() > 0)
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle gs-0 gy-3" id="documents_list">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th class="p-0 w-50px"></th>
                                        <th class="p-0 min-w-200px"></th>
                                        <th class="p-0 min-w-100px"></th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach ($person->documents as $document)
                                        @php
                                            $value = $document->value;
                                            $mask = $document->typeDocument->mask;
                                            $masked = $value;
                                            if ($mask) {
                                                $masked = '';
                                                $valueIndex = 0;
                                                for ($i = 0; $i < strlen($mask); $i++) {
                                                    if (
                                                        ($mask[$i] == '9' || $mask[$i] == '0') &&
                                                        $valueIndex < strlen($value)
                                                    ) {
                                                        $masked .= $value[$valueIndex++];
                                                    } elseif ($mask[$i] != '9' && $mask[$i] != '0') {
                                                        $masked .= $mask[$i];
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr data-document-id="{{ $document->id }}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-info">
                                                        <i class="ki-outline ki-document fs-2x text-info"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="event.preventDefault(); editDocument({{ $document->id }});"
                                                    class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block @if (!$document->status) text-decoration-line-through opacity-50 @endif">
                                                    {{ $masked }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="text-muted fw-semibold fs-7">{{ $document->typeDocument->name }}</span>
                                                    @if ($document->expiration_date)
                                                        <span class="text-muted fw-semibold fs-7">• Validade:
                                                            {{ $document->expiration_date->format('d/m/Y') }}</span>
                                                    @endif
                                                    @if (!$document->status)
                                                        <span class="badge badge-light-danger badge-sm">Inativo</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    onclick="event.preventDefault(); editDocument({{ $document->id }});"
                                                    class="btn btn-sm btn-icon btn-light-primary me-2"
                                                    data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#"
                                                    onclick="event.preventDefault(); deleteDocument({{ $document->id }}, '{{ $document->typeDocument->name }}');"
                                                    class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    @else
                        <div class="text-center text-muted py-10" id="documents_empty_state">
                            <i class="ki-outline ki-document fs-3x text-gray-400 mb-3"></i>
                            <div class="fw-semibold">Nenhum documento cadastrado</div>
                        </div>
                    @endif
                </div>
                <!--end::Body-->
            </div>
            <!--end::Documentos-->

            <!--begin::Observações-->
            <div class="card mb-6" id="notes_card">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Observações</span>
                        <span class="text-muted mt-1 fw-semibold fs-7" id="notes_subtitle">{{ $person->notes->count() }}
                            {{ $person->notes->count() == 1 ? 'observação' : 'observações' }}
                            cadastrada{{ $person->notes->count() == 1 ? '' : 's' }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" onclick="addNote()" class="btn btn-sm btn-light-primary">
                            <i class="ki-outline ki-plus fs-3"></i>
                            Adicionar
                        </button>
                    </div>
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body py-3">
                    @if ($person->notes->count() > 0)
                        <!--begin::Table container-->
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle gs-0 gy-3" id="notes_list">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th class="p-0 w-50px"></th>
                                        <th class="p-0 min-w-200px"></th>
                                        <th class="p-0 min-w-100px"></th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody>
                                    @foreach ($person->notes as $note)
                                        <tr data-note-id="{{ $note->id }}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#"
                                                    onclick="event.preventDefault(); editNote({{ $note->id }});"
                                                    class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block @if (!$note->status) text-decoration-line-through opacity-50 @endif">
                                                    {{ $note->content }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted fw-semibold fs-7">
                                                        <i
                                                            class="ki-outline ki-calendar fs-7 me-1"></i>{{ $note->created_at->format('d/m/Y H:i') }}
                                                    </span>
                                                    @if (!$note->status)
                                                        <span class="badge badge-light-danger badge-sm">Inativo</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    onclick="event.preventDefault(); editNote({{ $note->id }});"
                                                    class="btn btn-sm btn-icon btn-light-primary me-2"
                                                    data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#"
                                                    onclick="event.preventDefault(); deleteNote({{ $note->id }});"
                                                    class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table container-->
                    @else
                        <div class="text-center text-muted py-10" id="notes_empty_state">
                            <i class="ki-outline ki-notepad fs-3x text-gray-400 mb-3"></i>
                            <div class="fw-semibold">Nenhuma observação cadastrada</div>
                        </div>
                    @endif
                </div>
                <!--end::Body-->
            </div>
            <!--end::Observações-->
        </div>
        <!--end::Col Left-->

        <!--begin::Col Right-->
        <div class="col-xl-7">
            <!--begin::Chart Widget - Vendas-->
            <div class="card mb-5">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Histórico de Vendas</span>
                        <span class="text-muted fw-semibold fs-7">Mais de 1000 novos registros</span>
                    </h3>
                    <!--begin::Toolbar-->
                    <div class="card-toolbar" data-kt-buttons="true">
                        <a class="btn btn-sm btn-color-muted btn-active btn-active-primary active px-4 me-1" id="kt_sales_chart_year_btn">Ano</a>
                        <a class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4 me-1" id="kt_sales_chart_month_btn">Mês</a>
                        <a class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4" id="kt_sales_chart_week_btn">Semana</a>
                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-5 pb-0">
                    <!--begin::Chart-->
                    <div id="kt_sales_chart" style="height: 280px; min-height: 280px; max-height: 280px;"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Chart Widget - Vendas-->

            <!--begin::Row - Financeiro-->
            <div class="row g-5">
                <!--begin::Col - Pagar-->
                <div class="col-md-6">
                    <!--begin::Card-->
                    <div class="card card-flush mb-5">
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column p-0">
                            <!--begin::Stats-->
                            <div class="card-p pb-0">
                                <div class="d-flex flex-stack flex-wrap mb-5">
                                    <div class="me-2">
                                        <span class="text-gray-500 fw-semibold fs-7 d-block">Contas a Pagar</span>
                                        <span class="text-gray-800 fw-bold fs-2x">R$ 45.250</span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Chart-->
                            <div id="kt_payables_chart" class="card-rounded-bottom" style="height: 150px; min-height: 150px;"></div>
                            <!--end::Chart-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col - Pagar-->

                <!--begin::Col - Receber-->
                <div class="col-md-6">
                    <!--begin::Card-->
                    <div class="card card-flush mb-5">
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-column p-0">
                            <!--begin::Stats-->
                            <div class="card-p pb-0">
                                <div class="d-flex flex-stack flex-wrap mb-5">
                                    <div class="me-2">
                                        <span class="text-gray-500 fw-semibold fs-7 d-block">Contas a Receber</span>
                                        <span class="text-gray-800 fw-bold fs-2x">R$ 69.700</span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Stats-->
                            <!--begin::Chart-->
                            <div id="kt_receivables_chart" class="card-rounded-bottom" style="height: 150px; min-height: 150px;"></div>
                            <!--end::Chart-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Col - Receber-->
            </div>
            <!--end::Row - Financeiro-->
        </div>
        <!--end::Col Right-->
    </div>
    <!--end::Content-->

    {{-- Modal - Adicionar/Editar Pessoa --}}
    @include('tenant.layouts.modals.modal-module', ['module' => 'people', 'modalSize' => 'mw-800px'])

    {{-- Modal - Adicionar/Editar Contato --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'contact',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
    ])

    {{-- Modal - Adicionar/Editar Documento --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'document',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
    ])

    {{-- Modal - Adicionar/Editar Endereço --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'address',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
        'modalSize' => 'mw-750px',
    ])

    {{-- Modal - Adicionar/Editar Observação --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'note',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
        'modalSize' => 'mw-650px',
    ])

    {{-- Modal - Adicionar/Editar Arquivo --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'file',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
        'modalSize' => 'mw-650px',
    ])
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.8/dist/inputmask.min.js"></script>
    <script>
        // ========================================
        // Modal - Editar Pessoa
        // ========================================
        function editPerson(id, firstName, surname, birthDate, avatarUrl, status) {
            var personModal = document.getElementById('kt_modal_add_person');
            var personForm = document.getElementById('kt_modal_add_person_form');
            var modalTitle = document.getElementById('modal_person_title');

            // Configura modo edição
            modalTitle.textContent = 'Editar Pessoa';
            document.getElementById('person_form_method').value = 'PUT';
            document.getElementById('person_id').value = id;
            personForm.action = "{{ url('/people') }}/" + id;

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

            // Define o status baseado no valor real da pessoa
            document.getElementById('person_status_switch').checked = status;
            document.getElementById('person_status_hidden').value = status ? '1' : '0';

            // Abre o modal (reutiliza instância se existir)
            var modal = bootstrap.Modal.getOrCreateInstance(personModal);
            modal.show();
        }

        // ========================================
        // Modal - Adicionar Contato
        // ========================================
        function addContact() {
            var contactModal = document.getElementById('kt_modal_add_contact');
            var contactForm = document.getElementById('kt_modal_add_contact_form');
            var modalTitle = document.getElementById('modal_contact_title');

            // Reseta o formulário
            contactForm.reset();
            modalTitle.textContent = 'Adicionar Contato';
            document.getElementById('contact_form_method').value = 'POST';
            document.getElementById('contact_id').value = '';
            contactForm.action = "{{ url('/people/' . $person->id . '/contacts') }}";

            // Reseta o Select2
            $('#contact_type_select').val('').trigger('change');

            // Reseta o status para ativo
            document.getElementById('contact_status_switch').checked = true;
            document.getElementById('contact_status_hidden').value = '1';

            // Abre o modal (reutiliza instância se existir)
            var modal = bootstrap.Modal.getOrCreateInstance(contactModal);
            modal.show();
        }

        // ========================================
        // Modal - Editar Contato
        // ========================================
        function editContact(contactId) {
            var contactModal = document.getElementById('kt_modal_add_contact');
            var contactForm = document.getElementById('kt_modal_add_contact_form');
            var modalTitle = document.getElementById('modal_contact_title');

            // Busca os dados do contato via AJAX
            fetch(`/people/{{ $person->id }}/contacts/${contactId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza o modal
                        modalTitle.textContent = 'Editar Contato';
                        document.getElementById('contact_form_method').value = 'PUT';
                        document.getElementById('contact_id').value = contactId;
                        contactForm.action = `/people/{{ $person->id }}/contacts/${contactId}`;

                        // Preenche o tipo de contato
                        $('#contact_type_select').val(data.contact.type_contact_id).trigger('change');

                        // Aguarda o Select2 carregar e aplica a máscara
                        setTimeout(() => {
                            var valueInput = document.getElementById('contact_value');

                            // Remove máscaras anteriores
                            Inputmask.remove(valueInput);

                            // Busca a máscara do tipo selecionado
                            var selectedOption = document.querySelector(
                                `#contact_type_select option[value="${data.contact.type_contact_id}"]`);
                            var mask = selectedOption ? selectedOption.getAttribute('data-mask') : null;

                            // Preenche o valor SEM máscara (só números)
                            valueInput.value = data.contact.value;

                            // Aplica a máscara se existir
                            if (mask && mask !== '' && mask !== 'null') {
                                var processedMask = mask.replace(/0/g, '9');
                                var maskConfig = {
                                    placeholder: '',
                                    showMaskOnHover: false,
                                    showMaskOnFocus: false,
                                    clearIncomplete: false,
                                    jitMasking: false,
                                    autoUnmask: false
                                };

                                if (processedMask.includes('|')) {
                                    maskConfig.mask = processedMask.split('|');
                                } else {
                                    maskConfig.mask = processedMask;
                                }

                                var maskInstance = new Inputmask(maskConfig);
                                maskInstance.mask(valueInput);
                            }
                        }, 150);

                        // Define o status
                        var statusSwitch = document.getElementById('contact_status_switch');
                        statusSwitch.checked = data.contact.status == 1;
                        document.getElementById('contact_status_hidden').value = data.contact.status;

                        // Abre o modal (reutiliza instância se existir)
                        var modal = bootstrap.Modal.getOrCreateInstance(contactModal);
                        modal.show();
                    } else {
                        toastr.error('Erro ao carregar contato');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Erro ao carregar contato');
                });
        }

        // ========================================
        // Função global para inicializar tooltips
        // ========================================
        function initTooltips() {
            // Destroi tooltips existentes primeiro
            var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(function(element) {
                var existingTooltip = bootstrap.Tooltip.getInstance(element);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
                // Cria novo tooltip
                new bootstrap.Tooltip(element);
            });
        }

        // ========================================
        // Inicialização - DOMContentLoaded
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa tooltips
            initTooltips();

            // ========================================
            // Submit AJAX do formulário de contato
            // ========================================
            var contactForm = document.getElementById('kt_modal_add_contact_form');
            var contactModal = document.getElementById('kt_modal_add_contact');
            var contactsContainer = document.getElementById('contacts_list');
            var isSubmitting = false; // Flag para prevenir double-submit

            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Previne múltiplos submits
                    if (isSubmitting) {
                        console.log('Formulário já está sendo enviado, ignorando...');
                        return false;
                    }

                    isSubmitting = true;
                    var formData = new FormData(contactForm);
                    var submitButton = contactForm.querySelector('button[type="submit"]');

                    // Mostra loading no botão
                    submitButton.disabled = true;
                    submitButton.querySelector('.indicator-label').classList.add('d-none');
                    submitButton.querySelector('.indicator-progress').classList.remove('d-none');

                    // Envia via AJAX
                    fetch(contactForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok && response.status === 422) {
                                // Erro de validação
                                return response.json().then(errorData => {
                                    throw {
                                        validation: true,
                                        errors: errorData.errors
                                    };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Formata o valor do contato
                                var formattedValue = data.contact.value;
                                if (data.contact.type_mask) {
                                    formattedValue = formatPhoneTemp(data.contact.value);
                                }

                                // Define ícone e cor baseado no tipo de contato
                                var iconClass = 'ki-message-text';
                                var bgClass = 'bg-light-secondary';
                                var iconColor = 'text-secondary';

                                if (data.contact.type_name === 'WhatsApp') {
                                    iconClass = 'ki-whatsapp';
                                    bgClass = 'bg-light-success';
                                    iconColor = 'text-success';
                                } else if (data.contact.type_name === 'Email') {
                                    iconClass = 'ki-sms';
                                    bgClass = 'bg-light-primary';
                                    iconColor = 'text-primary';
                                } else if (data.contact.type_name === 'Telefone') {
                                    iconClass = 'ki-phone';
                                    bgClass = 'bg-light-info';
                                    iconColor = 'text-info';
                                } else if (data.contact.type_name === 'Celular') {
                                    iconClass = 'ki-phone';
                                    bgClass = 'bg-light-warning';
                                    iconColor = 'text-warning';
                                }

                                // Verifica se é edição ou criação
                                var isEditing = document.getElementById('contact_form_method').value ===
                                    'PUT';

                                if (isEditing) {
                                    // EDIÇÃO - Atualiza linha existente
                                    var contactId = document.getElementById('contact_id').value;
                                    var existingRow = document.querySelector(
                                        `#contacts_list tbody tr[data-contact-id="${contactId}"]`);

                                    // Verifica se está inativo para aplicar estilo
                                    var statusSwitch = document.getElementById('contact_status_switch');
                                    var isInactive = !statusSwitch.checked;
                                    var inactiveClass = isInactive ?
                                        'text-decoration-line-through opacity-50' : '';
                                    var inactiveBadge = isInactive ?
                                        '<span class="badge badge-light-danger badge-sm">Inativo</span>' :
                                        '';

                                    if (existingRow) {
                                        // Atualiza o conteúdo da linha existente
                                        existingRow.innerHTML = `
                                        <td>
                                            <div class="symbol symbol-50px me-2">
                                                <span class="symbol-label ${bgClass}">
                                                    <i class="ki-outline ${iconClass} fs-2x ${iconColor}"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); editContact(${data.contact.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block ${inactiveClass}">${formattedValue}</a>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted fw-semibold fs-7">${data.contact.type_name}</span>
                                                ${inactiveBadge}
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" onclick="event.preventDefault(); editContact(${data.contact.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                <i class="ki-outline ki-pencil fs-4"></i>
                                            </a>
                                            <a href="#" onclick="event.preventDefault(); deleteContact(${data.contact.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                <i class="ki-outline ki-trash fs-4"></i>
                                            </a>
                                        </td>
                                    `;
                                    }
                                } else {
                                    // CRIAÇÃO - Adiciona nova linha
                                    var emptyState = document.getElementById('contacts_empty_state');
                                    var cardBody = document.querySelector('#contacts_card .card-body');

                                    if (emptyState) {
                                        // Remove estado vazio e cria a tabela
                                        emptyState.remove();
                                        cardBody.innerHTML = `
                                        <div class="table-responsive">
                                            <table class="table align-middle gs-0 gy-3" id="contacts_list">
                                                <thead>
                                                    <tr>
                                                        <th class="p-0 w-50px"></th>
                                                        <th class="p-0 min-w-200px"></th>
                                                        <th class="p-0 min-w-100px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    `;
                                    }

                                    // Verifica se está inativo para aplicar estilo (na criação sempre será ativo)
                                    var statusSwitch = document.getElementById('contact_status_switch');
                                    var isInactive = !statusSwitch.checked;
                                    var inactiveClass = isInactive ?
                                        'text-decoration-line-through opacity-50' : '';
                                    var inactiveBadge = isInactive ?
                                        '<span class="badge badge-light-danger badge-sm">Inativo</span>' :
                                        '';

                                    // Cria o HTML da nova linha da tabela
                                    var contactRow = `
                                    <tr data-contact-id="${data.contact.id}">
                                        <td>
                                            <div class="symbol symbol-50px me-2">
                                                <span class="symbol-label ${bgClass}">
                                                    <i class="ki-outline ${iconClass} fs-2x ${iconColor}"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); editContact(${data.contact.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block ${inactiveClass}">${formattedValue}</a>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted fw-semibold fs-7">${data.contact.type_name}</span>
                                                ${inactiveBadge}
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" onclick="event.preventDefault(); editContact(${data.contact.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                <i class="ki-outline ki-pencil fs-4"></i>
                                            </a>
                                            <a href="#" onclick="event.preventDefault(); deleteContact(${data.contact.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                <i class="ki-outline ki-trash fs-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                `;

                                    // Adiciona a nova linha no tbody da tabela
                                    var tbody = document.querySelector('#contacts_list tbody');
                                    if (tbody) {
                                        tbody.insertAdjacentHTML('beforeend', contactRow);
                                    }

                                    // Atualiza contador de contatos (apenas na criação)
                                    var contactCounter = document.getElementById('contacts_counter');
                                    if (contactCounter) {
                                        var currentCount = parseInt(contactCounter.textContent);
                                        contactCounter.textContent = currentCount + 1;

                                        // Atualiza subtítulo
                                        var contactsSubtitle = document.getElementById(
                                            'contacts_subtitle');
                                        if (contactsSubtitle) {
                                            var newCount = currentCount + 1;
                                            contactsSubtitle.textContent = newCount + (newCount == 1 ?
                                                ' contato cadastrado' : ' contatos cadastrados');
                                        }
                                    }
                                }

                                // Fecha o modal
                                var modal = bootstrap.Modal.getInstance(contactModal);
                                modal.hide();

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);

                                // Reseta o formulário
                                contactForm.reset();

                                // Reinicializa tooltips
                                setTimeout(() => initTooltips(), 100);
                            } else {
                                toastr.error('Erro ao adicionar contato');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            // Se for erro de validação, mostra mensagem específica
                            if (error.validation && error.errors) {
                                // Pega a primeira mensagem de erro
                                const firstError = Object.values(error.errors)[0];
                                const errorMessage = Array.isArray(firstError) ? firstError[0] :
                                    firstError;
                                toastr.error(errorMessage);
                            } else {
                                toastr.error('Erro ao adicionar contato');
                            }
                        })
                        .finally(() => {
                            // Reseta flag de submissão
                            isSubmitting = false;

                            // Remove loading do botão
                            submitButton.disabled = false;
                            submitButton.querySelector('.indicator-label').classList.remove('d-none');
                            submitButton.querySelector('.indicator-progress').classList.add('d-none');
                        });
                });
            }

            // ========================================
            // Submit AJAX do formulário de documento
            // ========================================
            var documentForm = document.getElementById('kt_modal_add_document_form');
            var documentModal = document.getElementById('kt_modal_add_document');
            var isDocumentSubmitting = false;

            if (documentForm) {
                documentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (isDocumentSubmitting) {
                        return false;
                    }

                    isDocumentSubmitting = true;
                    var formData = new FormData(documentForm);
                    var submitButton = documentForm.querySelector('button[type="submit"]');

                    // Mostra loading no botão
                    submitButton.disabled = true;
                    submitButton.querySelector('.indicator-label').classList.add('d-none');
                    submitButton.querySelector('.indicator-progress').classList.remove('d-none');

                    // Envia via AJAX
                    fetch(documentForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok && response.status === 422) {
                                return response.json().then(errorData => {
                                    throw {
                                        validation: true,
                                        errors: errorData.errors
                                    };
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Formata o valor do documento com máscara
                                var formattedValue = data.document.value;
                                if (data.document.type_mask) {
                                    var mask = data.document.type_mask;
                                    var value = data.document.value;
                                    var masked = '';
                                    var valueIndex = 0;
                                    for (var i = 0; i < mask.length; i++) {
                                        if ((mask[i] == '9' || mask[i] == '0') && valueIndex < value
                                            .length) {
                                            masked += value[valueIndex++];
                                        } else if (mask[i] != '9' && mask[i] != '0') {
                                            masked += mask[i];
                                        }
                                    }
                                    formattedValue = masked;
                                }

                                // Formata data de validade se existir
                                var expirationInfo = '';
                                if (data.document.expiration_date) {
                                    expirationInfo =
                                        '<span class="text-muted fw-semibold fs-7">• Validade: ' + data
                                        .document.expiration_date_formatted + '</span>';
                                }

                                // Verifica se é edição ou criação
                                var isEditing = document.getElementById('document_form_method')
                                    .value === 'PUT';

                                if (isEditing) {
                                    // EDIÇÃO - Atualiza linha existente
                                    var documentId = document.getElementById('document_id').value;
                                    var existingRow = document.querySelector(
                                        `#documents_list tbody tr[data-document-id="${documentId}"]`
                                    );

                                    // Verifica se está inativo para aplicar estilo
                                    var statusSwitch = document.getElementById(
                                        'document_status_switch');
                                    var isInactive = !statusSwitch.checked;
                                    var inactiveClass = isInactive ?
                                        'text-decoration-line-through opacity-50' : '';
                                    var inactiveBadge = isInactive ?
                                        '<span class="badge badge-light-danger badge-sm">Inativo</span>' :
                                        '';

                                    if (existingRow) {
                                        existingRow.innerHTML = `
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-info">
                                                        <i class="ki-outline ki-document fs-2x text-info"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault(); editDocument(${data.document.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block ${inactiveClass}">${formattedValue}</a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted fw-semibold fs-7">${data.document.type_name}</span>
                                                    ${expirationInfo}
                                                    ${inactiveBadge}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" onclick="event.preventDefault(); editDocument(${data.document.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); deleteDocument(${data.document.id}, '${data.document.type_name}');" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        `;
                                    }
                                } else {
                                    // CRIAÇÃO - Adiciona nova linha
                                    var emptyState = document.getElementById('documents_empty_state');
                                    var cardBody = document.querySelector('#documents_card .card-body');

                                    if (emptyState) {
                                        // Remove estado vazio e cria a tabela
                                        emptyState.remove();
                                        cardBody.innerHTML = `
                                            <div class="table-responsive">
                                                <table class="table align-middle gs-0 gy-3" id="documents_list">
                                                    <thead>
                                                        <tr>
                                                            <th class="p-0 w-50px"></th>
                                                            <th class="p-0 min-w-200px"></th>
                                                            <th class="p-0 min-w-100px"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        `;
                                    }

                                    // Verifica se está inativo (na criação sempre será ativo)
                                    var statusSwitch = document.getElementById(
                                        'document_status_switch');
                                    var isInactive = !statusSwitch.checked;
                                    var inactiveClass = isInactive ?
                                        'text-decoration-line-through opacity-50' : '';
                                    var inactiveBadge = isInactive ?
                                        '<span class="badge badge-light-danger badge-sm">Inativo</span>' :
                                        '';

                                    // Cria o HTML da nova linha
                                    var documentRow = `
                                        <tr data-document-id="${data.document.id}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-info">
                                                        <i class="ki-outline ki-document fs-2x text-info"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault(); editDocument(${data.document.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block ${inactiveClass}">${formattedValue}</a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted fw-semibold fs-7">${data.document.type_name}</span>
                                                    ${expirationInfo}
                                                    ${inactiveBadge}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" onclick="event.preventDefault(); editDocument(${data.document.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); deleteDocument(${data.document.id}, '${data.document.type_name}');" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    `;

                                    // Adiciona a nova linha no tbody
                                    var tbody = document.querySelector('#documents_list tbody');
                                    if (tbody) {
                                        tbody.insertAdjacentHTML('beforeend', documentRow);
                                    }

                                    // Atualiza contador do card de stats (topo)
                                    var documentsCounter = document.getElementById('documents_counter');
                                    if (documentsCounter) {
                                        var currentCount = parseInt(documentsCounter.textContent);
                                        documentsCounter.textContent = currentCount + 1;
                                    }

                                    // Atualiza subtítulo
                                    var documentsSubtitle = document.getElementById(
                                        'documents_subtitle');
                                    if (documentsSubtitle) {
                                        var currentText = documentsSubtitle.textContent;
                                        var currentCount = parseInt(currentText);
                                        var newCount = currentCount + 1;
                                        documentsSubtitle.textContent = newCount + (newCount == 1 ?
                                            ' documento cadastrado' : ' documentos cadastrados');
                                    }
                                }

                                // Fecha o modal
                                var modal = bootstrap.Modal.getInstance(documentModal);
                                modal.hide();

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);

                                // Reseta o formulário
                                documentForm.reset();

                                // Reinicializa tooltips
                                setTimeout(() => initTooltips(), 100);
                            } else {
                                toastr.error('Erro ao salvar documento');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            if (error.validation && error.errors) {
                                const firstError = Object.values(error.errors)[0];
                                const errorMessage = Array.isArray(firstError) ? firstError[0] :
                                    firstError;
                                toastr.error(errorMessage);
                            } else {
                                toastr.error('Erro ao salvar documento');
                            }
                        })
                        .finally(() => {
                            isDocumentSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.querySelector('.indicator-label').classList.remove('d-none');
                            submitButton.querySelector('.indicator-progress').classList.add('d-none');
                        });
                });
            }
        });

        // Função auxiliar para formatar telefone (replicada do PHP)
        function formatPhoneTemp(phone) {
            if (!phone) return '';
            phone = phone.replace(/[^0-9]/g, '');
            var length = phone.length;
            if (length == 11) {
                return '(' + phone.substr(0, 2) + ') ' + phone.substr(2, 5) + '-' + phone.substr(7, 4);
            } else if (length == 10) {
                return '(' + phone.substr(0, 2) + ') ' + phone.substr(2, 4) + '-' + phone.substr(6, 4);
            }
            return phone;
        }

        // Função para excluir contato com confirmação SweetAlert
        function deleteContact(contactId) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta ação não poderá ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Executa a exclusão
                    fetch(`/people/{{ $person->id }}/contacts/${contactId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove a linha da tabela
                                var row = document.querySelector(
                                    `#contacts_list tbody tr[data-contact-id="${contactId}"]`);
                                if (row) {
                                    row.remove();
                                }

                                // Atualiza contador de contatos
                                var contactCounter = document.getElementById('contacts_counter');
                                if (contactCounter) {
                                    var currentCount = parseInt(contactCounter.textContent);
                                    var newCount = currentCount - 1;
                                    contactCounter.textContent = newCount;

                                    // Atualiza subtítulo
                                    var contactsSubtitle = document.getElementById('contacts_subtitle');
                                    if (contactsSubtitle) {
                                        contactsSubtitle.textContent = newCount + (newCount == 1 ?
                                            ' contato cadastrado' : ' contatos cadastrados');
                                    }

                                    // Se não houver mais contatos, mostra estado vazio
                                    if (newCount === 0) {
                                        var cardBody = document.querySelector('#contacts_card .card-body');
                                        if (cardBody) {
                                            cardBody.innerHTML = `
                                            <div class="text-center py-10" id="contacts_empty_state">
                                                <i class="ki-outline ki-message-text-2 fs-3x text-muted mb-5"></i>
                                                <div class="text-muted fw-semibold fs-6">Nenhum contato cadastrado</div>
                                            </div>
                                        `;
                                        }
                                    }
                                }

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir contato');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao excluir contato');
                        });
                }
            });
        }

        // ========================================
        // Submit AJAX do formulário de endereço
        // ========================================
        var addressForm = document.getElementById('kt_modal_add_address_form');
        var addressModal = document.getElementById('kt_modal_add_address');

        if (addressForm) {
            addressForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var url = this.action;
                var method = document.getElementById('address_form_method').value;

                fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fecha o modal
                            var modal = bootstrap.Modal.getInstance(addressModal);
                            modal.hide();

                            // Mostra mensagem de sucesso
                            toastr.success(data.message);

                            // Reseta o formulário
                            addressForm.reset();

                            // Recarrega a página para atualizar a lista
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            toastr.error('Erro ao salvar endereço');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Erro ao salvar endereço');
                    });
            });
        }

        // ========================================
        // DOCUMENTOS - Scripts
        // ========================================

        // ========================================
        // Modal - Adicionar Documento
        // ========================================
        function addDocument() {
            var documentModal = document.getElementById('kt_modal_add_document');
            var documentForm = document.getElementById('kt_modal_add_document_form');
            var modalTitle = document.getElementById('modal_document_title');

            // Reseta o formulário
            documentForm.reset();
            modalTitle.textContent = 'Adicionar Documento';
            document.getElementById('document_form_method').value = 'POST';
            document.getElementById('document_id').value = '';
            documentForm.action = "{{ url('/people/' . $person->id . '/documents') }}";

            // Reseta o Select2
            $('#document_type_select').val('').trigger('change');

            // Reseta o status para ativo
            document.getElementById('document_status_switch').checked = true;
            document.getElementById('document_status_hidden').value = '1';

            // Limpa campo de data
            document.getElementById('document_expiration_date').value = '';

            // Abre o modal (reutiliza instância se existir)
            var modal = bootstrap.Modal.getOrCreateInstance(documentModal);
            modal.show();
        }

        // ========================================
        // Modal - Editar Documento
        // ========================================
        function editDocument(documentId) {
            var documentModal = document.getElementById('kt_modal_add_document');
            var documentForm = document.getElementById('kt_modal_add_document_form');
            var modalTitle = document.getElementById('modal_document_title');

            // Busca os dados do documento via AJAX
            fetch(`/people/{{ $person->id }}/documents/${documentId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza o modal
                        modalTitle.textContent = 'Editar Documento';
                        document.getElementById('document_form_method').value = 'PUT';
                        document.getElementById('document_id').value = documentId;
                        documentForm.action = `/people/{{ $person->id }}/documents/${documentId}`;

                        // Preenche o tipo de documento
                        $('#document_type_select').val(data.document.type_document_id).trigger('change');

                        // Aguarda o Select2 carregar e aplica a máscara
                        setTimeout(() => {
                            var valueInput = document.getElementById('document_value');

                            // Remove máscaras anteriores
                            Inputmask.remove(valueInput);
                            valueInput.disabled = false;
                            valueInput.readOnly = false;

                            // Busca a máscara do tipo selecionado
                            var selectedOption = document.querySelector(
                                `#document_type_select option[value="${data.document.type_document_id}"]`);
                            var mask = selectedOption ? selectedOption.getAttribute('data-mask') : null;

                            // Preenche o valor SEM máscara (só números)
                            valueInput.value = data.document.value;

                            // Aplica a máscara se existir
                            if (mask && mask !== '' && mask !== 'null') {
                                var processedMask = mask.replace(/0/g, '9');

                                var maskInstance;
                                if (processedMask.includes('|')) {
                                    maskInstance = new Inputmask({
                                        mask: processedMask.split('|')
                                    });
                                } else {
                                    maskInstance = new Inputmask(processedMask);
                                }

                                maskInstance.mask(valueInput);
                            }
                        }, 150);

                        // Preenche data de validade se existir
                        if (data.document.expiration_date) {
                            document.getElementById('document_expiration_date').value = data.document.expiration_date;
                        } else {
                            document.getElementById('document_expiration_date').value = '';
                        }

                        // Define o status
                        var statusSwitch = document.getElementById('document_status_switch');
                        statusSwitch.checked = data.document.status == 1;
                        document.getElementById('document_status_hidden').value = data.document.status;

                        // Abre o modal (reutiliza instância se existir)
                        var modal = bootstrap.Modal.getOrCreateInstance(documentModal);
                        modal.show();
                    } else {
                        toastr.error('Erro ao carregar documento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Erro ao carregar documento');
                });
        }

        function deleteDocument(documentId, documentType) {
            Swal.fire({
                title: 'Tem certeza?',
                text: `Deseja excluir o documento ${documentType}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/people/{{ $person->id }}/documents/${documentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove a linha da tabela
                                var row = document.querySelector(
                                    `#documents_list tbody tr[data-document-id="${documentId}"]`);
                                if (row) {
                                    row.remove();
                                }

                                // Atualiza contador do card de stats (topo)
                                var documentsCounter = document.getElementById('documents_counter');
                                if (documentsCounter) {
                                    var currentCount = parseInt(documentsCounter.textContent);
                                    documentsCounter.textContent = currentCount - 1;
                                }

                                // Atualiza subtítulo
                                var documentsSubtitle = document.getElementById('documents_subtitle');
                                if (documentsSubtitle) {
                                    var currentText = documentsSubtitle.textContent;
                                    var currentCount = parseInt(currentText);
                                    var newCount = currentCount - 1;
                                    documentsSubtitle.textContent = newCount + (newCount == 1 ?
                                        ' documento cadastrado' : ' documentos cadastrados');

                                    // Se não houver mais documentos, mostra estado vazio
                                    if (newCount === 0) {
                                        var cardBody = document.querySelector('#documents_card .card-body');
                                        if (cardBody) {
                                            cardBody.innerHTML = `
                                                <div class="text-center text-muted py-10" id="documents_empty_state">
                                                    <i class="ki-outline ki-document fs-3x text-gray-400 mb-3"></i>
                                                    <div class="fw-semibold">Nenhum documento cadastrado</div>
                                                </div>
                                            `;
                                        }
                                    }
                                }

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir documento');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao excluir documento');
                        });
                }
            });
        }

        // ========================================
        // ENDEREÇOS - Scripts
        // ========================================

        // ========================================
        // Modal - Adicionar Endereço
        // ========================================
        function addAddress() {
            var addressModal = document.getElementById('kt_modal_add_address');
            var addressForm = document.getElementById('kt_modal_add_address_form');
            var modalTitle = document.getElementById('modal_address_title');

            // Reseta o formulário
            addressForm.reset();
            modalTitle.textContent = 'Adicionar Endereço';
            document.getElementById('address_form_method').value = 'POST';
            document.getElementById('address_id').value = '';
            addressForm.action = "{{ url('/people/' . $person->id . '/addresses') }}";

            // Reseta os Select2
            $('#address_type_select').val('').trigger('change');
            $('#address_state').val('').trigger('change');

            // Reseta o status para ativo
            document.getElementById('address_status_switch').checked = true;
            document.getElementById('address_status_hidden').value = '1';

            // Desmarca endereço principal
            document.getElementById('address_is_main').checked = false;

            // Limpa campo país (define BR como padrão)
            document.getElementById('address_country').value = 'BR';

            // Abre o modal
            var modal = bootstrap.Modal.getOrCreateInstance(addressModal);
            modal.show();
        }

        // ========================================
        // Modal - Editar Endereço
        // ========================================
        function editAddress(addressId) {
            var addressModal = document.getElementById('kt_modal_add_address');
            var addressForm = document.getElementById('kt_modal_add_address_form');
            var modalTitle = document.getElementById('modal_address_title');

            // Busca os dados do endereço via AJAX
            fetch(`/people/{{ $person->id }}/addresses/${addressId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza o modal
                        modalTitle.textContent = 'Editar Endereço';
                        document.getElementById('address_form_method').value = 'PUT';
                        document.getElementById('address_id').value = addressId;
                        addressForm.action = `/people/{{ $person->id }}/addresses/${addressId}`;

                        // Preenche os campos
                        $('#address_type_select').val(data.address.type_address_id).trigger('change');
                        document.getElementById('address_zip_code').value = data.address.zip_code;
                        document.getElementById('address_number').value = data.address.number;
                        document.getElementById('address_street').value = data.address.street;
                        document.getElementById('address_complement').value = data.address.complement || '';
                        document.getElementById('address_neighborhood').value = data.address.neighborhood;
                        document.getElementById('address_city').value = data.address.city;
                        $('#address_state').val(data.address.state).trigger('change');
                        document.getElementById('address_country').value = data.address.country;
                        document.getElementById('address_is_main').checked = data.address.is_main == 1;

                        // Define o status
                        var statusSwitch = document.getElementById('address_status_switch');
                        statusSwitch.checked = data.address.status == 1;
                        document.getElementById('address_status_hidden').value = data.address.status;

                        // Abre o modal
                        var modal = bootstrap.Modal.getOrCreateInstance(addressModal);
                        modal.show();
                    } else {
                        toastr.error('Erro ao carregar endereço');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Erro ao carregar endereço');
                });
        }

        function deleteAddress(addressId, addressType) {
            Swal.fire({
                title: 'Tem certeza?',
                text: `Deseja excluir o endereço ${addressType}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/people/{{ $person->id }}/addresses/${addressId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove a linha da tabela
                                var row = document.querySelector(
                                    `#addresses_list tbody tr[data-address-id="${addressId}"]`);
                                if (row) {
                                    row.remove();
                                }

                                // Atualiza contador do card de stats (topo)
                                var addressesCounter = document.getElementById('addresses_counter');
                                if (addressesCounter) {
                                    var currentCount = parseInt(addressesCounter.textContent);
                                    addressesCounter.textContent = currentCount - 1;
                                }

                                // Atualiza subtítulo
                                var addressesSubtitle = document.getElementById('addresses_subtitle');
                                if (addressesSubtitle) {
                                    var currentText = addressesSubtitle.textContent;
                                    var currentCount = parseInt(currentText);
                                    var newCount = currentCount - 1;
                                    addressesSubtitle.textContent = newCount + (newCount == 1 ?
                                        ' endereço cadastrado' : ' endereços cadastrados');

                                    // Se não houver mais endereços, mostra estado vazio
                                    if (newCount === 0) {
                                        var cardBody = document.querySelector('#addresses_card .card-body');
                                        if (cardBody) {
                                            cardBody.innerHTML = `
                                                <div class="text-center text-muted py-10" id="addresses_empty_state">
                                                    <i class="ki-outline ki-geolocation fs-3x text-gray-400 mb-3"></i>
                                                    <div class="fw-semibold">Nenhum endereço cadastrado</div>
                                                </div>
                                            `;
                                        }
                                    }
                                }

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir endereço');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao excluir endereço');
                        });
                }
            });
        }

        // ========================================
        // Modal - Adicionar Nota
        // ========================================
        function addNote() {
            var noteModal = document.getElementById('kt_modal_add_note');
            var noteForm = document.getElementById('kt_modal_add_note_form');
            var modalTitle = document.getElementById('modal_note_title');

            // Reseta o formulário
            noteForm.reset();
            modalTitle.textContent = 'Adicionar Observação';
            document.getElementById('note_form_method').value = 'POST';
            document.getElementById('note_id').value = '';
            noteForm.action = "{{ url('/people/' . $person->id . '/notes') }}";

            // Reseta o status para ativo
            document.getElementById('note_status_switch').checked = true;
            document.getElementById('note_status_hidden').value = '1';

            // Abre o modal
            var modal = bootstrap.Modal.getOrCreateInstance(noteModal);
            modal.show();
        }

        // ========================================
        // Modal - Editar Nota
        // ========================================
        function editNote(noteId) {
            var noteModal = document.getElementById('kt_modal_add_note');
            var noteForm = document.getElementById('kt_modal_add_note_form');
            var modalTitle = document.getElementById('modal_note_title');

            // Busca os dados da nota via AJAX
            fetch(`/people/{{ $person->id }}/notes/${noteId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza o modal
                        modalTitle.textContent = 'Editar Observação';
                        document.getElementById('note_form_method').value = 'PUT';
                        document.getElementById('note_id').value = noteId;
                        noteForm.action = `/people/{{ $person->id }}/notes/${noteId}`;

                        // Preenche o campo
                        document.getElementById('note_content').value = data.note.content || '';

                        // Define o status
                        var statusSwitch = document.getElementById('note_status_switch');
                        statusSwitch.checked = data.note.status == 1;
                        document.getElementById('note_status_hidden').value = data.note.status;

                        // Abre o modal
                        var modal = bootstrap.Modal.getOrCreateInstance(noteModal);
                        modal.show();
                    } else {
                        toastr.error('Erro ao carregar observação');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Erro ao carregar observação');
                });
        }

        // ========================================
        // Excluir Nota
        // ========================================
        function deleteNote(noteId) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta observação será excluída permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/people/{{ $person->id }}/notes/${noteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove a linha da lista
                                var item = document.querySelector(`[data-note-id="${noteId}"]`);
                                if (item) {
                                    item.remove();
                                }

                                // Atualiza contador
                                var notesSubtitle = document.getElementById('notes_subtitle');
                                if (notesSubtitle) {
                                    var currentText = notesSubtitle.textContent;
                                    var currentCount = parseInt(currentText);
                                    var newCount = currentCount - 1;
                                    notesSubtitle.textContent = newCount + (newCount == 1 ?
                                        ' observação' : ' observações');

                                    // Se não houver mais notas, mostra estado vazio
                                    if (newCount === 0) {
                                        var cardBody = document.querySelector('#notes_card .card-body');
                                        if (cardBody) {
                                            cardBody.innerHTML = `
                                                <div class="text-center text-muted py-10" id="notes_empty_state">
                                                    <i class="ki-outline ki-notepad fs-3x text-gray-400 mb-3"></i>
                                                    <div class="fw-semibold">Nenhuma observação cadastrada</div>
                                                </div>
                                            `;
                                        }
                                    }
                                }

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir observação');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao excluir observação');
                        });
                }
            });
        }

        // ========================================
        // Submit AJAX do formulário de notas
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            var noteForm = document.getElementById('kt_modal_add_note_form');
            var noteModal = document.getElementById('kt_modal_add_note');
            var isNoteSubmitting = false;

            if (noteForm) {
                noteForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (isNoteSubmitting) {
                        return false;
                    }

                    isNoteSubmitting = true;
                    var formData = new FormData(noteForm);
                    var submitButton = noteForm.querySelector('button[type="submit"]');

                    // Mostra loading
                    submitButton.disabled = true;
                    submitButton.querySelector('.indicator-label').classList.add('d-none');
                    submitButton.querySelector('.indicator-progress').classList.remove('d-none');

                    // Determina se é criação ou edição
                    var noteId = document.getElementById('note_id').value;
                    var isEditing = noteId !== '';

                    // Submete o formulário
                    fetch(noteForm.action, {
                            method: formData.get('_method') || 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (isEditing) {
                                    // EDIÇÃO - Atualiza linha existente
                                    var row = document.querySelector(
                                        `tr[data-note-id="${data.note.id}"]`);
                                    if (row) {
                                        var statusSwitch = document.getElementById(
                                        'note_status_switch');
                                        var isInactive = !statusSwitch.checked;
                                        var inactiveClass = isInactive ?
                                            'text-decoration-line-through opacity-50' : '';
                                        var inactiveBadge = isInactive ?
                                            '<span class="badge badge-light-danger badge-sm">Inativo</span>' :
                                            '';

                                        row.innerHTML = `
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault(); editNote(${data.note.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block ${inactiveClass}">
                                                    ${data.note.content || ''}
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted fw-semibold fs-7">
                                                        <i class="ki-outline ki-calendar fs-7 me-1"></i>${data.note.created_at}
                                                    </span>
                                                    ${inactiveBadge}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" onclick="event.preventDefault(); editNote(${data.note.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); deleteNote(${data.note.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        `;
                                    }
                                } else {
                                    // CRIAÇÃO - Adiciona nova linha
                                    var emptyState = document.getElementById('notes_empty_state');
                                    var cardBody = document.querySelector('#notes_card .card-body');

                                    if (emptyState) {
                                        // Remove estado vazio e cria a tabela
                                        emptyState.remove();
                                        cardBody.innerHTML = `
                                            <div class="table-responsive">
                                                <table class="table align-middle gs-0 gy-3" id="notes_list">
                                                    <thead>
                                                        <tr>
                                                            <th class="p-0 w-50px"></th>
                                                            <th class="p-0 min-w-200px"></th>
                                                            <th class="p-0 min-w-100px"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        `;
                                    }

                                    // Cria o HTML da nova linha da tabela
                                    var noteRow = `
                                        <tr data-note-id="${data.note.id}">
                                            <td>
                                                <div class="symbol symbol-50px me-2">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="ki-outline ki-notepad fs-2x text-primary"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="#" onclick="event.preventDefault(); editNote(${data.note.id});" class="text-gray-900 fw-bold text-hover-primary mb-1 fs-6 d-block">
                                                    ${data.note.content || ''}
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted fw-semibold fs-7">
                                                        <i class="ki-outline ki-calendar fs-7 me-1"></i>${data.note.created_at}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" onclick="event.preventDefault(); editNote(${data.note.id});" class="btn btn-sm btn-icon btn-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar">
                                                    <i class="ki-outline ki-pencil fs-4"></i>
                                                </a>
                                                <a href="#" onclick="event.preventDefault(); deleteNote(${data.note.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Excluir">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    `;

                                    // Adiciona a nova linha no tbody da tabela
                                    var tbody = document.querySelector('#notes_list tbody');
                                    if (tbody) {
                                        tbody.insertAdjacentHTML('beforeend', noteRow);
                                    }

                                    // Atualiza contador
                                    var notesSubtitle = document.getElementById('notes_subtitle');
                                    if (notesSubtitle) {
                                        var currentText = notesSubtitle.textContent;
                                        var currentCount = parseInt(currentText) || 0;
                                        var newCount = currentCount + 1;
                                        notesSubtitle.textContent = newCount + (newCount == 1 ?
                                            ' observação' : ' observações');
                                    }
                                }

                                // Fecha o modal
                                var modal = bootstrap.Modal.getInstance(noteModal);
                                modal.hide();

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);

                                // Reseta o formulário
                                noteForm.reset();

                                // Reinicializa tooltips
                                setTimeout(() => initTooltips(), 100);
                            } else {
                                toastr.error('Erro ao salvar observação');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao salvar observação');
                        })
                        .finally(() => {
                            isNoteSubmitting = false;
                            submitButton.disabled = false;
                            submitButton.querySelector('.indicator-label').classList.remove('d-none');
                            submitButton.querySelector('.indicator-progress').classList.add('d-none');
                        });
                });
            }
        });

        // ========================================
        // Adicionar Arquivo
        // ========================================
        function addFile() {
            var fileModal = document.getElementById('kt_modal_add_file');
            var fileForm = document.getElementById('kt_modal_add_file_form');
            var modalTitle = document.getElementById('modal_file_title');

            // Configura modo criação
            modalTitle.textContent = 'Adicionar Arquivo';
            document.getElementById('file_form_method').value = 'POST';
            fileForm.action = "{{ url('/people/' . $person->id . '/files') }}";

            // Limpa o formulário
            fileForm.reset();
            document.getElementById('file_status_hidden').value = '1';
            document.getElementById('file_status_switch').checked = true;

            // Reseta a dropzone
            var dropzone = document.getElementById('file_dropzone');
            if (dropzone) {
                dropzone.querySelector('.dz-message').innerHTML = `
                    <i class="ki-outline ki-file-up fs-3x text-primary"></i>
                    <div class="ms-4">
                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste o arquivo aqui ou clique para selecionar</h3>
                        <span class="fs-7 fw-semibold text-gray-500">Tamanho máximo: 10MB</span>
                    </div>
                `;
            }

            // Abre o modal
            var modal = bootstrap.Modal.getOrCreateInstance(fileModal);
            modal.show();
        }

        // ========================================
        // Submit AJAX do formulário de arquivo
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            var fileForm = document.getElementById('kt_modal_add_file_form');
            var fileModal = document.getElementById('kt_modal_add_file');
            var isFileSubmitting = false;

            if (fileForm) {
                fileForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (isFileSubmitting) {
                        return false;
                    }

                    // Chama a função de upload sequencial (definida no file.blade.php)
                    if (typeof uploadFilesSequentially === 'function') {
                        uploadFilesSequentially();
                    } else {
                        toastr.error('Erro ao processar arquivos');
                    }
                });
            }
        });

        // ========================================
        // Adicionar arquivo à grid
        // ========================================
        function addFileToGrid(file) {
            var filesList = document.getElementById('files_list');
            var emptyState = document.getElementById('files_empty_state');

            // Remove estado vazio se existir
            if (emptyState) {
                emptyState.parentElement.remove();
            }

            // Detecta se é imagem
            var extension = file.path.split('.').pop().toLowerCase();
            var isImage = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(extension) ||
                (file.mime_type && file.mime_type.startsWith('image/'));

            // Formata tamanho
            var size = file.size;
            var formattedSize;
            if (size < 1024) {
                formattedSize = size + ' B';
            } else if (size < 1048576) {
                formattedSize = (size / 1024).toFixed(2) + ' KB';
            } else {
                formattedSize = (size / 1048576).toFixed(2) + ' MB';
            }

            // Monta o HTML do card
            var fileCard = `
                <div class="col-md-4" data-file-id="${file.id}">
                    <div class="card" style="min-height: 300px;">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <div class="text-gray-800 d-flex flex-column">
                                <div class="symbol symbol-100px mb-5 mx-auto">
                                    ${isImage ?
                                        `<img src="/storage/${file.path}" alt="${file.name}" class="symbol-label rounded" style="object-fit: cover;" />` :
                                        `<span class="symbol-label bg-light-secondary">
                                                <i class="ki-outline ki-file fs-2x text-secondary"></i>
                                            </span>`
                                    }
                                </div>
                                <div class="fs-5 fw-bold mb-2" data-bs-toggle="tooltip" title="${file.name}">
                                    ${file.name.length > 20 ? file.name.substring(0, 20) + '..' : file.name}
                                </div>
                            </div>
                            <div class="fs-7 fw-semibold text-gray-500 mb-3">
                                ${formattedSize} • ${file.created_at}
                            </div>
                            <div class="d-flex justify-content-center gap-2">
                                ${isImage ?
                                    `<a href="#" onclick="event.preventDefault(); previewImage('/storage/${file.path}', '${file.name}');" class="btn btn-sm btn-icon btn-light-info" data-bs-toggle="tooltip" title="Visualizar">
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </a>` : ''
                                }
                                <a href="/storage/${file.path}" download class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="tooltip" title="Download">
                                    <i class="ki-outline ki-cloud-download fs-4"></i>
                                </a>
                                <a href="#" onclick="event.preventDefault(); deleteFile(${file.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Excluir">
                                    <i class="ki-outline ki-trash fs-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Insere no final da lista
            filesList.insertAdjacentHTML('beforeend', fileCard);
        }

        // ========================================
        // Atualizar contador de arquivos
        // ========================================
        function updateFilesCount(change) {
            var filesSubtitle = document.getElementById('files_subtitle');
            if (filesSubtitle) {
                var currentText = filesSubtitle.textContent;
                var currentCount = parseInt(currentText);
                var newCount = currentCount + change;
                filesSubtitle.textContent = newCount + (newCount == 1 ?
                    ' arquivo cadastrado' : ' arquivos cadastrados');
            }
        }

        // ========================================
        // Excluir Arquivo
        // ========================================
        function deleteFile(fileId) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Este arquivo será excluído permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/people/{{ $person->id }}/files/${fileId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove o card da grid
                                var item = document.querySelector(`[data-file-id="${fileId}"]`);
                                if (item) {
                                    item.remove();
                                }

                                // Atualiza contador
                                updateFilesCount(-1);

                                // Verifica se precisa mostrar estado vazio
                                var filesSubtitle = document.getElementById('files_subtitle');
                                if (filesSubtitle && filesSubtitle.textContent.startsWith('0 ')) {
                                    var cardBody = document.querySelector('#files_card .card-body');
                                    if (cardBody) {
                                        cardBody.innerHTML = `
                                            <div class="text-center text-muted py-10" id="files_empty_state">
                                                <i class="ki-outline ki-file fs-3x text-gray-400 mb-3"></i>
                                                <div class="fw-semibold">Nenhum arquivo cadastrado</div>
                                            </div>
                                        `;
                                    }
                                }

                                // Mostra mensagem de sucesso
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message || 'Erro ao excluir arquivo');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('Erro ao excluir arquivo');
                        });
                }
            });
        }

        // ========================================
        // Visualizar Imagem
        // ========================================
        function previewImage(imageSrc, imageName) {
            var modal = document.getElementById('image_preview_modal');
            var modalImage = document.getElementById('preview_image');
            var modalTitle = document.getElementById('preview_title');

            modalImage.src = imageSrc;
            modalImage.style.display = 'block';
            modalTitle.textContent = imageName;

            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        // ========================================
        // Gráfico de Vendas
        // ========================================
        var salesChart;
        var salesChartElement = document.getElementById('kt_sales_chart');

        if (salesChartElement) {
            var height = 280;
            var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
            var baseColor = KTUtil.getCssVariableValue('--bs-primary');
            var lightColor = KTUtil.getCssVariableValue('--bs-light-primary');

            var salesChartOptions = {
                series: [{
                    name: 'Vendas',
                    data: [30, 40, 40, 90, 90, 70, 70]
                }],
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {},
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'solid',
                    opacity: 1
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                    colors: [baseColor]
                },
                xaxis: {
                    categories: ['Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: baseColor,
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function(val) {
                            return "R$ " + val.toLocaleString('pt-BR')
                        }
                    }
                },
                colors: [lightColor],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    strokeColors: baseColor,
                    strokeWidth: 3
                }
            };

            salesChart = new ApexCharts(salesChartElement, salesChartOptions);
            salesChart.render();

            // Dados para diferentes períodos
            var salesData = {
                year: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    data: [30, 40, 40, 90, 90, 70, 70, 85, 95, 80, 75, 90]
                },
                month: {
                    categories: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                    data: [40, 60, 75, 85]
                },
                week: {
                    categories: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    data: [30, 40, 40, 90, 90, 70, 70]
                }
            };

            // Event listeners para os botões
            document.getElementById('kt_sales_chart_year_btn').addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active de todos
                document.querySelectorAll('[id^="kt_sales_chart_"]').forEach(btn => {
                    btn.classList.remove('active');
                });
                // Adiciona active no clicado
                this.classList.add('active');
                // Atualiza o gráfico
                salesChart.updateOptions({
                    xaxis: {
                        categories: salesData.year.categories
                    }
                });
                salesChart.updateSeries([{
                    name: 'Vendas',
                    data: salesData.year.data
                }]);
            });

            document.getElementById('kt_sales_chart_month_btn').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('[id^="kt_sales_chart_"]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                salesChart.updateOptions({
                    xaxis: {
                        categories: salesData.month.categories
                    }
                });
                salesChart.updateSeries([{
                    name: 'Vendas',
                    data: salesData.month.data
                }]);
            });

            document.getElementById('kt_sales_chart_week_btn').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('[id^="kt_sales_chart_"]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                salesChart.updateOptions({
                    xaxis: {
                        categories: salesData.week.categories
                    }
                });
                salesChart.updateSeries([{
                    name: 'Vendas',
                    data: salesData.week.data
                }]);
            });
        }

        // ========================================
        // Gráfico de Contas a Pagar
        // ========================================
        var payablesChartElement = document.getElementById('kt_payables_chart');
        if (payablesChartElement) {
            var payablesChartOptions = {
                series: [25250, 20000],
                chart: {
                    fontFamily: 'inherit',
                    type: 'donut',
                    height: 150,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: false
                            }
                        }
                    }
                },
                labels: ['A Pagar', 'Pagos'],
                colors: ['#F1416C', '#50CD89'],
                stroke: {
                    width: 0
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '13px',
                    fontWeight: 500,
                    labels: {
                        colors: '#A1A5B7'
                    },
                    markers: {
                        width: 6,
                        height: 6,
                        radius: 12
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 0
                    },
                    formatter: function(seriesName, opts) {
                        return seriesName + ': R$ ' + opts.w.globals.series[opts.seriesIndex].toLocaleString('pt-BR');
                    }
                }
            };

            var payablesChart = new ApexCharts(payablesChartElement, payablesChartOptions);
            payablesChart.render();
        }

        // ========================================
        // Gráfico de Contas a Receber
        // ========================================
        var receivablesChartElement = document.getElementById('kt_receivables_chart');
        if (receivablesChartElement) {
            var receivablesChartOptions = {
                series: [35700, 34000],
                chart: {
                    fontFamily: 'inherit',
                    type: 'donut',
                    height: 150,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: false
                            }
                        }
                    }
                },
                labels: ['A Receber', 'Recebidos'],
                colors: ['#FFC700', '#009EF7'],
                stroke: {
                    width: 0
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '13px',
                    fontWeight: 500,
                    labels: {
                        colors: '#A1A5B7'
                    },
                    markers: {
                        width: 6,
                        height: 6,
                        radius: 12
                    },
                    itemMargin: {
                        horizontal: 8,
                        vertical: 0
                    },
                    formatter: function(seriesName, opts) {
                        return seriesName + ': R$ ' + opts.w.globals.series[opts.seriesIndex].toLocaleString('pt-BR');
                    }
                }
            };

            var receivablesChart = new ApexCharts(receivablesChartElement, receivablesChartOptions);
            receivablesChart.render();
        }
    </script>
@endpush

{{-- Modal - Visualizar Imagem --}}
<div class="modal fade" id="image_preview_modal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="preview_title">Imagem</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <div class="modal-body text-center p-0">
                <img id="preview_image" src="" alt="Preview" class="img-fluid"
                    style="max-height: 70vh; display: none;" />
            </div>
        </div>
    </div>
</div>

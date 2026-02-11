<!--begin::Table-->
<div class="table-responsive">
    <table id="kt_table_people" class="table align-middle table-row-dashed fs-6 gy-5">
        <thead class="fw-bold text-muted bg-light">
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="w-2 ps-4 rounded-start">
                    <i class="ki-duotone ki-abstract-16">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </th>
                <x-tenant.table-checkbox :is-header="true" target-table="kt_table_people" />
                <th class="w-5">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_direction' => request('sort_by') == 'id' && request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}"
                       class="text-muted text-hover-primary">
                        #
                        @if(request('sort_by') == 'id')
                            <i class="ki-solid ki-arrow-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} fs-6 ms-1"></i>
                        @endif
                    </a>
                </th>
                <th class="">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'first_name', 'sort_direction' => request('sort_by') == 'first_name' && request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}"
                       class="text-muted text-hover-primary">
                        Nome
                        @if(request('sort_by') == 'first_name')
                            <i class="ki-solid ki-arrow-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} fs-6 ms-1"></i>
                        @endif
                    </a>
                </th>
                <th class="w-20">WhatsApp</th>
                <th class="w-10">
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_direction' => request('sort_by') == 'status' && request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}"
                       class="text-muted text-hover-primary">
                        Status
                        @if(request('sort_by') == 'status')
                            <i class="ki-solid ki-arrow-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} fs-6 ms-1"></i>
                        @endif
                    </a>
                </th>
                <th class="text-end w-10 pe-4 rounded-end">Ações</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold" data-kt-sortable="true">
            @forelse($people as $person)
                <tr data-id="{{ $person->id }}">
                    <x-tenant.table-sortable-handle />
                    <x-tenant.table-checkbox :item="$person" />
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                                {{ $person->id }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="d-flex justify-content-start flex-column">
                                <span class="text-gray-800 fw-bold mb-1">{{ $person->first_name }}
                                    {{ $person->surname }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $whatsapp = $person->contacts->first();
                        @endphp
                        @if ($whatsapp)
                            <a href="https://wa.me/55{{ $whatsapp->value }}" target="_blank"
                                class="text-gray-800 text-hover-success fw-bold">
                                <i class="ki-solid ki-whatsapp fs-4 me-1 text-success"></i>
                                {{ function_exists('format_phone') ? format_phone($whatsapp->value) : format_phone_temp($whatsapp->value) }}
                            </a>
                        @else
                            <span class="text-gray-400 text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <x-tenant.status-badge :status="$person->status" />
                    </td>
                    <td class="text-end">
                        <x-tenant.table-row-actions :item="$person" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-10">
                        <div class="d-flex flex-column align-items-center">
                            <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                            <h3 class="text-gray-800 fw-bold mb-2">Nenhuma pessoa cadastrada</h3>
                            <p class="text-gray-500 fs-6 mb-0">Comece adicionando sua primeira pessoa</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!--end::Table-->

<!--begin::Pagination-->
<div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
    <x-tenant.pagination-info :paginator="$people" />
    <div>
        {{ $people->links() }}
    </div>
</div>
<!--end::Pagination-->

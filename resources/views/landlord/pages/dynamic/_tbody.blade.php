@forelse($records as $record)
    <tr data-id="{{ $record->id }}">
        @if($config->show_drag)
            <td class="ps-4">
                <div class="d-flex justify-content-center align-items-center" data-kt-sortable-handle="true" style="cursor: move;" data-bs-toggle="tooltip" data-bs-placement="right" title="Arrastar para reordenar">
                    <i class="ki-duotone ki-abstract-16 fs-5 text-gray-400">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </td>
        @endif
        @if($config->show_checkbox)
            <td>
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input row-check" type="checkbox" value="{{ $record->id }}" />
                </div>
            </td>
        @endif
        @foreach($indexFields as $field)
            <td>
                @php
                    $value = $record->{$field->name} ?? '';
                    $displayValue = $value;

                    // grid_template: combina campos
                    if ($field->grid_template) {
                        $displayValue = $field->grid_template;
                        preg_match_all('/\{(\w+)\}/', $field->grid_template, $matches);
                        foreach ($matches[1] as $placeholder) {
                            $displayValue = str_replace('{' . $placeholder . '}', $record->{$placeholder} ?? '', $displayValue);
                        }
                    }

                    // Formatar por tipo
                    if (!$field->grid_template) {
                        if ($field->type === 'date' && $value) {
                            $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y');
                        } elseif ($field->type === 'datetime' && $value) {
                            $displayValue = \Carbon\Carbon::parse($value)->format('d/m/Y H:i');
                        } elseif ($field->type === 'decimal' && $value !== '') {
                            $displayValue = number_format((float)$value, $field->precision ?? 2, ',', '.');
                        }
                    }
                @endphp

                {{-- Badge (boolean ou options) --}}
                @if($field->ui_options && isset($field->ui_options[(string)$value]))
                    @php $opt = $field->ui_options[(string)$value]; @endphp
                    <span class="badge badge-light-{{ $opt['badge'] ?? 'primary' }}">{{ $opt['label'] ?? $value }}</span>

                {{-- Link --}}
                @elseif($field->grid_link)
                    @php
                        $link = $field->grid_link;
                        $code = encodeId($record->id);
                        $link = str_replace('{show}', url($module . '/' . $code), $link);
                        $link = str_replace('{edit}', url($module . '/' . $code . '/edit'), $link);
                        $link = str_replace('{value}', $value, $link);
                        $isExternal = str_starts_with($link, 'http');
                    @endphp
                    <a href="{{ $link }}" @if($isExternal) target="_blank" @endif class="text-gray-800 text-hover-primary">
                        {{ $displayValue }}
                    </a>

                {{-- Texto normal --}}
                @else
                    {{ $displayValue }}
                @endif
            </td>
        @endforeach
        @if($config->show_actions)
            <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-edit"
                        data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Editar">
                        <i class="ki-outline ki-pencil fs-5"></i>
                    </button>
                    @if($record->trashed())
                        <button type="button" class="btn btn-sm btn-icon btn-light-success btn-restore"
                            data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Restaurar">
                            <i class="ki-outline ki-arrow-circle-left fs-5"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete"
                            data-id="{{ $record->id }}" data-code="{{ encodeId($record->id) }}" title="Excluir">
                            <i class="ki-outline ki-trash fs-5"></i>
                        </button>
                    @endif
                </div>
            </td>
        @endif
    </tr>
@empty
    <tr>
        <td colspan="99" class="text-center py-10">
            <div class="d-flex flex-column align-items-center">
                <i class="ki-outline ki-information-4 fs-5x text-gray-400 mb-5"></i>
                <h3 class="text-gray-800 fw-bold mb-2">Nenhum registro encontrado</h3>
                <p class="text-gray-500 fs-6 mb-0">Tente ajustar os filtros de busca</p>
            </div>
        </td>
    </tr>
@endforelse

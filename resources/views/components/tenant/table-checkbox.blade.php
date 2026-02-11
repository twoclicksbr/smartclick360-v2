@if($isHeader)
    <th class="w-10px pe-2" data-bs-toggle="tooltip" data-bs-placement="right" title="Marcar / Desmarcar todos">
        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
            <input class="form-check-input" type="checkbox" data-kt-check="true"
                data-kt-check-target="#{{ $targetTable }} .form-check-input" value="1" />
        </div>
    </th>
@else
    <td>
        <div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox" value="{{ $item->id }}" />
        </div>
    </td>
@endif

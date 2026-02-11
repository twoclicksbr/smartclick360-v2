<form id="quick_search_form" method="GET" action="{{ $action }}" class="d-flex align-items-center position-relative my-1">
    <button type="submit" class="btn btn-sm btn-icon position-absolute ms-3 border-0 bg-transparent"
            data-bs-toggle="tooltip" title="Buscar">
        <i class="ki-outline ki-magnifier fs-3"></i>
    </button>
    <input type="text"
           id="quick_search_input"
           name="{{ $name }}"
           class="form-control form-control-sm form-control-solid w-250px ps-13"
           placeholder="{{ $placeholder }}"
           value="{{ $value }}" />
    @if($value)
        <button type="button" onclick="window.location.href='{{ $action }}'"
                class="btn btn-sm btn-icon position-absolute end-0 me-2"
                data-bs-toggle="tooltip" title="Limpar busca">
            <i class="ki-outline ki-cross fs-3 text-muted"></i>
        </button>
    @endif
</form>

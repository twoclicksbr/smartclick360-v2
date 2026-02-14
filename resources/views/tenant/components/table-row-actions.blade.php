@foreach($actions as $action)
    <a href="#"
       class="btn btn-sm btn-icon btn-{{ $action['color'] }} @if(!$loop->last) me-2 @endif"
       data-bs-toggle="tooltip"
       data-bs-placement="left"
       title="{{ $action['label'] }}"
       data-action="{{ $action['action'] }}"
       @if($item) data-id="{{ $item->id }}" @endif>
        <i class="ki-outline ki-{{ $action['icon'] }} fs-5"></i>
    </a>
@endforeach

<a href="{{ $href }}"
   @if($id) id="{{ $id }}" @endif
   {{ $attributes->merge(['class' => "btn btn-{$size} btn-{$color}"]) }}
   @if($modal) data-bs-toggle="modal" data-bs-target="#{{ $modal }}" @endif>
    @if($iconLibrary === 'bi')
        <i class="bi bi-{{ $icon }}"></i>
    @else
        <i class="{{ $icon }} fs-2"></i>
    @endif
    {{ $label }}
</a>

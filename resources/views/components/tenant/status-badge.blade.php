@if ($status)
    <span class="badge badge-light-success">{{ $activeText }}</span>
@else
    <span class="badge badge-light-danger">{{ $inactiveText }}</span>
@endif

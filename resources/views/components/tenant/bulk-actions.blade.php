<div id="{{ $containerId }}" class="d-none">
    <a href="#" id="{{ $buttonId }}" class="btn btn-light btn-light-danger btn-flex btn-center btn-sm me-2"
        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
        <span id="{{ $textId }}">Ações em massa</span>
        <i class="ki-outline ki-down fs-5 ms-1"></i>
    </a>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
        data-kt-menu="true">
        @foreach($actions as $action)
            @if(isset($action['type']) && $action['type'] === 'separator')
                <div class="separator my-2"></div>
            @else
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-action="{{ $action['action'] }}">
                        <i class="ki-outline ki-{{ $action['icon'] }} fs-5 me-2 text-{{ $action['color'] }}"></i>
                        {{ $action['label'] }}
                    </a>
                </div>
            @endif
        @endforeach
    </div>
    <!--end::Menu-->
</div>

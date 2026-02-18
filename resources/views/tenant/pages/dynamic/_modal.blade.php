{{-- Modal Create --}}
<div class="modal fade" id="modal_dynamic_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="modal_dynamic_title">{{ $config->description_create ?? 'Novo ' . $config->name }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>
            <form id="form_dynamic" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form_method" value="POST">
                <input type="hidden" name="_record_code" id="record_code" value="">
                <div class="modal-body py-10 px-lg-17">
                    <div class="row g-5">
                        @foreach($fieldsWithUi as $field)
                            @if($field->main)
                                @continue
                            @endif
                            <div class="{{ $field->grid_col ?? 'col-md-12' }} field-create {{ $field->visible_create ? '' : 'd-none' }} field-edit {{ $field->visible_edit ? '' : 'd-none' }}"
                                 data-visible-create="{{ $field->visible_create ? '1' : '0' }}"
                                 data-visible-edit="{{ $field->visible_edit ? '1' : '0' }}">

                                <label class="form-label {{ $field->required ? 'required' : '' }}" for="field_{{ $field->name }}">
                                    {{ $field->label }}
                                    @if($field->tooltip)
                                        <span class="ms-1" data-bs-toggle="tooltip" data-bs-placement="{{ $field->tooltip_direction ?? 'top' }}" title="{{ $field->tooltip }}">
                                            <i class="ki-outline ki-information-5 fs-7 text-gray-500"></i>
                                        </span>
                                    @endif
                                </label>

                                @switch($field->component)
                                    @case('input')
                                        <input type="{{ $field->type === 'integer' || $field->type === 'decimal' ? 'number' : 'text' }}"
                                               class="form-control form-control-solid"
                                               id="field_{{ $field->name }}"
                                               name="{{ $field->name }}"
                                               placeholder="{{ $field->placeholder ?? '' }}"
                                               @if($field->mask) data-inputmask="'mask': '{{ $field->mask }}'" @endif
                                               @if($field->type === 'decimal') step="0.{{ str_repeat('0', ($field->precision ?? 2) - 1) }}1" @endif
                                        />
                                        @break

                                    @case('select')
                                        <select class="form-select form-select-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" data-control="select2" data-placeholder="{{ $field->placeholder ?? 'Selecione...' }}" data-hide-search="true">
                                            <option></option>
                                            @if($field->ui_options)
                                                @foreach($field->ui_options as $optValue => $optConfig)
                                                    <option value="{{ $optValue }}">{{ is_array($optConfig) ? ($optConfig['label'] ?? $optValue) : $optConfig }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @break

                                    @case('select_module')
                                        @php
                                            $fkRecords = \Illuminate\Support\Facades\DB::connection($field->connection ?? 'tenant')
                                                ->table($field->fk_table)
                                                ->where('status', true)
                                                ->whereNull('deleted_at')
                                                ->orderBy($field->fk_label ?? 'name')
                                                ->get();
                                        @endphp
                                        <select class="form-select form-select-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" data-control="select2" data-placeholder="{{ $field->placeholder ?? 'Selecione...' }}">
                                            <option></option>
                                            @foreach($fkRecords as $fkRecord)
                                                <option value="{{ $fkRecord->{$field->fk_column ?? 'id'} }}">{{ $fkRecord->{$field->fk_label ?? 'name'} }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('date')
                                        <input type="text" class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder ?? 'DD/MM/AAAA' }}" data-inputmask="'mask': '99/99/9999'" />
                                        @break

                                    @case('datetime')
                                        <input type="text" class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder ?? 'DD/MM/AAAA HH:MM' }}" data-inputmask="'mask': '99/99/9999 99:99'" />
                                        @break

                                    @case('textarea')
                                        <textarea class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" rows="4" placeholder="{{ $field->placeholder ?? '' }}"></textarea>
                                        @break

                                    @case('switch')
                                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                                            <input class="form-check-input" type="checkbox" id="field_{{ $field->name }}" name="{{ $field->name }}" value="1" checked />
                                            <label class="form-check-label" for="field_{{ $field->name }}">
                                                @if($field->ui_options)
                                                    <span class="switch-label-on">{{ $field->ui_options['1']['label'] ?? 'Ativo' }}</span>
                                                @else
                                                    Ativo
                                                @endif
                                            </label>
                                        </div>
                                        @break

                                    @case('checkbox')
                                        @if($field->ui_options)
                                            @foreach($field->ui_options as $optValue => $optConfig)
                                                <div class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input" type="checkbox" name="{{ $field->name }}[]" value="{{ $optValue }}" id="field_{{ $field->name }}_{{ $optValue }}" />
                                                    <label class="form-check-label" for="field_{{ $field->name }}_{{ $optValue }}">
                                                        {{ is_array($optConfig) ? ($optConfig['label'] ?? $optValue) : $optConfig }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                        @break

                                    @case('radio')
                                        @if($field->ui_options)
                                            @foreach($field->ui_options as $optValue => $optConfig)
                                                <div class="form-check form-check-custom form-check-solid mb-2">
                                                    <input class="form-check-input" type="radio" name="{{ $field->name }}" value="{{ $optValue }}" id="field_{{ $field->name }}_{{ $optValue }}" />
                                                    <label class="form-check-label" for="field_{{ $field->name }}_{{ $optValue }}">
                                                        {{ is_array($optConfig) ? ($optConfig['label'] ?? $optValue) : $optConfig }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                        @break

                                    @case('password')
                                        <div class="position-relative" data-kt-password-meter="true">
                                            <input type="password" class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder ?? '' }}" autocomplete="new-password" />
                                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                                <i class="ki-outline ki-eye-slash fs-2"></i>
                                                <i class="ki-outline ki-eye fs-2 d-none"></i>
                                            </span>
                                            <div class="d-flex align-items-center mt-3" data-kt-password-meter-control="highlight">
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                            </div>
                                        </div>
                                        @break

                                    @case('upload')
                                        <input type="file" class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" />
                                        @break

                                    @default
                                        <input type="text" class="form-control form-control-solid" id="field_{{ $field->name }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder ?? '' }}" />
                                @endswitch

                                <div class="invalid-feedback" id="error_{{ $field->name }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn_submit">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSlug = '{{ $module }}';
    const modal = document.getElementById('modal_dynamic_create');
    const form = document.getElementById('form_dynamic');
    const formMethod = document.getElementById('form_method');
    const recordCode = document.getElementById('record_code');
    const btnSubmit = document.getElementById('btn_submit');
    const modalTitle = document.getElementById('modal_dynamic_title');
    const titleCreate = '{{ $config->description_create ?? "Novo " . $config->name }}';
    const titleEdit = '{{ $config->description_edit ?? "Editar " . $config->name }}';

    // Reset form ao abrir para criar
    modal.addEventListener('show.bs.modal', function(e) {
        if (!e.relatedTarget || !e.relatedTarget.classList.contains('btn-edit')) {
            form.reset();
            formMethod.value = 'POST';
            recordCode.value = '';
            modalTitle.textContent = titleCreate;
            clearErrors();
            toggleFieldVisibility('create');

            // Marcar switches como checked por padrão
            form.querySelectorAll('.form-switch input[type="checkbox"]').forEach(cb => cb.checked = true);
        }
    });

    // Editar: carregar dados
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            formMethod.value = 'PUT';
            recordCode.value = code;
            modalTitle.textContent = titleEdit;
            clearErrors();
            toggleFieldVisibility('edit');

            fetch(`/${moduleSlug}/${code}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success || data.data) {
                    const record = data.data || data;
                    populateForm(record);
                    new bootstrap.Modal(modal).show();
                }
            });
        });
    });

    // Submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();
        btnSubmit.setAttribute('data-kt-indicator', 'on');
        btnSubmit.disabled = true;

        const isEdit = formMethod.value === 'PUT';
        const code = recordCode.value;
        const url = isEdit ? `/${moduleSlug}/${code}` : `/${moduleSlug}`;
        const formData = new FormData(form);

        // Switch não envia valor quando desmarcado
        form.querySelectorAll('.form-switch input[type="checkbox"]').forEach(cb => {
            if (!cb.checked) {
                formData.set(cb.name, '0');
            }
        });

        if (isEdit) {
            formData.set('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            btnSubmit.removeAttribute('data-kt-indicator');
            btnSubmit.disabled = false;

            if (data.success) {
                location.reload();
            } else if (data.errors) {
                showErrors(data.errors);
            }
        })
        .catch(() => {
            btnSubmit.removeAttribute('data-kt-indicator');
            btnSubmit.disabled = false;
        });
    });

    function populateForm(record) {
        for (const [key, value] of Object.entries(record)) {
            const field = form.querySelector(`[name="${key}"]`);
            if (!field) continue;

            if (field.type === 'checkbox' && field.closest('.form-switch')) {
                field.checked = value == 1 || value === true;
            } else if (field.tagName === 'SELECT') {
                field.value = value;
                if (typeof $(field).select2 === 'function') {
                    $(field).val(value).trigger('change');
                }
            } else {
                field.value = value ?? '';
            }
        }
    }

    function toggleFieldVisibility(mode) {
        form.querySelectorAll('[data-visible-create]').forEach(el => {
            const visible = mode === 'create' ? el.dataset.visibleCreate === '1' : el.dataset.visibleEdit === '1';
            el.classList.toggle('d-none', !visible);
        });
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            const errorDiv = document.getElementById(`error_${field}`);
            if (input) input.classList.add('is-invalid');
            if (errorDiv) errorDiv.textContent = messages[0];
        }
    }
});
</script>
@endpush

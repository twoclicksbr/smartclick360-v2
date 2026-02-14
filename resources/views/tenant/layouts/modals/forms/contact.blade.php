{{-- Formulário - Contato --}}
<!--begin::Input group - Tipo-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Tipo de Contato</label>
    <!--end::Label-->
    <!--begin::Select-->
    <select name="type_contact_id" id="contact_type_select" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione..." data-hide-search="true" required>
        <option value="">Selecione...</option>
        @foreach(\App\Models\Tenant\TypeContact::orderBy('order')->get() as $type)
            <option value="{{ $type->id }}" data-mask="{{ $type->mask }}">{{ $type->name }}</option>
        @endforeach
    </select>
    <!--end::Select-->
</div>
<!--end::Input group-->
<!--begin::Input group - Valor-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Valor</label>
    <!--end::Label-->
    <!--begin::Input-->
    <input type="text" name="value" id="contact_value" class="form-control form-control-solid" placeholder="Digite o contato" value="" required />
    <!--end::Input-->
</div>
<!--end::Input group-->

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // Select2 - Tipo de Contato
    // ========================================
    $('#contact_type_select').select2({
        dropdownParent: $('#kt_modal_add_contact'),
        minimumResultsForSearch: Infinity
    });

    // ========================================
    // Máscara dinâmica - Contato
    // ========================================
    var contactValueInput = document.getElementById('contact_value');
    var contactMaskInstance = null;

    $('#contact_type_select').on('select2:select', function(e) {
        var selectedOption = e.params.data.element;
        var mask = selectedOption.getAttribute('data-mask');

        // Remove máscara anterior
        if (contactMaskInstance) {
            contactMaskInstance.remove();
            contactMaskInstance = null;
        }
        Inputmask.remove(contactValueInput);
        contactValueInput.value = '';

        // Aplica nova máscara se existir
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

            contactMaskInstance = new Inputmask(maskConfig);
            contactMaskInstance.mask(contactValueInput);
        }

        setTimeout(function() {
            contactValueInput.focus();
        }, 100);
    });

    // ========================================
    // Status Switch - Contato
    // ========================================
    document.getElementById('contact_status_switch').addEventListener('change', function() {
        document.getElementById('contact_status_hidden').value = this.checked ? '1' : '0';
    });
});
</script>
@endpush
@endonce

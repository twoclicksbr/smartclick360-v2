{{-- Formulário - Documento --}}
<!--begin::Input group - Tipo-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Tipo de Documento</label>
    <!--end::Label-->
    <!--begin::Select-->
    <select name="type_document_id" id="document_type_select" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione..." data-hide-search="true" required>
        <option value="">Selecione...</option>
        @foreach(\App\Models\Tenant\TypeDocument::orderBy('order')->get() as $type)
            <option value="{{ $type->id }}" data-mask="{{ $type->mask }}">{{ $type->name }}</option>
        @endforeach
    </select>
    <!--end::Select-->
</div>
<!--end::Input group-->
<!--begin::Input group - Valor-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Número</label>
    <!--end::Label-->
    <!--begin::Input-->
    <input type="text" name="value" id="document_value" class="form-control form-control-solid" placeholder="Digite o número do documento" value="" required />
    <!--end::Input-->
</div>
<!--end::Input group-->
<!--begin::Input group - Data de Validade-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="fw-semibold fs-6 mb-2">Data de Validade (opcional)</label>
    <!--end::Label-->
    <!--begin::Input-->
    <input type="date" name="expiration_date" id="document_expiration_date" class="form-control form-control-solid" value="" />
    <!--end::Input-->
</div>
<!--end::Input group-->

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // Select2 - Tipo de Documento
    // ========================================
    $('#document_type_select').select2({
        dropdownParent: $('#kt_modal_add_document'),
        minimumResultsForSearch: Infinity
    });

    // ========================================
    // Máscara dinâmica - Documento
    // ========================================
    var documentValueInput = document.getElementById('document_value');
    var documentMaskInstance = null;

    $('#document_type_select').on('select2:select', function(e) {
        var selectedOption = e.params.data.element;
        var mask = selectedOption.getAttribute('data-mask');

        // Remove máscara anterior
        if (documentMaskInstance) {
            documentMaskInstance.remove();
            documentMaskInstance = null;
        }
        Inputmask.remove(documentValueInput);
        documentValueInput.value = '';
        documentValueInput.disabled = false;
        documentValueInput.readOnly = false;

        // Aplica nova máscara se existir
        if (mask && mask !== '' && mask !== 'null') {
            var processedMask = mask.replace(/0/g, '9');

            if (processedMask.includes('|')) {
                documentMaskInstance = new Inputmask({
                    mask: processedMask.split('|')
                });
            } else {
                documentMaskInstance = new Inputmask(processedMask);
            }

            documentMaskInstance.mask(documentValueInput);
        }

        setTimeout(function() {
            documentValueInput.focus();
        }, 100);
    });

    // ========================================
    // Status Switch - Documento
    // ========================================
    document.getElementById('document_status_switch').addEventListener('change', function() {
        document.getElementById('document_status_hidden').value = this.checked ? '1' : '0';
    });
});
</script>
@endpush
@endonce

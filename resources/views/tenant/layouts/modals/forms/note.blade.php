{{-- Formulário - Nota --}}
<!--begin::Input group - Conteúdo-->
<div class="fv-row mb-7">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Observação</label>
    <!--end::Label-->
    <!--begin::Textarea-->
    <textarea name="content" id="note_content" class="form-control form-control-solid" rows="5" placeholder="Digite a observação" required></textarea>
    <!--end::Textarea-->
</div>
<!--end::Input group-->

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // Status Switch - Nota
    // ========================================
    document.getElementById('note_status_switch').addEventListener('change', function() {
        document.getElementById('note_status_hidden').value = this.checked ? '1' : '0';
    });
});
</script>
@endpush
@endonce

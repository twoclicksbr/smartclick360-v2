<!--begin::Row-->
<div class="row mb-7">
    <!--begin::Col - Avatar-->
    <div class="col-md-4">
        <div class="fv-row">
            <!--begin::Label-->
            <label class="d-block fw-semibold fs-6 mb-5"></label>
            <!--end::Label-->
            <!--begin::Image input-->
            <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('/assets/media/svg/files/blank-image.svg')">
                <!--begin::Preview existing avatar-->
                <div class="image-input-wrapper w-125px h-125px" style="background-image: url('/assets/media/avatars/blank.png')"></div>
                <!--end::Preview existing avatar-->
                <!--begin::Label-->
                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar avatar">
                    <i class="ki-outline ki-pencil fs-7"></i>
                    <!--begin::Inputs-->
                    <input type="file" name="avatar" id="avatar_input" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="remove_avatar" id="remove_avatar" value="0" />
                    <!--end::Inputs-->
                </label>
                <!--end::Label-->
                <!--begin::Cancel-->
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancelar avatar">
                    <i class="ki-outline ki-cross fs-2"></i>
                </span>
                <!--end::Cancel-->
                <!--begin::Remove-->
                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover avatar">
                    <i class="ki-outline ki-cross fs-2"></i>
                </span>
                <!--end::Remove-->
            </div>
            <!--end::Image input-->
            <!--begin::Hint-->
            <div class="form-text">Tipos permitidos: png, jpg, jpeg.</div>
            <!--end::Hint-->
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col - Nome e Sobrenome-->
    <div class="col-md-8">
        <div class="row">
            <!--begin::Col - Nome-->
            <div class="col-md-4">
                <!--begin::Input group-->
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Nome</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" name="first_name" id="person_first_name" class="form-control form-control-solid" placeholder="Nome" value="" required />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Col-->
            <!--begin::Col - Sobrenome-->
            <div class="col-md-8">
                <!--begin::Input group-->
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Sobrenome</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" name="surname" id="person_surname" class="form-control form-control-solid" placeholder="Sobrenome" value="" required />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Col-->
        </div>
    
        <div class="row">
            <!--begin::Col - Nascimento-->
            <div class="col-md-6">
                <!--begin::Input group-->
                <div class="fv-row mb-7">
                    <!--begin::Label-->
                    <label class="required fw-semibold fs-6 mb-2">Nascimento</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="date" name="birth_date" id="person_birth_date" class="form-control form-control-solid" placeholder="dd/mm/aaaa" value="" required />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Col-->
        </div>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleciona os botões de avatar
    const avatarInput = document.getElementById('avatar_input');
    const removeAvatarInput = document.getElementById('remove_avatar');
    const imageInputWrapper = document.querySelector('[data-kt-image-input="true"]');

    if (imageInputWrapper) {
        // Botão de remover avatar
        const removeBtn = imageInputWrapper.querySelector('[data-kt-image-input-action="remove"]');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                removeAvatarInput.value = '1';
            });
        }

        // Botão de cancelar
        const cancelBtn = imageInputWrapper.querySelector('[data-kt-image-input-action="cancel"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                removeAvatarInput.value = '0';
            });
        }
    }

    // Quando selecionar novo avatar, reseta o flag de remoção
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            removeAvatarInput.value = '0';
        });
    }
});
</script>

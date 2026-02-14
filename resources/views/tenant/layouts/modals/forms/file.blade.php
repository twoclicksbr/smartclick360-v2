{{-- Formulário - Arquivo --}}
<!--begin::Input group - Nome do Arquivo (quando editando)-->
<div class="fv-row mb-7" id="file_name_group" style="display: none;">
    <!--begin::Label-->
    <label class="fw-semibold fs-6 mb-2">Nome do Arquivo</label>
    <!--end::Label-->
    <!--begin::Input-->
    <input type="text" name="name" id="file_name" class="form-control form-control-solid" placeholder="Nome do arquivo" />
    <!--end::Input-->
    <!--begin::Hint-->
    <div class="form-text">Edite apenas o nome, sem a extensão</div>
    <!--end::Hint-->
</div>
<!--end::Input group-->

<!--begin::Input group - Upload do Arquivo (quando criando)-->
<div class="fv-row mb-7" id="file_upload_group">
    <!--begin::Label-->
    <label class="required fw-semibold fs-6 mb-2">Arquivos</label>
    <!--end::Label-->
    <!--begin::Dropzone-->
    <div class="dropzone border-2 border-dashed rounded" id="file_dropzone" style="transition: all 0.3s ease; cursor: pointer;">
        <div class="dz-message needsclick">
            <i class="ki-outline ki-file-up fs-3x text-primary"></i>
            <div class="ms-4">
                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste os arquivos aqui ou clique para selecionar</h3>
                <span class="fs-7 fw-semibold text-gray-500">Tamanho máximo: 10MB por arquivo</span>
            </div>
        </div>
    </div>
    <!--end::Dropzone-->
    <!--begin::Input (hidden fallback)-->
    <input type="file" name="file" id="file_input" class="d-none" accept="*/*" multiple />
    <!--end::Input-->

    <!--begin::Files List-->
    <div id="files_list_container" class="mt-5" style="display: none;">
        <div class="separator separator-dashed mb-5"></div>
        <div id="files_queue"></div>
    </div>
    <!--end::Files List-->
</div>
<!--end::Input group-->

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // Status Switch - Arquivo
    // ========================================
    document.getElementById('file_status_switch').addEventListener('change', function() {
        document.getElementById('file_status_hidden').value = this.checked ? '1' : '0';
    });

    // ========================================
    // Upload múltiplo de arquivos
    // ========================================
    var dropzone = document.getElementById('file_dropzone');
    var fileInput = document.getElementById('file_input');
    var filesListContainer = document.getElementById('files_list_container');
    var filesQueue = document.getElementById('files_queue');
    var selectedFiles = [];
    var isUploading = false;

    if (dropzone && fileInput) {
        // Click para selecionar arquivo
        dropzone.addEventListener('click', function(e) {
            if (!isUploading) {
                fileInput.click();
            }
        });

        // Mostra lista de arquivos selecionados
        function showFilesList() {
            filesQueue.innerHTML = '';

            if (selectedFiles.length === 0) {
                filesListContainer.style.display = 'none';
                dropzone.querySelector('.dz-message').innerHTML = `
                    <i class="ki-outline ki-file-up fs-3x text-primary"></i>
                    <div class="ms-4">
                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste os arquivos aqui ou clique para selecionar</h3>
                        <span class="fs-7 fw-semibold text-gray-500">Tamanho máximo: 10MB por arquivo</span>
                    </div>
                `;
                return;
            }

            filesListContainer.style.display = 'block';
            dropzone.querySelector('.dz-message').innerHTML = `
                <i class="ki-outline ki-check-circle fs-3x text-success"></i>
                <div class="ms-4">
                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">${selectedFiles.length} arquivo(s) selecionado(s)</h3>
                    <span class="fs-7 fw-semibold text-gray-500">Clique aqui para adicionar mais ou clique em "Salvar" para iniciar o upload</span>
                </div>
            `;

            selectedFiles.forEach((file, index) => {
                var fileSize = (file.size / 1024 / 1024).toFixed(2);
                var fileItem = document.createElement('div');
                fileItem.className = 'd-flex align-items-center justify-content-between p-3 mb-2 bg-light rounded';
                fileItem.id = `file_item_${index}`;
                fileItem.innerHTML = `
                    <div class="d-flex align-items-center flex-grow-1">
                        <i class="ki-outline ki-file fs-2x text-primary me-3"></i>
                        <div>
                            <div class="fw-bold text-gray-800">${file.name}</div>
                            <div class="text-muted fs-7">${fileSize} MB</div>
                        </div>
                    </div>
                    <div class="file-status d-flex align-items-center gap-2">
                        <span class="badge badge-light-secondary">Aguardando</span>
                        <button type="button" class="btn btn-icon btn-sm btn-light-danger" onclick="removeFileFromList(${index})" title="Remover">
                            <i class="ki-outline ki-trash fs-4"></i>
                        </button>
                    </div>
                `;
                filesQueue.appendChild(fileItem);
            });
        }

        // Remove arquivo da lista antes do upload
        window.removeFileFromList = function(index) {
            if (isUploading) {
                return; // Não pode remover durante upload
            }

            // Remove do array
            selectedFiles.splice(index, 1);

            // Atualiza a visualização
            showFilesList();
        };

        // Change event do input file
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                // Adiciona novos arquivos aos já selecionados
                var newFiles = Array.from(this.files);
                selectedFiles = selectedFiles.concat(newFiles);

                // Reseta o input para permitir selecionar o mesmo arquivo novamente
                this.value = '';

                // Atualiza a visualização
                showFilesList();
            }
        });

        // Drag and Drop events
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!isUploading) {
                dropzone.classList.add('border', 'border-primary', 'border-3');
                dropzone.style.backgroundColor = 'rgba(0, 123, 255, 0.08)';
            }
        });

        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Só remove se realmente saiu da dropzone
            if (e.target === dropzone || !dropzone.contains(e.relatedTarget)) {
                dropzone.classList.remove('border', 'border-primary', 'border-3');
                dropzone.style.backgroundColor = '';
            }
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('border', 'border-primary', 'border-3');
            dropzone.style.backgroundColor = '';

            if (!isUploading) {
                var files = e.dataTransfer.files;
                if (files.length > 0) {
                    // Adiciona novos arquivos aos já selecionados
                    var newFiles = Array.from(files);
                    selectedFiles = selectedFiles.concat(newFiles);

                    // Atualiza a visualização
                    showFilesList();
                }
            }
        });

        // Upload sequencial de arquivos
        window.uploadFilesSequentially = async function() {
            if (selectedFiles.length === 0) {
                toastr.error('Selecione pelo menos um arquivo');
                return;
            }

            isUploading = true;
            var modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_add_file'));
            var submitButton = document.querySelector('#kt_modal_add_file_form button[type="submit"]');
            var cancelButton = document.querySelector('#kt_modal_add_file_form button[data-bs-dismiss="modal"]');

            // Desabilita botões
            submitButton.disabled = true;
            cancelButton.disabled = true;
            dropzone.style.pointerEvents = 'none';
            dropzone.style.opacity = '0.6';

            var successCount = 0;
            var errorCount = 0;

            for (let i = 0; i < selectedFiles.length; i++) {
                var file = selectedFiles[i];
                var fileItem = document.getElementById(`file_item_${i}`);
                var statusBadge = fileItem.querySelector('.file-status');

                // Atualiza status para "Enviando"
                statusBadge.innerHTML = '<span class="badge badge-light-primary"><span class="spinner-border spinner-border-sm me-1"></span>Enviando...</span>';

                try {
                    // Cria FormData para este arquivo
                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('status', document.getElementById('file_status_hidden').value);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    // Faz o upload
                    var response = await fetch('{{ url("/" . $moduleSlug . "/" . $recordId . "/files") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    var data = await response.json();

                    if (response.ok && data.success) {
                        // Sucesso
                        statusBadge.innerHTML = '<span class="badge badge-light-success"><i class="ki-outline ki-check fs-2"></i></span>';
                        successCount++;

                        // Adiciona o arquivo na grid (função definida no show.blade.php)
                        if (typeof addFileToGrid === 'function') {
                            addFileToGrid(data.file);
                            updateFilesCount(1);
                        }
                    } else {
                        // Erro
                        statusBadge.innerHTML = '<span class="badge badge-light-danger" title="' + (data.message || 'Erro no upload') + '">Erro</span>';
                        errorCount++;
                    }
                } catch (error) {
                    console.error('Erro no upload:', error);
                    statusBadge.innerHTML = '<span class="badge badge-light-danger" title="' + error.message + '">Erro</span>';
                    errorCount++;
                }

                // Pequeno delay entre uploads
                await new Promise(resolve => setTimeout(resolve, 300));
            }

            // Mostra resultado final
            if (errorCount === 0) {
                toastr.success(`${successCount} arquivo(s) enviado(s) com sucesso!`);
                // Fecha o modal após 1 segundo
                setTimeout(() => {
                    modal.hide();
                    resetModal();
                }, 1000);
            } else if (successCount > 0) {
                toastr.warning(`${successCount} enviado(s), ${errorCount} com erro`);
            } else {
                toastr.error('Erro ao enviar arquivos');
            }

            // Reabilita botões
            isUploading = false;
            submitButton.disabled = false;
            cancelButton.disabled = false;
            dropzone.style.pointerEvents = '';
            dropzone.style.opacity = '';
        };

        // Reseta o modal
        function resetModal() {
            selectedFiles = [];
            fileInput.value = '';
            filesQueue.innerHTML = '';
            filesListContainer.style.display = 'none';
            dropzone.querySelector('.dz-message').innerHTML = `
                <i class="ki-outline ki-file-up fs-3x text-primary"></i>
                <div class="ms-4">
                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arraste os arquivos aqui ou clique para selecionar</h3>
                    <span class="fs-7 fw-semibold text-gray-500">Tamanho máximo: 10MB por arquivo</span>
                </div>
            `;
        }

        // Reseta quando o modal fecha
        document.getElementById('kt_modal_add_file').addEventListener('hidden.bs.modal', resetModal);

        // ========================================
        // Recebe arquivos do drag and drop da página principal
        // ========================================
        document.addEventListener('filesDropped', function(e) {
            if (e.detail && e.detail.files) {
                // Adiciona os arquivos ao array
                var droppedFiles = e.detail.files;
                selectedFiles = selectedFiles.concat(droppedFiles);

                // Atualiza a visualização
                showFilesList();
            }
        });
    }
});
</script>
@endpush
@endonce

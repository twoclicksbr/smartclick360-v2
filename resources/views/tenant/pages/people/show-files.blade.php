@extends('tenant.layouts.app')

@php
    $breadcrumbs = [
        ['label' => $tenant->name, 'url' => url('/dashboard/main')],
        ['label' => 'Pessoas', 'url' => url('/people')],
        ['label' => $person->first_name . ' ' . $person->surname, 'url' => url('/people/' . $person->id)],
        ['label' => 'Arquivos', 'url' => null],
    ];
    $pageTitle = $person->first_name . ' ' . $person->surname;
    $pageDescription = 'Arquivos';
@endphp

@section('title', $person->first_name . ' ' . $person->surname . ' - Arquivos - ' . $tenant->name)

@section('content')
    @include('tenant.pages.people._navbar', ['person' => $person, 'activeTab' => 'files'])

    <!--begin::Arquivos-->
    <div class="card" id="files_card">
        <!--begin::Header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Arquivos</span>
                <span class="text-muted mt-1 fw-semibold fs-7"
                    id="files_subtitle">{{ $person->files->count() }}
                    {{ $person->files->count() == 1 ? 'arquivo' : 'arquivos' }}
                    cadastrado{{ $person->files->count() == 1 ? '' : 's' }}</span>
            </h3>
            <div class="card-toolbar">
                <button type="button" onclick="addFile()" class="btn btn-sm btn-light-primary">
                    <i class="ki-outline ki-plus fs-3"></i>
                    Adicionar
                </button>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body py-3">
            @if ($person->files->count() > 0)
                <!--begin::Row-->
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-6 g-xl-9" id="files_list">
                    @php
                        // Helper para mapear extensão → ícone/cor
                        $fileIconMap = [
                            'pdf' => ['icon' => 'document', 'color' => 'danger'],
                            'doc' => ['icon' => 'document', 'color' => 'primary'],
                            'docx' => ['icon' => 'document', 'color' => 'primary'],
                            'xls' => ['icon' => 'file-sheet', 'color' => 'success'],
                            'xlsx' => ['icon' => 'file-sheet', 'color' => 'success'],
                            'png' => ['icon' => 'picture', 'color' => 'warning'],
                            'jpg' => ['icon' => 'picture', 'color' => 'warning'],
                            'jpeg' => ['icon' => 'picture', 'color' => 'warning'],
                            'gif' => ['icon' => 'picture', 'color' => 'warning'],
                            'txt' => ['icon' => 'file', 'color' => 'secondary'],
                            'zip' => ['icon' => 'folder-down', 'color' => 'info'],
                            'rar' => ['icon' => 'folder-down', 'color' => 'info'],
                        ];
                    @endphp
                    @foreach ($person->files as $file)
                        @php
                            // Detecta extensão pelo path (não pelo name, pois pode ser "avatar" sem extensão)
                            $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
                            $fileInfo = $fileIconMap[$extension] ?? ['icon' => 'file', 'color' => 'secondary'];

                            // Verifica se é uma imagem pela extensão OU pelo mime_type
                            $isImage = in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']) ||
                                       str_starts_with($file->mime_type ?? '', 'image/');

                            // Formata tamanho do arquivo
                            $size = $file->size;
                            if ($size < 1024) {
                                $formattedSize = $size . ' B';
                            } elseif ($size < 1048576) {
                                $formattedSize = number_format($size / 1024, 2) . ' KB';
                            } else {
                                $formattedSize = number_format($size / 1048576, 2) . ' MB';
                            }

                            // Trunca nome do arquivo se muito longo
                            $displayName = strlen($file->name) > 20 ? substr($file->name, 0, 20) . '..' : $file->name;
                        @endphp
                        <!--begin::Col-->
                        <div class="col" data-file-id="{{ $file->id }}">
                            <!--begin::Card-->
                            <div class="card" style="min-height: 300px;">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <div class="text-gray-800 d-flex flex-column">
                                        <!--begin::Icon/Image-->
                                        <div class="symbol symbol-100px mb-5 mx-auto">
                                            @if($isImage)
                                                <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->name }}" class="symbol-label rounded" style="object-fit: cover;" />
                                            @else
                                                <span class="symbol-label bg-light-{{ $fileInfo['color'] }}">
                                                    <i class="ki-outline ki-{{ $fileInfo['icon'] }} fs-2x text-{{ $fileInfo['color'] }}"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <!--end::Icon/Image-->
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-bold mb-2" data-bs-toggle="tooltip" title="{{ $file->name }}">
                                            {{ $displayName }}
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Name-->
                                    <!--begin::Description-->
                                    <div class="fs-7 fw-semibold text-gray-500 mb-3">
                                        {{ $formattedSize }} • {{ $file->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <!--end::Description-->
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($isImage)
                                            <a href="#" onclick="event.preventDefault(); previewImage('{{ asset('storage/' . $file->path) }}', '{{ $file->name }}');"
                                                class="btn btn-sm btn-icon btn-light-info" data-bs-toggle="tooltip"
                                                title="Visualizar">
                                                <i class="ki-outline ki-eye fs-4"></i>
                                            </a>
                                        @endif
                                        <a href="{{ asset('storage/' . $file->path) }}" download
                                            class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="tooltip"
                                            title="Download">
                                            <i class="ki-outline ki-cloud-download fs-4"></i>
                                        </a>
                                        <a href="#" onclick="event.preventDefault(); deleteFile({{ $file->id }});"
                                            class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip"
                                            title="Excluir">
                                            <i class="ki-outline ki-trash fs-4"></i>
                                        </a>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                    @endforeach
                </div>
                <!--end::Row-->
            @else
                <div class="text-center text-muted py-10" id="files_empty_state">
                    <i class="ki-outline ki-file fs-3x text-gray-400 mb-3"></i>
                    <div class="fw-semibold">Nenhum arquivo cadastrado</div>
                </div>
            @endif
        </div>
        <!--end::Body-->
    </div>
    <!--end::Arquivos-->

    {{-- Modal - Adicionar/Editar Pessoa --}}
    @include('tenant.layouts.modals.modal-module', ['module' => 'people', 'modalSize' => 'mw-800px'])

    {{-- Modal - Adicionar/Editar Arquivo --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'file',
        'moduleSlug' => 'people',
        'recordId' => $person->id,
        'modalSize' => 'mw-650px',
    ])

    {{-- Modal - Visualizar Imagem --}}
    <div class="modal fade" id="image_preview_modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered mw-900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="preview_title">Imagem</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="preview_image" src="" alt="Preview" class="img-fluid" style="max-height: 70vh; display: none;" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ========================================
        // Modal - Editar Pessoa
        // ========================================
        function editPerson(id, firstName, surname, birthDate, avatarUrl, status) {
            var personModal = document.getElementById('kt_modal_add_person');
            var personForm = document.getElementById('kt_modal_add_person_form');
            var modalTitle = document.getElementById('modal_person_title');

            // Configura modo edição
            modalTitle.textContent = 'Editar Pessoa';
            document.getElementById('person_form_method').value = 'PUT';
            document.getElementById('person_id').value = id;
            personForm.action = "{{ url('/people') }}/" + id;

            // Preenche os campos
            document.getElementById('person_first_name').value = firstName;
            document.getElementById('person_surname').value = surname;
            document.getElementById('person_birth_date').value = birthDate || '';

            // Atualiza o preview do avatar se houver
            if (avatarUrl) {
                var imageInputWrapper = personForm.querySelector('.image-input-wrapper');
                if (imageInputWrapper) {
                    imageInputWrapper.style.backgroundImage = `url('${avatarUrl}')`;
                }
            } else {
                // Se não houver avatar, usa a imagem padrão
                var imageInputWrapper = personForm.querySelector('.image-input-wrapper');
                if (imageInputWrapper) {
                    imageInputWrapper.style.backgroundImage = "url('/assets/media/avatars/blank.png')";
                }
            }

            // Define o status baseado no valor real da pessoa
            document.getElementById('person_status_switch').checked = status;
            document.getElementById('person_status_hidden').value = status ? '1' : '0';

            // Abre o modal (reutiliza instância se existir)
            var modal = bootstrap.Modal.getOrCreateInstance(personModal);
            modal.show();
        }

        // ========================================
        // Modal - Adicionar Arquivo
        // ========================================
        function addFile() {
            var fileModal = document.getElementById('kt_modal_add_file');
            var fileForm = document.getElementById('kt_modal_add_file_form');
            var modalTitle = document.getElementById('modal_file_title');

            // Reseta o formulário
            fileForm.reset();
            modalTitle.textContent = 'Adicionar Arquivo';
            document.getElementById('file_form_method').value = 'POST';
            document.getElementById('file_id').value = '';
            fileForm.action = "{{ url('/people/' . $person->id . '/files') }}";

            // Reseta o status para ativo
            document.getElementById('file_status_switch').checked = true;
            document.getElementById('file_status_hidden').value = '1';

            // Abre o modal (reutiliza instância se existir)
            var modal = bootstrap.Modal.getOrCreateInstance(fileModal);
            modal.show();
        }

        // ========================================
        // Deletar Arquivo
        // ========================================
        function deleteFile(fileId) {
            if (!confirm('Tem certeza que deseja excluir este arquivo?')) {
                return;
            }

            fetch(`/people/{{ $person->id }}/files/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove o arquivo da grid
                        var fileCard = document.querySelector(`[data-file-id="${fileId}"]`);
                        if (fileCard) {
                            fileCard.remove();
                        }

                        // Atualiza o contador
                        updateFilesCount(-1);

                        toastr.success('Arquivo excluído com sucesso!');
                    } else {
                        toastr.error(data.message || 'Erro ao excluir arquivo');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    toastr.error('Erro ao excluir arquivo');
                });
        }

        // ========================================
        // Atualizar contador de arquivos
        // ========================================
        function updateFilesCount(delta) {
            var counter = document.getElementById('files_counter');
            var subtitle = document.getElementById('files_subtitle');
            var currentCount = parseInt(counter.textContent);
            var newCount = currentCount + delta;

            counter.textContent = newCount;

            var fileWord = newCount === 1 ? 'arquivo' : 'arquivos';
            var registerWord = newCount === 1 ? 'cadastrado' : 'cadastrados';
            subtitle.textContent = `${newCount} ${fileWord} ${registerWord}`;

            // Se não há mais arquivos, mostra o empty state
            if (newCount === 0) {
                var filesList = document.getElementById('files_list');
                if (filesList) {
                    filesList.remove();
                }

                var filesBody = document.querySelector('#files .card-body');
                if (filesBody && !document.getElementById('files_empty_state')) {
                    filesBody.innerHTML += `
                        <div class="text-center text-muted py-10" id="files_empty_state">
                            <i class="ki-outline ki-file fs-3x text-gray-400 mb-3"></i>
                            <div class="fw-semibold">Nenhum arquivo cadastrado</div>
                        </div>
                    `;
                }
            }
        }

        // ========================================
        // Adicionar arquivo na grid
        // ========================================
        function addFileToGrid(file) {
            // Remove empty state se existir
            var emptyState = document.getElementById('files_empty_state');
            if (emptyState) {
                emptyState.remove();
            }

            // Cria a grid se não existir
            var filesList = document.getElementById('files_list');
            if (!filesList) {
                var filesBody = document.querySelector('#files .card-body');
                filesBody.innerHTML = '<div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-6 g-xl-9" id="files_list"></div>';
                filesList = document.getElementById('files_list');
            }

            // Detecta tipo de arquivo
            var extension = file.path.split('.').pop().toLowerCase();
            var fileIconMap = {
                'pdf': {icon: 'document', color: 'danger'},
                'doc': {icon: 'document', color: 'primary'},
                'docx': {icon: 'document', color: 'primary'},
                'xls': {icon: 'file-sheet', color: 'success'},
                'xlsx': {icon: 'file-sheet', color: 'success'},
                'png': {icon: 'picture', color: 'warning'},
                'jpg': {icon: 'picture', color: 'warning'},
                'jpeg': {icon: 'picture', color: 'warning'},
                'gif': {icon: 'picture', color: 'warning'},
                'txt': {icon: 'file', color: 'secondary'},
                'zip': {icon: 'folder-down', color: 'info'},
                'rar': {icon: 'folder-down', color: 'info'}
            };

            var fileInfo = fileIconMap[extension] || {icon: 'file', color: 'secondary'};
            var isImage = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'].includes(extension);

            // Formata tamanho
            var size = file.size;
            var formattedSize;
            if (size < 1024) {
                formattedSize = size + ' B';
            } else if (size < 1048576) {
                formattedSize = (size / 1024).toFixed(2) + ' KB';
            } else {
                formattedSize = (size / 1048576).toFixed(2) + ' MB';
            }

            var displayName = file.name.length > 20 ? file.name.substring(0, 20) + '..' : file.name;

            // Cria o HTML do card
            var cardHtml = `
                <div class="col" data-file-id="${file.id}">
                    <div class="card" style="min-height: 300px;">
                        <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                            <div class="text-gray-800 d-flex flex-column">
                                <div class="symbol symbol-100px mb-5 mx-auto">
                                    ${isImage
                                        ? `<img src="/storage/${file.path}" alt="${file.name}" class="symbol-label rounded" style="object-fit: cover;" />`
                                        : `<span class="symbol-label bg-light-${fileInfo.color}">
                                            <i class="ki-outline ki-${fileInfo.icon} fs-2x text-${fileInfo.color}}"></i>
                                           </span>`
                                    }
                                </div>
                                <div class="fs-5 fw-bold mb-2" data-bs-toggle="tooltip" title="${file.name}">
                                    ${displayName}
                                </div>
                            </div>
                            <div class="fs-7 fw-semibold text-gray-500 mb-3">
                                ${formattedSize} • ${file.created_at}
                            </div>
                            <div class="d-flex justify-content-center gap-2">
                                ${isImage
                                    ? `<a href="#" onclick="event.preventDefault(); previewImage('/storage/${file.path}', '${file.name}');" class="btn btn-sm btn-icon btn-light-info" data-bs-toggle="tooltip" title="Visualizar">
                                        <i class="ki-outline ki-eye fs-4"></i>
                                       </a>`
                                    : ''
                                }
                                <a href="/storage/${file.path}" download class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="tooltip" title="Download">
                                    <i class="ki-outline ki-cloud-download fs-4"></i>
                                </a>
                                <a href="#" onclick="event.preventDefault(); deleteFile(${file.id});" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="tooltip" title="Excluir">
                                    <i class="ki-outline ki-trash fs-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Adiciona à grid
            filesList.insertAdjacentHTML('beforeend', cardHtml);
        }

        // ========================================
        // Visualizar Imagem
        // ========================================
        function previewImage(imageSrc, imageName) {
            var modal = document.getElementById('image_preview_modal');
            var modalImage = document.getElementById('preview_image');
            var modalTitle = document.getElementById('preview_title');

            modalImage.src = imageSrc;
            modalImage.style.display = 'block';
            modalTitle.textContent = imageName;

            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        // ========================================
        // Drag and Drop na página principal
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            var filesCard = document.getElementById('files_card');
            var isDragging = false;

            // Previne comportamento padrão em toda a página
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                filesCard.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Visual feedback quando arrasta sobre o card
            filesCard.addEventListener('dragenter', function(e) {
                isDragging = true;
                filesCard.classList.add('border', 'border-primary', 'border-3');
                filesCard.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
            });

            filesCard.addEventListener('dragleave', function(e) {
                // Só remove o highlight se realmente saiu do card
                if (e.target === filesCard) {
                    isDragging = false;
                    filesCard.classList.remove('border', 'border-primary', 'border-3');
                    filesCard.style.backgroundColor = '';
                }
            });

            // Quando soltar o arquivo
            filesCard.addEventListener('drop', function(e) {
                isDragging = false;
                filesCard.classList.remove('border', 'border-primary', 'border-3');
                filesCard.style.backgroundColor = '';

                var files = e.dataTransfer.files;
                if (files.length > 0) {
                    // Abre o modal
                    var fileModal = document.getElementById('kt_modal_add_file');
                    var modal = bootstrap.Modal.getOrCreateInstance(fileModal);
                    modal.show();

                    // Aguarda o modal abrir e então adiciona os arquivos
                    setTimeout(function() {
                        // Dispara evento customizado para o modal processar os arquivos
                        var event = new CustomEvent('filesDropped', {
                            detail: { files: Array.from(files) }
                        });
                        document.dispatchEvent(event);
                    }, 300);
                }
            });
        });
    </script>
@endpush

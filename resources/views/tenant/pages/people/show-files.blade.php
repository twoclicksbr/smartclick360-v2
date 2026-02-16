@extends('tenant.layouts.app')

@section('title', 'Carregando... - ' . $tenant->name)

@section('content')
    <!--begin::Loading skeleton-->
    <div id="person-loading" class="text-center py-20">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="text-gray-600 mt-3">Carregando detalhes...</p>
    </div>
    <!--end::Loading skeleton-->

    <!--begin::Content (hidden until loaded)-->
    <div id="person-content" style="display: none;">
        @include('tenant.pages.people._navbar', ['code' => $code, 'activeTab' => 'files'])

        <!--begin::Arquivos-->
        <div class="card" id="files_card">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Arquivos</span>
                    <span class="text-muted mt-1 fw-semibold fs-7" id="files_subtitle">
                        Carregando...
                    </span>
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
                <!--begin::Row (populated via JavaScript from API)-->
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-6 g-xl-9" id="files_list">
                    <!-- Populated via JavaScript from API -->
                </div>
                <!--end::Row-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Arquivos-->
    </div>
    <!--end::Content-->

    {{-- Modal - Adicionar/Editar Pessoa --}}
    @include('tenant.layouts.modals.modal-module', ['module' => 'people', 'modalSize' => 'mw-800px'])

    {{-- Modal - Adicionar/Editar Arquivo --}}
    @include('tenant.layouts.modals.modal-submodule', [
        'submodule' => 'file',
        'moduleSlug' => 'people',
        'recordId' => $code,
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
        // Store person code globally for use in functions
        window.personCode = '{{ $code }}';

        // ========================================
        // Fetch Person Data from API
        // ========================================
        function fetchPersonData() {
            console.log('Fetching person from:', '/api/v1/people/' + window.personCode);
            fetch(`/api/v1/people/${window.personCode}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('API Response status:', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error('Erro ao carregar dados');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response data:', data);
                    if (data.success && data.data) {
                        // API returns {person: {...}} structure
                        const person = data.data.person || data.data;
                        console.log('Person data:', person);
                        populateFilesPage(person);

                        // Hide loading, show content
                        document.getElementById('person-loading').style.display = 'none';
                        document.getElementById('person-content').style.display = 'block';
                        console.log('✓ Dados da pessoa carregados via API');
                    } else {
                        console.error('✗ Erro ao carregar pessoa:', data.message);
                        throw new Error(data.message || 'Erro ao carregar dados');
                    }
                })
                .catch(error => {
                    console.error('✗ Erro ao buscar pessoa da API:', error);
                    document.getElementById('person-loading').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ki-outline ki-information-5 fs-2x me-2"></i>
                            Erro ao carregar dados da pessoa. Por favor, tente novamente.
                        </div>
                    `;
                });
        }

        // ========================================
        // Populate Files Page
        // ========================================
        function populateFilesPage(person) {
            if (!person) return;

            // Update page title
            const firstName = person.first_name || '';
            const surname = person.surname || '';
            document.title = `${firstName} ${surname} - Arquivos - {{ $tenant->name }}`.trim();

            // Populate navbar
            populateNavbar(person);

            // Define variável global para controlar se a pessoa está deletada
            window.isPersonDeleted = !!person.deleted_at;

            // Esconde botão Excluir se a pessoa é o usuário logado
            if (window.authUser && window.authUser.person_id === person.id) {
                const deleteOption = document.getElementById('navbar-delete-option');
                if (deleteOption) deleteOption.style.display = 'none';
            }

            // Se a pessoa está excluída (soft-deleted), mostra botão Restaurar e esconde Editar
            if (person.deleted_at) {
                const editBtn = document.getElementById('navbar-edit-btn');
                if (editBtn) editBtn.style.display = 'none';
                const restoreOption = document.getElementById('navbar-restore-option');
                if (restoreOption) restoreOption.style.display = 'inline-block';
                // Esconde o dropdown (X) também
                const deleteOption = document.getElementById('navbar-delete-option');
                if (deleteOption) deleteOption.style.display = 'none';

                // Esconde TODOS os botões "+ Adicionar" dos submódulos
                document.querySelectorAll('button').forEach(btn => {
                    if (btn.textContent.trim().includes('Adicionar')) {
                        btn.style.display = 'none';
                    }
                });
            }

            // Atualiza breadcrumb — substitui o code pelo nome
            document.querySelectorAll('.breadcrumb-segment').forEach(el => {
                if (el.dataset.segment === window.personCode) {
                    el.textContent = person.first_name + ' ' + person.surname;
                }
            });

            // Populate files list
            populateFilesList(person.files || []);
        }

        // ========================================
        // Populate Navbar
        // ========================================
        function populateNavbar(person) {
            if (!person) return;

            const fullName = `${person.first_name || ''} ${person.surname || ''}`.trim();

            // Nome completo
            const navFullName = document.getElementById('navbar-full-name');
            if (navFullName) navFullName.textContent = fullName || 'Nome não disponível';

            // Avatar
            const avatar = person.files?.find(f => f.name === 'avatar');
            const navAvatarImg = document.getElementById('navbar-avatar-img');
            const navAvatarInitials = document.getElementById('navbar-avatar-initials');
            if (avatar && navAvatarImg && navAvatarInitials) {
                navAvatarImg.src = '/storage/' + avatar.path;
                navAvatarImg.style.display = 'block';
                navAvatarInitials.style.display = 'none';
            } else if (navAvatarInitials && person.first_name && person.surname) {
                const initials = person.first_name.charAt(0).toUpperCase() + person.surname.charAt(0).toUpperCase();
                navAvatarInitials.textContent = initials;
            }

            // Status icon
            const navStatusIcon = document.getElementById('navbar-status-icon');
            if (navStatusIcon) {
                if (person.status == 1 || person.status === true || person.status === 'active') {
                    navStatusIcon.className = 'ki-duotone ki-check-circle fs-2 text-success me-2';
                } else {
                    navStatusIcon.className = 'ki-duotone ki-cross-circle fs-2 text-danger me-2';
                }
            }

            // Data de nascimento
            if (person.birth_date) {
                const navBirthDateContainer = document.getElementById('navbar-birth-date-container');
                const navBirthDate = document.getElementById('navbar-birth-date');
                if (navBirthDateContainer && navBirthDate) {
                    const birthDate = new Date(person.birth_date);
                    const age = calculateAge(person.birth_date);
                    navBirthDate.textContent = birthDate.toLocaleDateString('pt-BR') + ' (' + age + ' anos)';
                    navBirthDateContainer.style.display = 'flex';
                }
            }

            // WhatsApp
            const whatsapp = person.contacts?.find(c => c.type_contact?.name === 'WhatsApp');
            if (whatsapp) {
                const navWhatsappContainer = document.getElementById('navbar-whatsapp-container');
                const navWhatsapp = document.getElementById('navbar-whatsapp');
                if (navWhatsappContainer && navWhatsapp) {
                    navWhatsappContainer.href = 'https://wa.me/55' + whatsapp.value;
                    navWhatsapp.textContent = formatPhone(whatsapp.value);
                    navWhatsappContainer.style.display = 'flex';
                }
            }

            // Email
            const email = person.contacts?.find(c => c.type_contact?.name === 'Email');
            if (email) {
                const navEmailContainer = document.getElementById('navbar-email-container');
                const navEmail = document.getElementById('navbar-email');
                if (navEmailContainer && navEmail) {
                    navEmailContainer.href = 'mailto:' + email.value;
                    navEmail.textContent = email.value;
                    navEmailContainer.style.display = 'flex';
                }
            }

            // Contadores do navbar
            const navContactsCount = document.getElementById('navbar-contacts-count');
            const navDocumentsCount = document.getElementById('navbar-documents-count');
            const navAddressesCount = document.getElementById('navbar-addresses-count');
            const navFilesCount = document.getElementById('navbar-files-count');
            const navNotesCount = document.getElementById('navbar-notes-count');
            const navPurchasesCount = document.getElementById('navbar-purchases-count');
            const navSalesCount = document.getElementById('navbar-sales-count');
            const navPayablesCount = document.getElementById('navbar-payables-count');
            const navReceivablesCount = document.getElementById('navbar-receivables-count');

            if (navContactsCount) navContactsCount.textContent = person.contacts?.length || 0;
            if (navDocumentsCount) navDocumentsCount.textContent = person.documents?.length || 0;
            if (navAddressesCount) navAddressesCount.textContent = person.addresses?.length || 0;
            if (navFilesCount) navFilesCount.textContent = person.files?.length || 0;
            if (navNotesCount) navNotesCount.textContent = person.notes?.length || 0;
            if (navPurchasesCount) navPurchasesCount.textContent = 0;
            if (navSalesCount) navSalesCount.textContent = 0;
            if (navPayablesCount) navPayablesCount.textContent = 0;
            if (navReceivablesCount) navReceivablesCount.textContent = 0;

            // Datas
            const navCreatedAt = document.getElementById('navbar-created-at');
            const navUpdatedAt = document.getElementById('navbar-updated-at');
            if (navCreatedAt && person.created_at) {
                const createdDate = new Date(person.created_at);
                navCreatedAt.textContent = createdDate.toLocaleString('pt-BR');
            }
            if (navUpdatedAt && person.updated_at) {
                const updatedDate = new Date(person.updated_at);
                navUpdatedAt.textContent = updatedDate.toLocaleString('pt-BR');
            }

            // Botão editar - configura onclick
            const navEditBtn = document.getElementById('navbar-edit-btn');
            if (navEditBtn) {
                const avatarUrl = avatar ? '/storage/' + avatar.path : '';
                const birthDateFormatted = person.birth_date ? person.birth_date.substring(0, 10) : '';
                navEditBtn.onclick = function() {
                    editPerson(person.id, person.first_name, person.surname, birthDateFormatted, avatarUrl, person.status);
                };
            }
        }

        // ========================================
        // Calculate Age
        // ========================================
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            return age;
        }

        // ========================================
        // Format Phone
        // ========================================
        function formatPhone(phone) {
            if (!phone) return '';
            phone = phone.replace(/\D/g, '');
            if (phone.length === 11) {
                return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (phone.length === 10) {
                return phone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            }
            return phone;
        }

        // ========================================
        // Populate Files List
        // ========================================
        function populateFilesList(files) {
            const filesList = document.getElementById('files_list');
            const subtitle = document.getElementById('files_subtitle');

            // Update subtitle
            const count = files.length;
            const fileWord = count === 1 ? 'arquivo' : 'arquivos';
            const registerWord = count === 1 ? 'cadastrado' : 'cadastrados';
            subtitle.textContent = `${count} ${fileWord} ${registerWord}`;

            // Clear list
            filesList.innerHTML = '';

            // If no files, show empty state
            if (files.length === 0) {
                filesList.innerHTML = `
                    <div class="w-100 text-center text-muted py-10">
                        <i class="ki-outline ki-file fs-3x text-gray-400 d-block mx-auto mb-3"></i>
                        <div class="fw-semibold">Nenhum arquivo cadastrado</div>
                    </div>
                `;
                return;
            }

            // Populate files
            files.forEach(file => {
                addFileToGrid(file);
            });
        }

        // ========================================
        // Modal - Editar Pessoa
        // ========================================
        function editPerson(id, firstName, surname, birthDate, avatarUrl, status) {
            var personModal = document.getElementById('kt_modal_add_person');
            var personForm = document.getElementById('kt_modal_add_person_form');
            var modalTitle = document.getElementById('modal_person_title');

            // Função helper para encode de ID
            function encodeId(id) {
                return btoa(String(id)).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            }

            // Configura modo edição
            modalTitle.textContent = 'Editar Pessoa';
            document.getElementById('person_form_method').value = 'PUT';
            document.getElementById('person_id').value = id;
            personForm.action = "{{ url('/people') }}/" + encodeId(id);

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
            fileForm.action = `/people/${window.personCode}/files`;

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

            fetch(`/people/${window.personCode}/files/${fileId}`, {
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
            var subtitle = document.getElementById('files_subtitle');
            var filesList = document.getElementById('files_list');

            // Get current count from subtitle text
            var currentText = subtitle.textContent;
            var currentCount = parseInt(currentText.match(/\d+/)) || 0;
            var newCount = currentCount + delta;

            var fileWord = newCount === 1 ? 'arquivo' : 'arquivos';
            var registerWord = newCount === 1 ? 'cadastrado' : 'cadastrados';
            subtitle.textContent = `${newCount} ${fileWord} ${registerWord}`;

            // Se não há mais arquivos, mostra o empty state
            if (newCount === 0) {
                filesList.innerHTML = `
                    <div class="w-100 text-center text-muted py-10">
                        <i class="ki-outline ki-file fs-3x text-gray-400 d-block mx-auto mb-3"></i>
                        <div class="fw-semibold">Nenhum arquivo cadastrado</div>
                    </div>
                `;
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

            // Formata a data
            var createdAt = '';
            if (file.created_at) {
                try {
                    const date = new Date(file.created_at);
                    if (!isNaN(date.getTime())) {
                        createdAt = date.toLocaleString('pt-BR', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                } catch (e) {
                    createdAt = '';
                }
            }

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
                                ${formattedSize}${createdAt ? ' • ' + createdAt : ''}
                            </div>
                            <div class="d-flex justify-content-center gap-2">
                                ${!window.isPersonDeleted
                                    ? `${isImage
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
                                    </a>`
                                    : `<a href="/storage/${file.path}" download class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="tooltip" title="Download">
                                        <i class="ki-outline ki-cloud-download fs-4"></i>
                                       </a>`
                                }
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
            // Fetch person data from API
            fetchPersonData();

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

        // ========================================
        // Deletar Pessoa
        // ========================================
        function deletePerson() {
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta pessoa será excluída!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/people/' + window.personCode, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success || data.status === 'success') {
                            Swal.fire('Excluído!', 'Pessoa excluída com sucesso.', 'success')
                                .then(() => window.location.href = '/people');
                        } else {
                            Swal.fire('Erro!', data.message || 'Erro ao excluir.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        Swal.fire('Erro!', 'Erro ao excluir pessoa.', 'error');
                    });
                }
            });
        }

        // ========================================
        // Restaurar Pessoa
        // ========================================
        function restorePerson() {
            Swal.fire({
                title: 'Restaurar pessoa?',
                text: 'Esta pessoa será restaurada.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f6c000',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, restaurar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/people/' + window.personCode + '/restore', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success || data.status === 'success') {
                            Swal.fire('Restaurado!', 'Pessoa restaurada com sucesso.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Erro!', data.message || 'Erro ao restaurar.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Erro!', 'Erro ao restaurar pessoa.', 'error');
                    });
                }
            });
        }
    </script>
@endpush

{{-- Modal Criar Módulo --}}
<div class="modal fade" id="modal_dynamic_create" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ url('/modules') }}" id="form_create_module">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title">Crie um novo módulo personalizado</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Nome --}}
                        <div class="col-md-4 mb-7">
                            <label class="fs-6 fw-semibold mb-2 required">Nome</label>
                            <input type="text" class="form-control form-control-solid" name="name" id="modal_name" required>
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2 required">Slug</label>
                            <input type="text" class="form-control form-control-solid" name="slug" id="modal_slug" required>
                        </div>

                        {{-- Tipo --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2 required">Tipo</label>
                            <select class="form-select form-select-solid" name="type" required>
                                <option value="">Selecione...</option>
                                <option value="module">Módulo</option>
                                <option value="submodule">Submódulo</option>
                                <option value="pivot">Pivot</option>
                            </select>
                        </div>

                        {{-- Escopo --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2 required">Escopo</label>
                            <select class="form-select form-select-solid" name="scope" required>
                                <option value="">Selecione...</option>
                                <option value="landlord">SmartClick360°</option>
                                <option value="tenant">Clientes</option>
                            </select>
                        </div>

                        {{-- Ícone --}}
                        <div class="col-md-2 mb-7">
                            <label class="fs-6 fw-semibold mb-2">Ícone</label>
                            <x-icon-picker name="icon" value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    {{-- Status à esquerda --}}
                    <div class="d-flex align-items-center gap-3">
                        <label class="fs-6 fw-semibold">Status</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input switch-badge" type="checkbox" name="status" value="1" checked>
                        </div>
                        <span class="badge badge-light-primary">Sim</span>
                    </div>
                    {{-- Botões à direita --}}
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-outline ki-check fs-4"></i> Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript do modal --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-geração de slug
    const modalName = document.getElementById('modal_name');
    const modalSlug = document.getElementById('modal_slug');
    let modalSlugManual = false;

    if (modalName && modalSlug) {
        modalName.addEventListener('input', function() {
            if (!modalSlugManual) {
                modalSlug.value = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            }
        });
        modalSlug.addEventListener('input', function() {
            modalSlugManual = this.value !== '';
        });
    }

    // Switch badge dinâmico no modal
    document.querySelectorAll('#modal_dynamic_create .switch-badge').forEach(function(input) {
        input.addEventListener('change', function() {
            const badge = this.closest('.d-flex').parentElement.querySelector('.badge');
            if (badge) {
                if (this.checked) {
                    badge.className = 'badge badge-light-primary';
                    badge.textContent = 'Sim';
                } else {
                    badge.className = 'badge badge-light-danger';
                    badge.textContent = 'Não';
                }
            }
        });
    });

    // Botão editar da listagem: redireciona para página de detalhes
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const code = this.dataset.code;
            window.location.href = `/modules/${code}`;
        });
    });
});
</script>

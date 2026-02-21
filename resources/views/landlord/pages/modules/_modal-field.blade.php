{{-- Modal Create/Edit Field --}}
<div class="modal fade" id="modal_field" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-4">
                <h3 class="modal-title" id="modal_field_title">Novo Campo</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form id="form_field" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="field_method" value="POST">
                    <input type="hidden" name="field_id" id="field_id" value="">

                    {{-- Linha 1: Identificação --}}
                    <div class="row mb-5">
                        <div class="col-md-3">
                            <label class="fs-6 fw-semibold mb-2 required">Nome (DB)</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="name" id="field_name" placeholder="ex: first_name" required>
                            <div class="form-text text-muted">Nome da coluna no banco</div>
                        </div>
                        <div class="col-md-3">
                            <label class="fs-6 fw-semibold mb-2 required">Label</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="label" id="field_label" placeholder="ex: Nome" required>
                            <div class="form-text text-muted">Texto exibido ao usuário</div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2 required">Tipo DB</label>
                            <select class="form-select form-select-solid form-select-sm" name="type" id="field_type" required>
                                <option value="">Selecione...</option>
                                <option value="string">string</option>
                                <option value="text">text</option>
                                <option value="integer">integer</option>
                                <option value="bigInteger">bigInteger</option>
                                <option value="decimal">decimal</option>
                                <option value="boolean">boolean</option>
                                <option value="date">date</option>
                                <option value="datetime">datetime</option>
                                <option value="timestamp">timestamp</option>
                                <option value="json">json</option>
                                <option value="foreignId">foreignId</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2 required">Componente UI</label>
                            <select class="form-select form-select-solid form-select-sm" name="component" id="field_component" required>
                                <option value="">Selecione...</option>
                                <option value="input">input</option>
                                <option value="select">select</option>
                                <option value="select_module">select_module</option>
                                <option value="date">date</option>
                                <option value="datetime">datetime</option>
                                <option value="textarea">textarea</option>
                                <option value="switch">switch</option>
                                <option value="checkbox">checkbox</option>
                                <option value="radio">radio</option>
                                <option value="password">password</option>
                                <option value="upload">upload</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Grid Col</label>
                            <select class="form-select form-select-solid form-select-sm" name="grid_col" id="field_grid_col">
                                <option value="col-md-12">col-md-12 (100%)</option>
                                <option value="col-md-6">col-md-6 (50%)</option>
                                <option value="col-md-4">col-md-4 (33%)</option>
                                <option value="col-md-3">col-md-3 (25%)</option>
                                <option value="col-md-2">col-md-2 (16%)</option>
                                <option value="col-md-8">col-md-8 (66%)</option>
                                <option value="col-md-9">col-md-9 (75%)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Linha 2: Flags do campo (switches) --}}
                    <div class="row mb-5">
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Obrigatório</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="required" id="field_required" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Nullable</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="nullable" id="field_nullable" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Unique</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="unique" id="field_unique" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Index</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="index" id="field_index" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Main</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="main" id="field_main" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Customizado</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="is_custom" id="field_is_custom" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                    </div>

                    {{-- Linha 3: Tamanho e Limites --}}
                    <div class="row mb-5">
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Length</label>
                            <input type="number" class="form-control form-control-solid form-control-sm" name="length" id="field_length" placeholder="255">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Precision</label>
                            <input type="number" class="form-control form-control-solid form-control-sm" name="precision" id="field_precision" placeholder="2">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Default</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="default" id="field_default">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Min</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="min" id="field_min">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Max</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="max" id="field_max">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Ícone</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="icon" id="field_icon" placeholder="ki-outline ki-...">
                        </div>
                    </div>

                    {{-- Linha 4: UI --}}
                    <div class="row mb-5">
                        <div class="col-md-3">
                            <label class="fs-6 fw-semibold mb-2">Placeholder</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="placeholder" id="field_placeholder">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Máscara</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="mask" id="field_mask" placeholder="(99) 99999-9999">
                        </div>
                        <div class="col-md-3">
                            <label class="fs-6 fw-semibold mb-2">Tooltip</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="tooltip" id="field_tooltip">
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Tooltip Dir.</label>
                            <select class="form-select form-select-solid form-select-sm" name="tooltip_direction" id="field_tooltip_direction">
                                <option value="top">top</option>
                                <option value="bottom">bottom</option>
                                <option value="left">left</option>
                                <option value="right">right</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Ícone UI</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="ui_icon" id="field_ui_icon" placeholder="ki-outline ki-...">
                        </div>
                    </div>

                    {{-- Linha 5: Foreign Key --}}
                    <div class="row mb-5" id="fk_section">
                        <div class="col-12 mb-2">
                            <span class="fw-semibold text-muted fs-7">FOREIGN KEY / SELECT MODULE</span>
                            <div class="separator separator-dashed mt-1"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold mb-2">FK Tabela</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="fk_table" id="field_fk_table" placeholder="ex: brands">
                        </div>
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold mb-2">FK Coluna</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="fk_column" id="field_fk_column" placeholder="ex: id">
                        </div>
                        <div class="col-md-4">
                            <label class="fs-6 fw-semibold mb-2">FK Label</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="fk_label" id="field_fk_label" placeholder="ex: name">
                        </div>
                    </div>

                    {{-- Linha 6: Auto-geração --}}
                    <div class="row mb-5" id="auto_section">
                        <div class="col-12 mb-2">
                            <span class="fw-semibold text-muted fs-7">AUTO-GERAÇÃO</span>
                            <div class="separator separator-dashed mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">Auto From (campo origem)</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="auto_from" id="field_auto_from" placeholder="ex: name">
                        </div>
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold mb-2">Auto Type</label>
                            <select class="form-select form-select-solid form-select-sm" name="auto_type" id="field_auto_type">
                                <option value="">Nenhum</option>
                                <option value="slug">slug</option>
                                <option value="uppercase">uppercase</option>
                                <option value="lowercase">lowercase</option>
                            </select>
                        </div>
                    </div>

                    {{-- Linha 7: Visibilidade --}}
                    <div class="row mb-5">
                        <div class="col-12 mb-2">
                            <span class="fw-semibold text-muted fs-7">VISIBILIDADE E GRID</span>
                            <div class="separator separator-dashed mt-1"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Vis. Index</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="visible_index" id="field_visible_index" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Vis. Show</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="visible_show" id="field_visible_show" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Vis. Create</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="visible_create" id="field_visible_create" value="1" checked>
                                </div>
                                <span class="badge badge-light-primary badge-sm">Sim</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Vis. Edit</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="visible_edit" id="field_visible_edit" value="1" checked>
                                </div>
                                <span class="badge badge-light-primary badge-sm">Sim</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Buscável</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="searchable" id="field_searchable" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Ordenável</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                                    <input class="form-check-input field-switch-badge" type="checkbox" name="sortable" id="field_sortable" value="1">
                                </div>
                                <span class="badge badge-light-danger badge-sm">Não</span>
                            </div>
                        </div>
                    </div>

                    {{-- Linha 8: Grid Template --}}
                    <div class="row mb-5">
                        <div class="col-md-2">
                            <label class="fs-6 fw-semibold mb-2">Width Index</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="width_index" id="field_width_index" placeholder="ex: 150px">
                        </div>
                        <div class="col-md-5">
                            <label class="fs-6 fw-semibold mb-2">Grid Template</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="grid_template" id="field_grid_template" placeholder="ex: {first_name} {surname}">
                        </div>
                        <div class="col-md-5">
                            <label class="fs-6 fw-semibold mb-2">Grid Link</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" name="grid_link" id="field_grid_link" placeholder="ex: {show} ou URL">
                        </div>
                    </div>

                    {{-- Linha 9: Options JSON --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fs-6 fw-semibold mb-2">Options (JSON)</label>
                            <textarea class="form-control form-control-solid form-control-sm" name="options" id="field_options" rows="3" placeholder='ex: {"1": {"label": "Ativo", "badge": "success"}, "0": {"label": "Inativo", "badge": "danger"}}'></textarea>
                            <div class="form-text text-muted">JSON para selects, switches, checkboxes e badges. Deixe vazio se não aplicável.</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-3">
                {{-- Status à esquerda --}}
                <div class="d-flex align-items-center gap-2 me-auto">
                    <label class="fs-6 fw-semibold">Status</label>
                    <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                        <input class="form-check-input field-switch-badge" type="checkbox" name="status" id="field_status" value="1" form="form_field" checked>
                    </div>
                    <span class="badge badge-light-primary badge-sm">Ativo</span>
                </div>
                {{-- Origem --}}
                <div class="d-flex align-items-center gap-2 me-3">
                    <label class="fs-6 fw-semibold">Origem</label>
                    <select class="form-select form-select-solid form-select-sm" name="origin" id="field_origin" form="form_field" style="width: 110px;">
                        <option value="system">system</option>
                        <option value="custom" selected>custom</option>
                    </select>
                </div>
                {{-- Botões à direita --}}
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btn_save_field">
                    <i class="ki-outline ki-check fs-4"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

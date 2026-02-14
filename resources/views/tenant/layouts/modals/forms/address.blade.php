{{-- Formulário - Endereço --}}
<div class="row">
    <div class="col-md-7 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">Tipo de Endereço</label>
            <select name="type_address_id" id="address_type_select" class="form-select form-select-solid"
                data-control="select2" data-placeholder="Selecione..." data-hide-search="true" required>
                <option value="">Selecione...</option>
                @foreach (\App\Models\Tenant\TypeAddress::orderBy('order')->get() as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">CEP</label>
            <input type="text" name="zip_code" id="address_zip_code" class="form-control form-control-solid"
                placeholder="00000-000" value="" required />
        </div>
    </div>
    <div class="col-md-2 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">Número</label>
            <input type="text" name="number" id="address_number" class="form-control form-control-solid"
                placeholder="123" value="" required />
        </div>
    </div>

    <div class="col-md-8 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">Logradouro</label>
            <input type="text" name="street" id="address_street" class="form-control form-control-solid"
                placeholder="Rua, Avenida, etc." value="" required />
        </div>
    </div>

    <div class="col-md-4 mb-7">
        <div class="fv-row">
            <label class="fw-semibold fs-6 mb-2">Complemento (opcional)</label>
            <input type="text" name="complement" id="address_complement" class="form-control form-control-solid"
                placeholder="Apto, Bloco, Sala, etc." value="" />
        </div>
    </div>

    <div class="col-md-4 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">Bairro</label>
            <input type="text" name="neighborhood" id="address_neighborhood" class="form-control form-control-solid"
                placeholder="Nome do bairro" value="" required />
        </div>
    </div>

    <div class="col-md-6 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">Cidade</label>
            <input type="text" name="city" id="address_city" class="form-control form-control-solid"
                placeholder="Nome da cidade" value="" required />
        </div>
    </div>

    <div class="col-md-2 mb-7">
        <div class="fv-row">
            <label class="required fw-semibold fs-6 mb-2">UF</label>
            <select name="state" id="address_state" class="form-select form-select-solid" data-control="select2"
                data-placeholder="UF" required>
                <option value="">UF</option>
                <option value="AC">AC</option>
                <option value="AL">AL</option>
                <option value="AP">AP</option>
                <option value="AM">AM</option>
                <option value="BA">BA</option>
                <option value="CE">CE</option>
                <option value="DF">DF</option>
                <option value="ES">ES</option>
                <option value="GO">GO</option>
                <option value="MA">MA</option>
                <option value="MT">MT</option>
                <option value="MS">MS</option>
                <option value="MG">MG</option>
                <option value="PA">PA</option>
                <option value="PB">PB</option>
                <option value="PR">PR</option>
                <option value="PE">PE</option>
                <option value="PI">PI</option>
                <option value="RJ">RJ</option>
                <option value="RN">RN</option>
                <option value="RS">RS</option>
                <option value="RO">RO</option>
                <option value="RR">RR</option>
                <option value="SC">SC</option>
                <option value="SP">SP</option>
                <option value="SE">SE</option>
                <option value="TO">TO</option>
            </select>
        </div>
    </div>

    <input type="hidden" name="country" id="address_country" class="form-control form-control-solid"
        placeholder="País" value="BR" required />
</div>

<div class="fv-row mb-7">
    <label class="form-check form-check-custom form-check-solid">
        <input class="form-check-input" type="checkbox" name="is_main" id="address_is_main" value="1" />
        <span class="form-check-label fw-semibold">
            Marcar como endereço principal
        </span>
    </label>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ========================================
                // Select2 - Tipo de Endereço
                // ========================================
                $('#address_type_select').select2({
                    dropdownParent: $('#kt_modal_add_address'),
                    minimumResultsForSearch: Infinity
                });

                // ========================================
                // Select2 - Estado (UF)
                // ========================================
                $('#address_state').select2({
                    dropdownParent: $('#kt_modal_add_address'),
                    minimumResultsForSearch: Infinity
                });

                // ========================================
                // Máscara - CEP
                // ========================================
                var cepInput = document.getElementById('address_zip_code');
                var cepMask = new Inputmask('99999-999');
                cepMask.mask(cepInput);

                // ========================================
                // Busca CEP via ViaCEP
                // ========================================
                cepInput.addEventListener('blur', function() {
                    var cep = this.value.replace(/\D/g, '');

                    if (cep.length === 8) {
                        // Limpa campos
                        document.getElementById('address_street').value = '';
                        document.getElementById('address_neighborhood').value = '';
                        document.getElementById('address_city').value = '';
                        $('#address_state').val('').trigger('change');

                        // Busca CEP
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    document.getElementById('address_street').value = data.logradouro || '';
                                    document.getElementById('address_neighborhood').value = data.bairro ||
                                        '';
                                    document.getElementById('address_city').value = data.localidade || '';
                                    $('#address_state').val(data.uf).trigger('change');

                                    // Foca no número se o CEP foi encontrado
                                    document.getElementById('address_number').focus();
                                } else {
                                    toastr.warning('CEP não encontrado');
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao buscar CEP:', error);
                            });
                    }
                });

                // ========================================
                // Status Switch - Endereço
                // ========================================
                document.getElementById('address_status_switch').addEventListener('change', function() {
                    document.getElementById('address_status_hidden').value = this.checked ? '1' : '0';
                });
            });
        </script>
    @endpush
@endonce

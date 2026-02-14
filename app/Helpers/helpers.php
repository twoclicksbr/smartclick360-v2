<?php

if (!function_exists('format_phone')) {
    /**
     * Formata um número de telefone brasileiro
     *
     * @param string $phone Número sem formatação (apenas dígitos)
     * @return string Número formatado
     */
    function format_phone($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Remove tudo que não é número
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Aplica a máscara baseado no tamanho
        $length = strlen($phone);

        if ($length == 11) {
            // Celular com 9 dígitos: (99) 99999-9999
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        } elseif ($length == 10) {
            // Telefone fixo: (99) 9999-9999
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
        }

        // Se não tiver o tamanho esperado, retorna o original
        return $phone;
    }
}

if (!function_exists('format_document')) {
    /**
     * Formata um documento (CPF ou CNPJ)
     *
     * @param string $document Documento sem formatação (apenas dígitos)
     * @return string Documento formatado
     */
    function format_document($document)
    {
        if (empty($document)) {
            return '';
        }

        // Remove tudo que não é número
        $document = preg_replace('/[^0-9]/', '', $document);

        $length = strlen($document);

        if ($length == 11) {
            // CPF: 999.999.999-99
            return substr($document, 0, 3) . '.' . substr($document, 3, 3) . '.' . substr($document, 6, 3) . '-' . substr($document, 9, 2);
        } elseif ($length == 14) {
            // CNPJ: 99.999.999/9999-99
            return substr($document, 0, 2) . '.' . substr($document, 2, 3) . '.' . substr($document, 5, 3) . '/' . substr($document, 8, 4) . '-' . substr($document, 12, 2);
        }

        // Se não tiver o tamanho esperado, retorna o original
        return $document;
    }
}

if (!function_exists('format_cep')) {
    /**
     * Formata um CEP
     *
     * @param string $cep CEP sem formatação (apenas dígitos)
     * @return string CEP formatado
     */
    function format_cep($cep)
    {
        if (empty($cep)) {
            return '';
        }

        // Remove tudo que não é número
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) == 8) {
            // CEP: 99999-999
            return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
        }

        return $cep;
    }
}

if (!function_exists('encodeId')) {
    /**
     * Codifica um ID para URL amigável
     *
     * @param int $id ID a ser codificado
     * @return string ID codificado
     */
    function encodeId($id)
    {
        return rtrim(strtr(base64_encode($id), '+/', '-_'), '=');
    }
}

if (!function_exists('decodeId')) {
    /**
     * Decodifica um ID de URL amigável
     *
     * @param string $encoded ID codificado
     * @return int ID original
     */
    function decodeId($encoded)
    {
        return (int) base64_decode(strtr($encoded . str_repeat('=', (4 - strlen($encoded) % 4) % 4), '-_', '+/'));
    }
}

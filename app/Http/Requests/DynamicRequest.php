<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class DynamicRequest extends FormRequest
{
    /**
     * Slug do módulo (definido pelo controller antes da validação).
     */
    protected ?string $moduleSlug = null;

    /**
     * Conexão do banco (landlord ou tenant).
     */
    protected string $connectionName = 'tenant';

    /**
     * Define o slug do módulo para montar as regras.
     */
    public function setModuleSlug(string $slug): static
    {
        $this->moduleSlug = $slug;
        return $this;
    }

    /**
     * Define a conexão do banco.
     */
    public function setConnectionName(string $connection): static
    {
        $this->connectionName = $connection;
        return $this;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if (!$this->moduleSlug) {
            return [];
        }

        $fields = $this->getModuleFields();
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];

            // Required ou nullable
            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type
            switch ($field->type) {
                case 'string':
                    $fieldRules[] = 'string';
                    if ($field->length) {
                        $fieldRules[] = "max:{$field->length}";
                    }
                    break;
                case 'integer':
                    $fieldRules[] = 'integer';
                    break;
                case 'decimal':
                    $fieldRules[] = 'numeric';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'text':
                    $fieldRules[] = 'string';
                    break;
                case 'json':
                    $fieldRules[] = 'array';
                    break;
            }

            // Min / Max
            if ($field->min !== null) {
                $fieldRules[] = "min:{$field->min}";
            }
            if ($field->max !== null) {
                $fieldRules[] = "max:{$field->max}";
            }

            $rules[$field->name] = $fieldRules;
        }

        return $rules;
    }

    public function attributes(): array
    {
        if (!$this->moduleSlug) {
            return [];
        }

        $fields = $this->getModuleFields();
        $attributes = [];

        foreach ($fields as $field) {
            $attributes[$field->name] = $field->label;
        }

        return $attributes;
    }

    /**
     * Busca os campos do módulo (main = false, com status ativo).
     */
    protected function getModuleFields(): array
    {
        $moduleId = DB::connection($this->connectionName)
            ->table('modules')
            ->where('slug', $this->moduleSlug)
            ->whereNull('deleted_at')
            ->value('id');

        if (!$moduleId) {
            return [];
        }

        return DB::connection($this->connectionName)
            ->table('module_fields')
            ->where('module_id', $moduleId)
            ->where('main', false)
            ->where('status', true)
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->get()
            ->toArray();
    }
}

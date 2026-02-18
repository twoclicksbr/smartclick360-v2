<?php

namespace App\Services;

use App\Models\DynamicModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DynamicService
{
    protected DynamicModel $model;
    protected array $fields;

    public function __construct(DynamicModel $model)
    {
        $this->model = $model;
        $this->fields = $model->getModuleFields();
    }

    /**
     * Lista registros com paginação e ordenação.
     */
    public function index(array $params = [])
    {
        $config = $this->model->getModuleConfig();
        $sortField = $params['sort_by'] ?? $config['default_sort_field'] ?? 'id';
        $sortDir = $params['sort_direction'] ?? $config['default_sort_direction'] ?? 'asc';
        $perPage = $params['per_page'] ?? $config['per_page'] ?? 25;

        $query = $this->model->newQuery()->orderBy($sortField, $sortDir);

        // Incluir deletados se solicitado
        if (!empty($params['with_trashed'])) {
            $query->withTrashed();
        }

        // Quick search
        if (!empty($params['quick_search'])) {
            $search = $params['quick_search'];
            $searchableFields = $this->getSearchableFields();

            if (!empty($searchableFields)) {
                $query->where(function ($q) use ($search, $searchableFields) {
                    foreach ($searchableFields as $fieldName) {
                        $q->orWhere($fieldName, 'ILIKE', "%{$search}%");
                    }
                    // Sempre buscar por ID se for numérico
                    if (is_numeric($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca um registro pelo ID.
     */
    public function find(int $id)
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * Cria um novo registro.
     */
    public function create(array $data): mixed
    {
        $data = $this->processBeforeSave($data);
        $this->validateUnique($data);

        return $this->model->newQuery()->create($data);
    }

    /**
     * Atualiza um registro existente.
     */
    public function update(int $id, array $data): mixed
    {
        $record = $this->model->newQuery()->findOrFail($id);
        $data = $this->processBeforeSave($data, $id);
        $this->validateUnique($data, $id);

        $record->update($data);
        return $record;
    }

    /**
     * Soft delete de um registro.
     */
    public function destroy(int $id): bool
    {
        $record = $this->model->newQuery()->findOrFail($id);
        return $record->delete();
    }

    /**
     * Restaura um registro deletado.
     */
    public function restore(int $id): mixed
    {
        $record = $this->model->newQuery()->withTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }

    /**
     * Reordena registros (drag and drop).
     */
    public function reorder(array $items): void
    {
        foreach ($items as $item) {
            if (isset($item['id'], $item['order'])) {
                $this->model->newQuery()
                    ->where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        }
    }

    /**
     * Processa dados antes de salvar:
     * - auto_from (slug, uppercase, lowercase)
     * - password (bcrypt)
     * - Remove campos vazios de unique (evita false positives)
     */
    protected function processBeforeSave(array $data, ?int $ignoreId = null): array
    {
        foreach ($this->fields as $field) {
            $name = $field['name'];

            // auto_from: gera valor automaticamente
            if (!empty($field['auto_from']) && !empty($field['auto_type'])) {
                $sourceValue = $data[$field['auto_from']] ?? null;
                if ($sourceValue) {
                    $data[$name] = match ($field['auto_type']) {
                        'slug'      => Str::slug($sourceValue),
                        'uppercase' => Str::upper($sourceValue),
                        'lowercase' => Str::lower($sourceValue),
                        default     => $sourceValue,
                    };
                }
            }

            // password: bcrypt antes de salvar
            if (isset($data[$name]) && $this->isPasswordField($name)) {
                $data[$name] = Hash::make($data[$name]);
            }
        }

        return $data;
    }

    /**
     * Valida unicidade dos campos marcados como unique.
     * Campo vazio é ignorado (unique só valida se preenchido).
     */
    protected function validateUnique(array $data, ?int $ignoreId = null): void
    {
        $errors = [];

        foreach ($this->fields as $field) {
            if (!$field['unique']) {
                continue;
            }

            $name = $field['name'];
            $value = $data[$name] ?? null;

            // Campo vazio: ignora validação de unique
            if ($value === null || $value === '') {
                continue;
            }

            $query = $this->model->newQuery()
                ->where($name, $value);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if ($query->exists()) {
                $label = $field['label'] ?? $name;
                $errors[$name] = ["O campo {$label} já está em uso."];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Retorna nomes dos campos pesquisáveis (lê module_fields_ui).
     */
    protected function getSearchableFields(): array
    {
        $config = $this->model->getModuleConfig();
        $connection = $this->model->getConnectionName();

        return DB::connection($connection)
            ->table('module_fields_ui')
            ->join('module_fields', 'module_fields.id', '=', 'module_fields_ui.module_field_id')
            ->where('module_fields.module_id', $config['id'])
            ->where('module_fields_ui.searchable', true)
            ->whereNull('module_fields.deleted_at')
            ->whereNull('module_fields_ui.deleted_at')
            ->pluck('module_fields.name')
            ->toArray();
    }

    /**
     * Verifica se um campo é do tipo password (via module_fields_ui).
     */
    protected function isPasswordField(string $fieldName): bool
    {
        $config = $this->model->getModuleConfig();
        $connection = $this->model->getConnectionName();

        return DB::connection($connection)
            ->table('module_fields_ui')
            ->join('module_fields', 'module_fields.id', '=', 'module_fields_ui.module_field_id')
            ->where('module_fields.module_id', $config['id'])
            ->where('module_fields.name', $fieldName)
            ->where('module_fields_ui.component', 'password')
            ->whereNull('module_fields.deleted_at')
            ->whereNull('module_fields_ui.deleted_at')
            ->exists();
    }
}

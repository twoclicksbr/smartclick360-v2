<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DynamicModel extends Model
{
    use SoftDeletes;

    /**
     * Configuração do módulo (registro da tabela modules)
     */
    protected array $moduleConfig = [];

    /**
     * Campos do módulo (registros da tabela module_fields)
     */
    protected array $moduleFields = [];

    /**
     * Configura o model dinamicamente a partir do slug do módulo.
     *
     * @param string $slug    Slug do módulo (ex: 'people', 'brands')
     * @param string $connection  Conexão do banco ('landlord' ou 'tenant')
     * @return static
     */
    public static function forModule(string $slug, string $connection = 'tenant'): static
    {
        $instance = new static();
        $instance->setConnection($connection);

        // Buscar configuração do módulo
        $module = DB::connection($connection)
            ->table('modules')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        if (!$module) {
            throw new \RuntimeException("Módulo '{$slug}' não encontrado.");
        }

        $instance->moduleConfig = (array) $module;
        $instance->setTable($slug);

        // Buscar campos do módulo
        $fields = DB::connection($connection)
            ->table('module_fields')
            ->where('module_id', $module->id)
            ->where('main', false)
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->get();

        $instance->moduleFields = $fields->map(fn ($f) => (array) $f)->toArray();

        // Montar fillable (campos não-main)
        $fillable = $fields->pluck('name')->toArray();
        $fillable[] = 'order';
        $fillable = array_filter($fillable, fn ($f) => !in_array($f, ['id', 'created_at', 'updated_at', 'deleted_at']));
        $instance->fillable($fillable);

        // Montar casts
        $casts = [];
        foreach ($fields as $field) {
            switch ($field->type) {
                case 'boolean':
                    $casts[$field->name] = 'boolean';
                    break;
                case 'integer':
                    $casts[$field->name] = 'integer';
                    break;
                case 'decimal':
                    $casts[$field->name] = 'decimal:' . ($field->precision ?? 2);
                    break;
                case 'date':
                    $casts[$field->name] = 'date';
                    break;
                case 'datetime':
                    $casts[$field->name] = 'datetime';
                    break;
                case 'json':
                    $casts[$field->name] = 'array';
                    break;
            }
        }
        $casts['order'] = 'integer';
        $casts['status'] = 'boolean';
        $instance->mergeCasts($casts);

        return $instance;
    }

    /**
     * Retorna a configuração do módulo.
     */
    public function getModuleConfig(): array
    {
        return $this->moduleConfig;
    }

    /**
     * Retorna os campos do módulo.
     */
    public function getModuleFields(): array
    {
        return $this->moduleFields;
    }

    /**
     * Cria nova query preservando a configuração do módulo.
     */
    public function newInstance($attributes = [], $exists = false): static
    {
        $model = new static();
        $model->exists = $exists;
        $model->moduleConfig = $this->moduleConfig;
        $model->moduleFields = $this->moduleFields;
        $model->setTable($this->getTable());
        $model->setConnection($this->getConnectionName());
        $model->fillable($this->getFillable());
        $model->mergeCasts($this->getCasts());
        $model->fill((array) $attributes);

        return $model;
    }

    /**
     * Sobrescreve para preservar configuração ao hidratar do banco.
     */
    public function newFromBuilder($attributes = [], $connection = null): static
    {
        $model = parent::newFromBuilder($attributes, $connection);
        $model->moduleConfig = $this->moduleConfig;
        $model->moduleFields = $this->moduleFields;
        $model->setTable($this->getTable());
        $model->fillable($this->getFillable());
        $model->mergeCasts($this->getCasts());

        return $model;
    }
}

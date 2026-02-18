<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleFieldUi extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'module_fields_ui';

    protected $fillable = [
        'module_field_id',
        'component',
        'options',
        'placeholder',
        'mask',
        'icon',
        'tooltip',
        'tooltip_direction',
        'grid_col',
        'visible_index',
        'visible_show',
        'visible_create',
        'visible_edit',
        'width_index',
        'grid_template',
        'grid_link',
        'grid_actions',
        'searchable',
        'sortable',
        'order',
        'status',
    ];

    protected $casts = [
        'options' => 'array',
        'grid_actions' => 'array',
        'visible_index' => 'boolean',
        'visible_show' => 'boolean',
        'visible_create' => 'boolean',
        'visible_edit' => 'boolean',
        'searchable' => 'boolean',
        'sortable' => 'boolean',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(ModuleField::class, 'module_field_id');
    }
}

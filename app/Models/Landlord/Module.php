<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'scope',
        'icon',
        'model',
        'service',
        'controller',
        'show_drag',
        'show_checkbox',
        'show_actions',
        'default_sort_field',
        'default_sort_direction',
        'per_page',
        'description_index',
        'description_show',
        'description_create',
        'description_edit',
        'view_index',
        'view_show',
        'view_modal',
        'after_store',
        'after_update',
        'after_restore',
        'default_checked',
        'origin',
        'order',
        'status',
    ];

    protected $casts = [
        'show_drag' => 'boolean',
        'show_checkbox' => 'boolean',
        'show_actions' => 'boolean',
        'per_page' => 'integer',
        'default_checked' => 'boolean',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function submodules()
    {
        return $this->hasMany(ModuleSubmodule::class, 'module_id');
    }

    public function fields()
    {
        return $this->hasMany(ModuleField::class, 'module_id')->orderBy('order');
    }

    public function seeds()
    {
        return $this->hasMany(ModuleSeed::class, 'module_id')->orderBy('order');
    }
}

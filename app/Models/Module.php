<?php

namespace App\Models;

use App\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory, UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'icon',
        'order',
        'active',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope para módulos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para módulos (não submódulos)
     */
    public function scopeModules($query)
    {
        return $query->where('type', 'module');
    }

    /**
     * Scope para submódulos
     */
    public function scopeSubmodules($query)
    {
        return $query->where('type', 'submodule');
    }

    /**
     * Submódulos vinculados (pivot)
     */
    public function submodules()
    {
        return $this->belongsToMany(Module::class, 'module_submodules', 'module_id', 'submodule_id')
            ->withPivot('order', 'active')
            ->withTimestamps();
    }

    /**
     * Módulos aos quais este submódulo pertence
     */
    public function parentModules()
    {
        return $this->belongsToMany(Module::class, 'module_submodules', 'submodule_id', 'module_id')
            ->withPivot('order', 'active')
            ->withTimestamps();
    }
}

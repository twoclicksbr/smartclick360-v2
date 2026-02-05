<?php

namespace App\Models;

use App\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes, UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'birthdate',
        'order',
        'status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'order' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relacionamento com Permissions
     */
    public function permissions()
    {
        return $this->hasMany(PersonPermission::class);
    }

    /**
     * Relacionamento com PersonTypes (many-to-many)
     */
    public function personTypes()
    {
        return $this->belongsToMany(PersonType::class, 'person_person_type')
            ->withTimestamps();
    }

    /**
     * Relacionamento com Status
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Verifica se a pessoa tem permissão
     */
    public function hasPermission(string $moduleSlug, string $action): bool
    {
        return $this->permissions()
            ->whereHas('module', fn($q) => $q->where('slug', $moduleSlug))
            ->where('action', $action)
            ->exists();
    }

    /**
     * Retorna permissões agrupadas por módulo
     * Formato: { "module_slug": ["action1", "action2"], ... }
     */
    public function formattedPermissions(): array
    {
        return $this->permissions()
            ->with('module:id,slug')
            ->get()
            ->groupBy('module.slug')
            ->map(fn($permissions) => $permissions->pluck('action')->values()->all())
            ->toArray();
    }
}

<?php

namespace App\Models;

use App\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonPermission extends Model
{
    use HasFactory, UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'person_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'person_id',
        'module_id',
        'action',
    ];

    /**
     * Relacionamento com Person
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Relacionamento com Module
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

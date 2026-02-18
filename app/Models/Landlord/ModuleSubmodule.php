<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleSubmodule extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'module_id',
        'submodule_id',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function submodule()
    {
        return $this->belongsTo(Module::class, 'submodule_id');
    }
}

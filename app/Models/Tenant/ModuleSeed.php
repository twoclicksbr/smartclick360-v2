<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleSeed extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'module_id',
        'data',
        'order',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}

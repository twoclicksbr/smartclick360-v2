<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'module_id',
        'register_id',
        'content',
        'order',
        'status',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

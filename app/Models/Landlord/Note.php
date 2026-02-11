<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';

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

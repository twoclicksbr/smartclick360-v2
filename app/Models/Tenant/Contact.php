<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'type_contact_id',
        'module_id',
        'register_id',
        'value',
        'order',
        'status',
    ];

    public function typeContact()
    {
        return $this->belongsTo(TypeContact::class, 'type_contact_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

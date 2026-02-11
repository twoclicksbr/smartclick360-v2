<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'type_address_id',
        'module_id',
        'register_id',
        'is_main',
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'order',
        'status',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function typeAddress()
    {
        return $this->belongsTo(TypeAddress::class, 'type_address_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

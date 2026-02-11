<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeAddress extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'type_addresses';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class, 'type_address_id');
    }
}

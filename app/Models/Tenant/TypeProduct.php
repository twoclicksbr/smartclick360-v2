<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeProduct extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'type_products';

    protected $fillable = [
        'name',
        'type',
        'order',
        'status',
    ];
}

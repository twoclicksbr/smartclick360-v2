<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

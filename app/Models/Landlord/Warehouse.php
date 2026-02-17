<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

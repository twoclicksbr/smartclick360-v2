<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'units';

    protected $fillable = [
        'name',
        'abbreviation',
        'decimal_places',
        'order',
        'status',
    ];
}

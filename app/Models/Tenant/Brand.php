<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'brands';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

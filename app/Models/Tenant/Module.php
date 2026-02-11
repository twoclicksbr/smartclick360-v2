<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'order',
        'status',
    ];
}

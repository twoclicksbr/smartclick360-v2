<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'families';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

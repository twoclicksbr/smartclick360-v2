<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Origin extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'origins';

    protected $fillable = [
        'code',
        'description',
        'order',
        'status',
    ];
}

<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'groups';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

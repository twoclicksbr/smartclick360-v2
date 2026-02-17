<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'groups';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

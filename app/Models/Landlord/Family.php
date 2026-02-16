<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'families';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

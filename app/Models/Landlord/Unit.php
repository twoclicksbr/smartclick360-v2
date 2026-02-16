<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'units';

    protected $fillable = [
        'name',
        'abbreviation',
        'decimal_places',
        'order',
        'status',
    ];
}

<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'brands';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];
}

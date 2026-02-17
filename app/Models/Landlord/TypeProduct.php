<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeProduct extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'type_products';

    protected $fillable = [
        'name',
        'type',
        'order',
        'status',
    ];
}

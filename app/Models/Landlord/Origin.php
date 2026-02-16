<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Origin extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'origins';

    protected $fillable = [
        'code',
        'description',
        'order',
        'status',
    ];
}

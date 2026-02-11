<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'surname',
        'order',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

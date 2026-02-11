<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'people';

    protected $fillable = [
        'tenant_id',
        'first_name',
        'surname',
        'order',
        'status',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

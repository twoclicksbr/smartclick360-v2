<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function variationOptions()
    {
        return $this->hasMany(VariationOption::class);
    }
}

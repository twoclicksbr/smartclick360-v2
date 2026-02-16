<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'variation_type_id',
        'name',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function variationType()
    {
        return $this->belongsTo(VariationType::class);
    }
}

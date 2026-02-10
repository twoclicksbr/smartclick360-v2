<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'type_address_id',
        'module_id',
        'register_id',
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'order',
        'status',
    ];

    protected $casts = [
        'register_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function typeAddress(): BelongsTo
    {
        return $this->belongsTo(TypeAddress::class, 'type_address_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

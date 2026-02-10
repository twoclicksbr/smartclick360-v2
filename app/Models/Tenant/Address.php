<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'production.addresses';

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
        'type_address_id' => 'integer',
        'module_id' => 'integer',
        'register_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function typeAddress(): BelongsTo
    {
        return $this->belongsTo(TypeAddress::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

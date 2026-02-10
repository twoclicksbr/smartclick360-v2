<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $table = 'type_addresses';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'type_address_id');
    }
}

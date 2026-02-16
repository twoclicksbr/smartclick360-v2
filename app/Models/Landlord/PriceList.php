<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'type',
        'percentage',
        'order',
        'status',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function salesChannels()
    {
        return $this->hasMany(SalesChannel::class);
    }
}

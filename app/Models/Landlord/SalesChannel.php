<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesChannel extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'price_list_id',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }
}

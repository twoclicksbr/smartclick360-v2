<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $table = 'type_contacts';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'type_contact_id');
    }
}

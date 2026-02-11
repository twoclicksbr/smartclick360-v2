<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeContact extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'type_contacts';

    protected $fillable = [
        'name',
        'mask',
        'order',
        'status',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'type_contact_id');
    }
}

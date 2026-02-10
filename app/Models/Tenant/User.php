<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'production.users';

    protected $fillable = [
        'person_id',
        'email',
        'password',
        'order',
        'status',
    ];

    protected $casts = [
        'person_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

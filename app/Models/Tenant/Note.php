<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'production.notes';

    protected $fillable = [
        'module_id',
        'register_id',
        'content',
        'order',
        'status',
    ];

    protected $casts = [
        'module_id' => 'integer',
        'register_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

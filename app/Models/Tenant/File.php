<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'production.files';

    protected $fillable = [
        'module_id',
        'register_id',
        'name',
        'path',
        'mime_type',
        'size',
        'order',
        'status',
    ];

    protected $casts = [
        'module_id' => 'integer',
        'register_id' => 'integer',
        'size' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

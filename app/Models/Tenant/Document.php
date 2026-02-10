<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'production.documents';

    protected $fillable = [
        'type_document_id',
        'module_id',
        'register_id',
        'value',
        'order',
        'status',
    ];

    protected $casts = [
        'type_document_id' => 'integer',
        'module_id' => 'integer',
        'register_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

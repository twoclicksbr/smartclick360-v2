<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'type_document_id',
        'module_id',
        'register_id',
        'value',
        'expiration_date',
        'order',
        'status',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function typeDocument()
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

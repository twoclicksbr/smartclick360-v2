<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeDocument extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'type_documents';

    protected $fillable = [
        'name',
        'mask',
        'order',
        'status',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'type_document_id');
    }
}

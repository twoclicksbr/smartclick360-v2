<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleField extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'landlord';

    protected $fillable = [
        'module_id',
        'main',
        'is_custom',
        'icon',
        'name',
        'label',
        'type',
        'length',
        'precision',
        'default',
        'nullable',
        'required',
        'unique',
        'index',
        'fk_table',
        'fk_column',
        'fk_label',
        'auto_from',
        'auto_type',
        'min',
        'max',
        'order',
        'status',
        'origin',
    ];

    protected $casts = [
        'main' => 'boolean',
        'is_custom' => 'boolean',
        'length' => 'integer',
        'precision' => 'integer',
        'nullable' => 'boolean',
        'required' => 'boolean',
        'unique' => 'boolean',
        'index' => 'boolean',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function ui()
    {
        return $this->hasOne(ModuleFieldUi::class, 'module_field_id');
    }
}

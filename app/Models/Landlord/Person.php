<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $connection = 'landlord';
    protected $table = 'people';

    protected $fillable = [
        'tenant_id',
        'first_name',
        'surname',
        'birth_date',
        'order',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'status' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'register_id')
            ->where('module_id', function($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'people')
                    ->limit(1);
            });
    }
}

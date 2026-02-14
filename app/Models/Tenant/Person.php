<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'people';

    protected $fillable = [
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

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'register_id')
            ->where('module_id', function($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'people')
                    ->limit(1);
            });
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'register_id')
            ->where('module_id', function($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'people')
                    ->limit(1);
            });
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'register_id')
            ->where('module_id', function($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'people')
                    ->limit(1);
            });
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

    public function notes()
    {
        return $this->hasMany(Note::class, 'register_id')
            ->where('module_id', function($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'people')
                    ->limit(1);
            });
    }
}

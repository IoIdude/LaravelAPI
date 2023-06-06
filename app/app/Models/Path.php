<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Path extends Model
{
    protected $table = 'paths';
    protected $fillable = [
        'route',
        'type_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, table: 'role_path');
    }
}

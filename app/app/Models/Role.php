<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

/*    public function paths()
    {
        return $this->belongsToMany(Path::class);
    }*/

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function paths()
    {
        return $this->belongsToMany(Path::class, table: 'role_path');
    }
}

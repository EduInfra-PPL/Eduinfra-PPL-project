<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'institution',
        'access_level',
        'description',
        'permissions',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }
}

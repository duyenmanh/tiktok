<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refresh_token extends Model
{
    use HasFactory;

    protected $table = 'refresh_token';
    protected $fillable = [
        'id',
        'refresh_code',
        'created_at',
        'updated_at'
    ];
}

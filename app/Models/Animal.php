<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'peso',
        'idade',
        'especie',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

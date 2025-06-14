<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'data',
        'hora',
        'especie',
        'servico',
        'pet',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



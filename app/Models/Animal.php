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
        'imagem',
        'especie',
        'user_id',
    ];

    /**
     * Define o relacionamento com o usuário (se houver autenticação por usuários).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

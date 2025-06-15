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
        'servico_id',
        'pet_id',
        'user_id',
    ];


    public function pet()
    {
        return $this->belongsTo(Animal::class, 'pet_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

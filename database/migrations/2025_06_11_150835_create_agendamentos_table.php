<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();

            $table->string('nome');
            $table->string('email');
            $table->string('telefone', 20);
            $table->date('data');
            $table->time('hora');
            $table->string('especie');

            $table->foreignId('servico_id')
                  ->constrained('servicos')
                  ->cascadeOnDelete();

            $table->foreignId('pet_id')
                  ->constrained('animals')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};

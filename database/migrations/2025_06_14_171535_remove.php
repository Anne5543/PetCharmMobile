<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
    $table->dropForeign(['servico_id']);
    $table->dropForeign(['pet_id']);
    $table->dropColumn(['servico_id', 'pet_id']);
    
    $table->string('servico')->nullable();
    $table->string('pet')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

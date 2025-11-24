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
        Schema::create('preparation_aliments', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable()->comment('Nom optionnel de la préparation');
            $table->decimal('quantite_totale', 10, 2)->default(0)->comment('Quantité totale en kg');
            $table->decimal('cout_total', 10, 2)->default(0)->comment('Coût total en FCFA');
            $table->date('date_preparation');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preparation_aliments');
    }
};

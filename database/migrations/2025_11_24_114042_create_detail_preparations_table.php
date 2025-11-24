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
        Schema::create('detail_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preparation_aliment_id')->constrained('preparation_aliments')->cascadeOnDelete();
            $table->foreignId('aliment_id')->constrained('aliments')->cascadeOnDelete();
            $table->decimal('quantite_kg', 10, 2)->comment('Quantité utilisée en kg');
            $table->decimal('cout_unitaire_kg', 10, 2)->comment('Coût unitaire par kg au moment de la sortie');
            $table->decimal('valeur', 10, 2)->comment('Valeur totale = quantite_kg × cout_unitaire_kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_preparations');
    }
};

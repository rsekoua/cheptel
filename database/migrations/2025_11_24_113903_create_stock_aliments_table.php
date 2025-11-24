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
        Schema::create('stock_aliments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aliment_id')->constrained('aliments')->cascadeOnDelete();
            $table->enum('type_stock', ['entrepot', 'preparation']);
            $table->decimal('nombre_sacs_disponibles', 10, 2)->default(0);
            $table->decimal('poids_kg_disponible', 10, 2)->default(0);
            $table->decimal('cout_moyen_kg', 10, 2)->default(0)->comment('Coût moyen pondéré par kg');
            $table->decimal('valeur_stock', 10, 2)->default(0)->comment('Valeur totale du stock');
            $table->timestamp('derniere_maj')->nullable();
            $table->timestamps();

            $table->unique(['aliment_id', 'type_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_aliments');
    }
};

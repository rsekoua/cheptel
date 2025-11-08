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
        Schema::create('produits_sanitaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->enum('type', ['vaccin', 'antibiotique', 'antiparasitaire', 'autre']);
            $table->string('laboratoire', 100)->nullable();
            $table->string('principe_actif', 200)->nullable();
            $table->string('numero_amm', 50)->nullable()->comment('Numéro AMM');
            $table->integer('delai_attente_jours')->nullable()->comment('Délai avant abattage en jours');
            $table->enum('voie_administration', ['injectable', 'orale', 'topique'])->nullable();
            $table->decimal('dosage_ml_kg', 6, 3)->nullable()->comment('Dosage en ml/kg');
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_alerte')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits_sanitaires');
    }
};

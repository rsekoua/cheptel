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
        Schema::create('cycles_reproduction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained('animaux')->cascadeOnDelete();
            $table->integer('numero_cycle');
            $table->date('date_debut');

            $table->enum('statut_cycle', ['en_attente', 'gestation_en_cours', 'en_lactation', 'termine_succes', 'termine_echec'])->default('en_attente');

            $table->datetime('date_premiere_saillie')->nullable();
            $table->enum('type_saillie', ['IA', 'MN'])->nullable()->comment('IA=Insémination Artificielle, MN=Monte Naturelle');

            $table->date('date_diagnostic')->nullable();
            $table->enum('resultat_diagnostic', ['positif', 'negatif', 'en_attente'])->default('en_attente');

            $table->date('date_mise_bas_prevue')->nullable();
            $table->date('date_mise_bas_reelle')->nullable();

            $table->text('motif_echec')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('animal_id');
            $table->index('statut_cycle');
            $table->index('date_mise_bas_prevue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycles_reproduction');
    }
};

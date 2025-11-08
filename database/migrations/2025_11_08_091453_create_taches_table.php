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
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->enum('type_tache', ['alimentation', 'sanitaire', 'reproduction', 'mouvement', 'verification', 'autre']);
            $table->enum('priorite', ['basse', 'normale', 'haute', 'critique'])->default('normale');
            $table->enum('type_cible', ['animal', 'lot', 'portee', 'salle', 'general']);
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->cascadeOnDelete();
            $table->foreignId('portee_id')->nullable()->constrained('portees')->cascadeOnDelete();
            $table->foreignId('salle_id')->nullable()->constrained('salles')->cascadeOnDelete();
            $table->date('date_echeance');
            $table->date('date_debut_periode')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'annulee'])->default('en_attente');
            $table->datetime('date_realisation')->nullable();
            $table->foreignId('utilisateur_assigne_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('utilisateur_realisation_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('generee_automatiquement')->default(false);
            $table->string('evenement_lie_type', 50)->nullable();
            $table->unsignedBigInteger('evenement_lie_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type_tache');
            $table->index('priorite');
            $table->index('statut');
            $table->index('date_echeance');
            $table->index('animal_id');
            $table->index('lot_id');
            $table->index('portee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};

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
        Schema::create('evenements_sanitaires', function (Blueprint $table) {
            $table->id();
            $table->enum('type_cible', ['animal', 'lot']);
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->cascadeOnDelete();
            $table->datetime('date_evenement');
            $table->enum('type_evenement', ['vaccination', 'traitement', 'castration', 'caudectomie', 'autre']);
            $table->foreignId('produit_sanitaire_id')->nullable()->constrained('produits_sanitaires')->nullOnDelete();
            $table->decimal('dose_administree', 8, 3)->nullable();
            $table->integer('nb_animaux_traites')->nullable();
            $table->string('intervenant', 100)->nullable();
            $table->text('motif')->nullable();
            $table->decimal('cout_total', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type_cible');
            $table->index('animal_id');
            $table->index('lot_id');
            $table->index('date_evenement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements_sanitaires');
    }
};

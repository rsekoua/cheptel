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
        Schema::create('pesees', function (Blueprint $table) {
            $table->id();
            $table->enum('type_cible', ['animal', 'lot']);
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->cascadeOnDelete();
            $table->date('date_pesee');
            $table->decimal('poids_total_kg', 8, 2);
            $table->integer('nb_animaux_peses')->default(1);
            $table->decimal('poids_moyen_kg', 6, 2)->storedAs('poids_total_kg / nb_animaux_peses');
            $table->enum('methode', ['individuelle', 'collective', 'echantillon']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type_cible');
            $table->index('animal_id');
            $table->index('lot_id');
            $table->index('date_pesee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesees');
    }
};

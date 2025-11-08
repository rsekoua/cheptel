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
        Schema::create('salles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_salle_id')->constrained('type_salles')->cascadeOnDelete();
            $table->string('nom', 100)->unique();
            $table->integer('capacite');
            $table->enum('statut', ['disponible', 'occupee', 'vide_sanitaire', 'maintenance'])->default('disponible');
            $table->date('date_debut_vide_sanitaire')->nullable();
            $table->integer('duree_vide_sanitaire_jours')->default(7);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};

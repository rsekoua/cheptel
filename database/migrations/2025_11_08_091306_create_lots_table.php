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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lot', 100)->unique();
            $table->enum('type_lot', ['post_sevrage', 'engraissement']);
            $table->date('date_creation');
            $table->integer('nb_animaux_depart');
            $table->integer('nb_animaux_actuel');
            $table->decimal('poids_total_depart_kg', 8, 2)->nullable();
            $table->decimal('poids_moyen_depart_kg', 6, 2)->nullable();
            $table->decimal('poids_total_actuel_kg', 8, 2)->nullable();
            $table->decimal('poids_moyen_actuel_kg', 6, 2)->nullable();
            $table->date('date_derniere_pesee')->nullable();
            $table->foreignId('salle_id')->nullable()->constrained('salles')->nullOnDelete();
            $table->enum('statut_lot', ['actif', 'transfere', 'vendu', 'cloture'])->default('actif');
            $table->foreignId('plan_alimentation_id')->nullable()->constrained('plan_alimentations')->nullOnDelete();
            $table->date('date_sortie')->nullable();
            $table->integer('nb_animaux_sortie')->nullable();
            $table->decimal('poids_total_sortie_kg', 8, 2)->nullable();
            $table->decimal('poids_moyen_sortie_kg', 6, 2)->nullable();
            $table->decimal('prix_vente_total', 10, 2)->nullable();
            $table->string('destination_sortie', 200)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('numero_lot');
            $table->index('type_lot');
            $table->index('statut_lot');
            $table->index('date_creation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};

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
        Schema::create('mouvement_aliments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aliment_id')->constrained('aliments')->cascadeOnDelete();
            $table->enum('type_mouvement', ['achat', 'transfert_entree', 'transfert_sortie', 'sortie_preparation']);
            $table->enum('type_stock', ['entrepot', 'preparation']);
            $table->foreignId('preparation_aliment_id')->nullable()->constrained('preparation_aliments')->cascadeOnDelete();
            $table->decimal('nombre_sacs', 10, 2)->default(0);
            $table->decimal('poids_kg', 10, 2);
            $table->decimal('prix_unitaire_sac', 10, 2)->nullable()->comment('Prix par sac pour les achats');
            $table->decimal('cout_total', 10, 2)->nullable()->comment('Coût total pour les achats');
            $table->decimal('cout_unitaire_kg', 10, 2)->comment('Coût par kg au moment du mouvement');
            $table->decimal('valeur_mouvement', 10, 2)->comment('Valeur monétaire du mouvement');
            $table->date('date_mouvement');
            $table->string('reference')->nullable()->comment('Bon de commande, facture...');
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
        Schema::dropIfExists('mouvement_aliments');
    }
};

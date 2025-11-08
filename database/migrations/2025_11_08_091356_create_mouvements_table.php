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
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();
            $table->enum('type_cible', ['animal', 'lot']);
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->cascadeOnDelete();
            $table->datetime('date_mouvement');
            $table->foreignId('salle_origine_id')->nullable()->constrained('salles')->nullOnDelete();
            $table->foreignId('salle_destination_id')->constrained('salles')->cascadeOnDelete();
            $table->string('place_numero', 20)->nullable();
            $table->enum('motif', ['preparation_saillie', 'transfert_maternite', 'retour_gestantes', 'sevrage', 'transfert_engraissement', 'autre']);
            $table->integer('nb_animaux')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type_cible');
            $table->index('animal_id');
            $table->index('lot_id');
            $table->index('date_mouvement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};

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
        Schema::create('animaux', function (Blueprint $table) {
            $table->id();
            $table->string('numero_identification', 50)->unique();
            $table->enum('type_animal', ['truie', 'verrat']);
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->boolean('is_actif')->default(true);
            $table->date('date_naissance')->nullable();
            $table->date('date_entree')->nullable();
            $table->enum('origine', ['naissance_elevage', 'achat_externe']);
            $table->string('numero_mere', 50)->nullable();
            $table->string('numero_pere', 50)->nullable();
            $table->string('provenance', 50)->nullable();

            //            $table->foreignId('salle_id')->nullable()->constrained('salles')->nullOnDelete();
            //            $table->string('place_numero', 20)->nullable();
            //            $table->decimal('poids_actuel_kg', 6, 2)->nullable();
            //            $table->date('date_derniere_pesee')->nullable();
            //            $table->foreignId('plan_alimentation_id')->nullable()->constrained('plan_alimentations')->nullOnDelete();

            $table->date('date_reforme')->nullable();
            $table->text('motif_reforme')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('numero_identification');
            $table->index('type_animal');
            $table->index('is_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animaux');
    }
};

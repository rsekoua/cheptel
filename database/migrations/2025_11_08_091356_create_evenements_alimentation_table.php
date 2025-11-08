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
        Schema::create('evenements_alimentation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('animaux')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('plan_alimentation_id')->constrained('plan_alimentations')->cascadeOnDelete();
            $table->decimal('quantite_kg', 8, 2);
            $table->integer('nb_animaux')->nullable();
            $table->decimal('cout_total', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('lot_id');
            $table->index('animal_id');
            $table->index('date_debut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements_alimentation');
    }
};

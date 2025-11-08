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
        Schema::create('plan_alimentations', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->enum('type_animal', ['reproducteur', 'production']);
            $table->text('description')->nullable();
            $table->decimal('energie_mcal_jour', 6, 2)->nullable()->comment('Énergie en Mcal/jour');
            $table->decimal('proteine_pourcent', 4, 1)->nullable()->comment('% de protéines');
            $table->decimal('ration_kg_jour', 5, 2)->nullable()->comment('Ration en kg/jour');
            $table->boolean('a_volonte')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_alimentations');
    }
};

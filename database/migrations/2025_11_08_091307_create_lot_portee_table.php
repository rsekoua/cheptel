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
        Schema::create('lot_portee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->cascadeOnDelete();
            $table->foreignId('portee_id')->constrained('portees')->cascadeOnDelete();
            $table->integer('nb_porcelets_transferes');
            $table->decimal('poids_total_transfere_kg', 7, 2)->nullable();
            $table->timestamps();

            $table->unique(['lot_id', 'portee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_portee');
    }
};

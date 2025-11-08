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
        Schema::create('saillies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_reproduction_id')->constrained('cycles_reproduction')->cascadeOnDelete();
            $table->datetime('date_heure');
            $table->enum('type', ['IA', 'MN'])->comment('IA=Insémination Artificielle, MN=Monte Naturelle');
            $table->foreignId('verrat_id')->nullable()->constrained('animaux')->nullOnDelete();
            $table->string('semence_lot_numero', 100)->nullable();
            $table->string('intervenant', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('cycle_reproduction_id');
            $table->index('date_heure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saillies');
    }
};

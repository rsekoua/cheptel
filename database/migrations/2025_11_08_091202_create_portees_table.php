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
        Schema::create('portees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_reproduction_id')->constrained('cycles_reproduction')->cascadeOnDelete();
            $table->foreignId('animal_id')->constrained('animaux')->cascadeOnDelete();
            $table->datetime('date_mise_bas');
            $table->integer('nb_nes_vifs');
            $table->integer('nb_mort_nes')->default(0);
            $table->integer('nb_momifies')->default(0);
            $table->integer('nb_total')->storedAs('nb_nes_vifs + nb_mort_nes + nb_momifies');
            $table->integer('poids_moyen_naissance_g')->nullable()->comment('Poids moyen à la naissance en grammes');
            $table->date('date_sevrage')->nullable();
            $table->integer('nb_sevres')->nullable();
            $table->decimal('poids_total_sevrage_kg', 7, 2)->nullable();
            $table->decimal('poids_moyen_sevrage_kg', 5, 2)->nullable();
            $table->unsignedBigInteger('lot_destination_id')->nullable()->comment('Lot où les porcelets sevrés sont transférés');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('cycle_reproduction_id');
            $table->index('animal_id');
            $table->index('date_sevrage');
            $table->index('lot_destination_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portees');
    }
};

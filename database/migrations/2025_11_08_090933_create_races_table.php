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
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['maternelle', 'paternelle', 'mixte']);
            $table->decimal('gmq_moyen', 5, 2)->nullable()->comment('Gain Moyen Quotidien en grammes/jour');
            $table->decimal('poids_adulte_moyen', 6, 2)->nullable()->comment('Poids adulte moyen en kg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};

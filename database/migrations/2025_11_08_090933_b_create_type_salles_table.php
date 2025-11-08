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
        Schema::create('type_salles', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('capacite_type', ['individuelle', 'collective']);
            $table->decimal('temperature_optimale', 4, 1)->nullable()->comment('Température en °C');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_salles');
    }
};

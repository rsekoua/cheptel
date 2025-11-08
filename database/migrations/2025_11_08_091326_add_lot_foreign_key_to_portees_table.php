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
        Schema::table('portees', function (Blueprint $table) {
            $table->foreign('lot_destination_id')->references('id')->on('lots')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portees', function (Blueprint $table) {
            $table->dropForeign(['lot_destination_id']);
        });
    }
};

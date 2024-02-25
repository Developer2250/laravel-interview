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
        Schema::create('distribution_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prize_id');
            $table->integer('winners');
            $table->decimal('actual_probability',10,2)->default(0);
            $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_data');
    }
};

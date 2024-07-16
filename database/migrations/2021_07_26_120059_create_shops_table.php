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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            
            $table->string('email')->nullable();
            
            $table->string('address');
            
            $table->string('mon_fri_open');
            $table->string('mon_fri_close');
            
            $table->string('sat_sun_open');
            $table->string('sat_sun_close');
            
            $table->string('image');

            $table->unsignedBigInteger('seller_id')->unsigned()->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade')->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};

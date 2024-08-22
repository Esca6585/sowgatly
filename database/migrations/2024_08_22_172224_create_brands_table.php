<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            
            $table->string('attributes')->nullable();
            
            $table->string('code')->unique();
            
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('brand_id'); // Added brand_id column

            $table->boolean('status')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade'); // Added foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
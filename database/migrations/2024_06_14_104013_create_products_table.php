<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('discount')->nullable();
            $table->text('description');
            $table->string('gender')->nullable()->comment('Men, Women, Children bolup bilya');
            $table->json('sizes')->nullable()->comment('42, 43,...,50 yaly olcegler array gornushunde saklanmaly');
            $table->json('separated_sizes')->nullable()->comment('S, M, L yaly olcegler array gornushunde saklanmaly');
            $table->string('color')->nullable();
            $table->text('manufacturer')->nullable()->comment('Cykarylan yurdy');
            $table->double('width')->nullable();
            $table->double('height')->nullable();
            $table->double('weight')->nullable()->comment('Hemmesi gram gorunusinde bellenmeli');
            $table->integer('production_time')->nullable()->comment('Hemme product time minutda gorkeziler');
            $table->integer('min_order')->nullable();
            $table->boolean('seller_status')->comment('Bu dukancy tarapyndan berilmeli status');
            $table->boolean('status')->comment('Bu administrator tarapyndan berilmeli status');

            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('category_id');
            
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
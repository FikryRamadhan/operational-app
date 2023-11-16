<?php

use App\Models\Brand;
use App\Models\Product_type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('product_name', 255);
            $table->string('model_name')->nullable();
            $table->bigInteger('id_brand')->nullable();
            $table->bigInteger('id_product_type')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->string('file_photo')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
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
};

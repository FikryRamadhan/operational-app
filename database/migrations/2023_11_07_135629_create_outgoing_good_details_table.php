<?php

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
        Schema::create('outgoing_good_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_outgoing_good')->nullable();
            $table->bigInteger('id_product')->nullable();
            $table->bigInteger('amount')->nullable();
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
        Schema::dropIfExists('outgoing_good_details');
    }
};

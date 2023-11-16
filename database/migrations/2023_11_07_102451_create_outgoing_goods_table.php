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
        Schema::create('outgoing_goods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_warehouse')->nullable();
            $table->string('transaction_number')->nullable();
            $table->date('date')->nullable();
            $table->bigInteger('total_amount')->nullable();
            $table->bigInteger('id_user')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('deleted_at');
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
        Schema::dropIfExists('outgoing_goods');
    }
};

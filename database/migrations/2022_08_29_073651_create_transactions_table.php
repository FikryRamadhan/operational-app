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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type');
            $table->foreignId('id_category');
            $table->text('description');
            $table->bigInteger('nominal');
            $table->string('file_transaction_proof')->nullable();
            $table->string('is_verified')->default('No');
            $table->bigInteger('id_user');
            $table->bigInteger('id_verificator_user')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};

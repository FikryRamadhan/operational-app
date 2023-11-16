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
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->bigInteger('stock')->nullable()->after('id_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->bigInteger('stock')->nullable();
        });
    }
};

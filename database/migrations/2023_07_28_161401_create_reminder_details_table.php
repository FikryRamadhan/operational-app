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
		Schema::create('reminder_details', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('id_reminder');
			$table->string('reminder_on')->default('0');
			$table->integer('reminder_minutes')->default(0);
			$table->timestamp('reminder_time')->nullable();
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
		Schema::dropIfExists('reminder_details');
	}
};

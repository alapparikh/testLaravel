<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMealscoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meal_scores', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->float('meal_1')->default(0.0);
			$table->float('meal_2')->default(0.0);
			$table->float('meal_3')->default(0.0);
			$table->float('meal_4')->default(0.0);
			$table->float('meal_5')->default(0.0);
			$table->integer('current_status')->default(1);
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
		Schema::drop('meal_scores');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusBet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //Création table des types de pari bonus
        Schema::create('bet_bonus_type', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('label', 255);
            $table->timestamp('date');

            $table->string('trigger_data_type', 255);
            $table->string('trigger_data_id', 255);
            $table->string('trigger_condition', 255);
            $table->integer('trigger_points');
        });

        //Création table des pari bonus
        Schema::create('bet_bonus', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('bbt_id')->unsigned();
            $table->integer('team_id')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('bbt_id')->references('id')->on('bet_bonus_type');
            $table->foreign('team_id')->references('id')->on('team');

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
        Schema::dropIfExists('bet_bonus');
        Schema::dropIfExists('bet_bonus_type');
	}

}

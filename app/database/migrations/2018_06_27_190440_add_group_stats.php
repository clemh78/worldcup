<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupStats extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //Modification table group
        Schema::table('team', function($table)
        {
            $table->integer('games_played')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goals_diff')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('team', function($table)
        {
            $table->dropColumn('games_played');
            $table->dropColumn('wins');
            $table->dropColumn('draws');
            $table->dropColumn('losses');
            $table->dropColumn('goals_for');
            $table->dropColumn('goals_against');
            $table->dropColumn('goals_diff');
        });
	}

}

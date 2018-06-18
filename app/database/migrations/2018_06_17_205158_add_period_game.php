<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeriodGame extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //Modification table game
        Schema::table('game', function($table)
        {
            $table->integer('period')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        //Modification table game
        Schema::table('game', function($table)
        {
            $table->dropColumn('period');
        });
	}

}

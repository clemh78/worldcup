<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusGame extends Migration {

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
            $table->string('status')->default("future");
            $table->dropColumn('finished');

            $table->dropColumn('minute');

            $table->string('time')->nullable();
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
            $table->dropColumn('status');
            $table->dropColumn('time');
            $table->string('minute')->nullable();
            $table->boolean('finished')->default(0);
        });
	}

}

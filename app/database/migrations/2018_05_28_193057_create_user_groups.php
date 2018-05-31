<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        //Création table des salon de joueurs
        Schema::create('room', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('code', 10);
        });

        //Création table des liens entre les utilisateurs et les salons
        Schema::create('room_user', function($table)
        {
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('room_id')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('room_id')->references('id')->on('room');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('users_rooms');
        Schema::dropIfExists('rooms');
	}

}

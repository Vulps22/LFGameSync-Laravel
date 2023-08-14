<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		//create a migration for games storing the game name and the game id
		Schema::create('games', function (Blueprint $table) {
			$table->id();
			$table->string('game_id');
			$table->string('name');
			$table->string('image_url');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		//
	}
};

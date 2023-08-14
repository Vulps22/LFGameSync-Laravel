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

		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('discord_id')->nullable()->unique();
			$table->string('discord_name');
			$table->string('discord_access_token')->nullable();
            $table->timestamp('discord_token_expires')->nullable();
            $table->string('discord_refresh_token')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};

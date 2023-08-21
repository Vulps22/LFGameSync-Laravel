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
		Schema::create('discord_server_users', function (Blueprint $table) {
			$table->id();
			$table->string('server_id');
			$table->string('user_id');
			$table->boolean('share_library')->default(false);
			$table->boolean('should_delete')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('discord_server_user');
	}
};

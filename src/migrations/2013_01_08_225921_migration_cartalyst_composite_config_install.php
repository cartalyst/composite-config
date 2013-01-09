<?php

use Illuminate\Database\Migrations\Migration;

class MigrationCartalystCompositeConfigInstall extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('config', function($table)
		{
			$table->string('environment');
			$table->string('group');
			$table->string('namespace')->nullable();
			$table->string('item');
			$table->text('value')->nullable();

			$table->unique(array('environment', 'group', 'namespace'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('config');
	}

}
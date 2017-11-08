<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintMultipleToPastPapers extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'past_papers', function ( Blueprint $table ) {
			$table->unique( [ 'name', 'semester', 'resource_type' ], 'unique_paper' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'past_papers', function ( Blueprint $table ) {
			$table->dropUnique( 'unique_paper' );
		} );
	}
}


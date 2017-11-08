<?php

use Illuminate\Database\Seeder;

class PastPapersDatabaseSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		factory( App\PastPaper::class, 100 )->create();
	}
}

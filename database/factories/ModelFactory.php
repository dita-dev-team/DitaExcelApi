<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Unit::class, function (Faker\Generator $faker) {
    static $password;

    $names = ['ACS-111', 'ACS-222', 'ACS-333', 'ACS-444', 'ACS-555'];
    $sections = ['A', 'B', 'C', 'D', 'E'];
    $units = array_map(function($name, $section) {
        return $name .  $section;
    }, $names, $sections);
    return [
        'name' => $faker->randomElement($units),
        'room' => 'ROOM101',
        'date' => $faker->dateTime,
        'shift' => $faker->randomElement(['athi', 'day', 'evening']),
    ];
});

$factory->define( App\PastPaper::class, function ( Faker\Generator $faker ) {
	$semesters = array(
		'jan 2016',
		'may 2016',
		'august 2016',
		'jan 2017',
		'may 2017',
		'august 2017',
	);
	$name      = $faker->randomElement( $array =
			array( "ACS-", "MIS-", "COM-", "ART-", "ENG-", "HPE-", "BIL-", "MAT-", "PHY-", "PHL-" ) )
	             . $faker->randomElement(
			$array = array( "111", "113", "112", "114", "115", "116", "211", "212", "213", "300" ) );

	return [
		'name'          => $name,
		'resource_type' => $faker->randomElement( array( 'cat', 'assignment', 'exam' ) ),
		'semester'      => ucfirst( $faker->randomElement( $semesters ) ),
		'file'          => $faker->word
	];
} );
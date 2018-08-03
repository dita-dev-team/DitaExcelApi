<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class BaseControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    //use WithoutMiddleware;
    use DatabaseMigrations;


    public function testFetch()
    {
        $units = factory(\App\Unit::class, 100)->create();

	    $this->get( "/api/v1/units?names=" . $units[0]->name . "," . $units[1]->name . "&shift=athi" )
	         ->assertSuccessful()
	         ->assertJsonStructure( [ 'results' ] )
	         ->assertJsonFragment( [
                'name' => $units[0]->name
            ]);
    }
}

<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;

class FileHandlerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use WithoutMiddleware;
    use DatabaseMigrations;

	public function testFileUploadDownload()
    {
        $faker = \Faker\Factory::create();

        $file = new UploadedFile(storage_path('testing/image.jpg'), 'image.jpg', null, filesize(storage_path('testing/image.jpg')), null, true);

	    $this->json( 'GET', 'api/v1/types?all=true' )->assertSuccessful();

	    $this->json( 'GET', 'api/v1/files?all=true' )
	         ->assertSuccessful()
	         ->assertJsonStructure( [ 'results' ] );


        $this->json("POST",'api/v1/files',
            ["file" => $file ])
             ->assertStatus( 400 );

	    $file     = new UploadedFile( storage_path( 'testing/file.pdf' ), 'file.pdf', null, filesize( storage_path( 'testing/file.pdf' ) ), null, true );
	    $response = $this->json( "POST", 'api/v1/files',
		    [ "file" => $file ] )
	                     ->assertSuccessful();
	    $data     = $response->decodeResponseJson();
	    $this->json( 'GET', 'file/type/details/' . $data['filetype'] )
	         ->assertJsonStructure( [ 'checksum' ] )
	         ->assertJson( [ 'filetype' => 'pdf' ] );
	    $response = $this->json( 'GET', 'file/type/' . $data['filetype'] )
	                     ->assertHeader( 'content-type', 'application/pdf' );
	    $this->json( 'GET', 'file/name/details/' . $data['file_name'] )
	         ->assertJsonStructure( [ 'checksum' ] )
	         ->assertJson( [ 'filetype' => 'pdf' ] );
	    $this->call( 'GET', 'file/name/' . $data['file_name'] )
	         ->assertHeader( 'content-type', 'application/pdf' );
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', function () {
    $url = getenv('BASE_URL');
    return view('upload', ['url' => URL::to('/')]);
});

Route::get('/pastpapers/upload', 'PastPaperController@create');
Route::get( '/pastpapers', 'PastPaperController@index' );

Route::get( 'file/{fetch_type}/{identifier}', 'FileController@retrieveFile' );
Route::get( 'file/{fetch_type}/details/{identifier}', 'FileController@retrieveFileDetails' );
Route::group(['prefix' => 'api/v1'], function () {

    Route::get('{table}/{id?}', 'BaseController@issueGetRequest');

    //File and File Types
    Route::post('files', 'FileController@saveFile');
    Route::post('files/db', 'FileController@saveFileToDB');

});

Route::group( [ 'prefix' => 'api/v2' ], function () {
	// Past papers
	Route::get( 'papers', 'PastPaperController@getAll' );
	Route::post( 'papers', 'PastPaperController@addPaper' );
} );


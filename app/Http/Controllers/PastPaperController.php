<?php

namespace App\Http\Controllers;

use App\PastPaper;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PastPaperController extends Controller {
	public function index() {
		return view( 'view_past_papers' );
	}

	public function create() {
		return view( 'upload_resources' );
	}

	public function getAll() {
		return PastPaper::paginate( 20 );
	}

	public function addPaper( Request $request ) {
		Log::info( 'PASS1' );
		$this->validate( $request, [
			'name'          => 'required',
			'resource_type' => 'required',
			'semester'      => 'required',
			'file'          => 'required|max:8000'
		] );

		Log::info( 'PASS2' );

		$ext = $request->file( 'file' )->getClientOriginalExtension();

		if ( $ext != 'pdf' ) {
			return response()->json( 'Bad request (Invalid file)', 400 );
		}

		$resource         = $request->file( 'file' );
		$checksum         = hash_file( 'md5', $resource->getRealPath() );
		$fileType         = $request->file( 'file' )->getClientOriginalExtension();
		$originalFilename = $request->file( 'file' )->getClientOriginalName();
		$file             = FileUtilities::saveFile( $originalFilename, $resource, $checksum, $fileType );

		if ( $file == null ) {
			return response()->json( 'Unable to save document', 500 );
		}

		$input = $request->all();

		$paper = new PastPaper( [
			'name'          => $input['name'],
			'resource_type' => $input['resource_type'],
			'semester'      => $input['semester'],
			'file'          => $file->file_name
		] );
		$paper->saveOrFail();

		return response()->json( 'File saved successfully', 200 );
	}
}

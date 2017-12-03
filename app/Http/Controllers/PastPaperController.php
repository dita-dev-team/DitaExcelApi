<?php

namespace App\Http\Controllers;

use App\PastPaper;
use App\Utilities\FileUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PastPaperController extends Controller {
	public function index() {
		return view( 'view_past_papers' );
	}

	public function create() {
		return view( 'upload_resources' );
	}

	public function getAll( Request $request ) {
		$filter = $request->input( 'filter' );
		if ( ! empty( $filter ) ) {
			return PastPaper::where( 'name', 'LIKE', "%$filter%" )->paginate( 20 );
		}
		return PastPaper::paginate( 20 );
	}

	public function addPaper( Request $request ) {
		Log::info( 'PASS1' );
		$rules    = [
			'name'          => 'required',
			'resource_type' => 'required',
			'semester'      => 'required',
			'file'          => 'required|max:8196'
		];
		$messages = [
			'name.required'          => 'The name field is required',
			'semester.required'      => 'The semester field is required',
			'resource_type.required' => 'The resource_type field is required',
			'file.required'          => 'The file field is required',
			'file.max'               => 'The max file size is 8196 kilobytes',
		];

		$validator = Validator::make( $request->all(), $rules, $messages );
		if ( $validator->fails() ) {
			return response()->json( $validator->messages(), 422 );
		}
		//$this->validate($request, $rules, $messages);

		Log::info( 'PASS2' );

		$ext = $request->file( 'file' )->getClientOriginalExtension();

		Log::info( $ext );

		if ( ! ( $ext == 'pdf' || $ext == 'doc' || $ext == 'docx' ) ) {
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


	/*protected function buildFailedValidationResponse( Request $request, array $errors ) {
		if ($request->expectsJson()) {
			return new JsonResponse($errors, 422);
		}

		return redirect()->to('/');
	}*/
}

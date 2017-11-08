<?php
/**
 * Created by PhpStorm.
 * User: black
 * Date: 08/05/2017
 * Time: 09:43
 */


namespace App\Utilities;


use App\File;
use Illuminate\Support\Facades\Storage;

class FileUtilities
{
	public static function getFileByType( $filetype )
    {
        $result = null;
        //Get the most recent File with the same extension
	    $fileDetails = File::where( 'filetype', $filetype )->orderBy( 'created_at', 'desc' )->first();

        $file = $fileDetails->file_name;

	    return self::getFile( $file );
    }

	public static function getFileByName( $name ) {
		$result = null;
		//Get the most recent File with the same extension
		$fileDetails = File::where( 'file_name', $name )->first();

		$file = $fileDetails->file_name;

		return self::getFile( $file );
	}

	public static function getFile( $file ) {
		$result = null;

		if ( $file != null ) {
			$path   = storage_path() . "/app/files/" . $file;
			$result = file_exists( $path ) ? $path : null;
		}

		return $result;
    }

	public static function getDetailsByType( $filetype )
    {
	    return File::where( 'filetype', $filetype )->orderBy( 'created_at', 'desc' )->first();
    }

	public static function getDetailsByName( $name ) {
		return File::where( 'file_name', $name )->first();
	}

    public static function storeFile($resource, $filename)
    {
        //Store File
        $path = Storage::putFileAs(
            "files/", $resource, $filename
        );
    }

    public static function storeFileCloud($filename, $resource)
    {
        Storage::disk('google')->write($filename, $resource);
    }

	public static function getFileCloudByType( $filetype )
    {
        $result = null;
        //Get the most recent File with the same extension
	    $fileDetails = File::where( 'filetype', $filetype )->orderBy( 'created_at', 'desc' )->first();

        $filename = $fileDetails->file_name;

	    return self::getFileCloud( $filename );
    }

	public static function getFileCloudByName( $name ) {
		$result = null;
		//Get the most recent File with the same extension
		$fileDetails = File::where( 'file_name', $name )->first();

		$filename = $fileDetails->file_name;

		return self::getFileCloud( $filename );
	}

	public static function getFileCloud( $filename ) {
		$result = null;

		$listContents = Storage::disk( 'google' )->listContents();
		$details      = self::getFileCloudDetails( $listContents, $filename );
		$data         = Storage::disk( 'google' )->get( $details['path'] );
		$dir          = storage_path() . '/app/files/';
		if ( ! file_exists( $dir ) ) {
			\Illuminate\Support\Facades\File::makeDirectory( $dir, 0775, true );
		}

		$path = storage_path() . '/app/files/' . 'temp_' . $filename;
		\Illuminate\Support\Facades\File::put( $path, $data );

		return $path;
    }

	public static function saveFile( $originalFilename, $resource, $checksum, $filetype ) {
		//Append Unique Identifier File Name
		$now = self::fileCreationDate();
		//Remove Special Characters
		$tmpFilename = str_replace( ' ', '_', $originalFilename );
		//Concatenate filename and date
		$filename = $now . '_' . $tmpFilename;

		//Store File
		if ( env( 'APP_ENV', 'local' ) == 'production' ) {
			FileUtilities::storeFileCloud( $filename, \Illuminate\Support\Facades\File::get( $resource->getRealPath() ) );
		} else {
			FileUtilities::storeFile( $resource, $filename );
		}

		$file = self::storeFileMetadataWithChecksum( $filename, $filetype, $checksum );

		return $file;
	}

	private static function storeFileMetadataWithChecksum( $filename, $fileType, $checksum ) {
		$file = new File( [
			'file_name' => $filename,
			'checksum'  => $checksum,
			'filetype'  => $fileType
		] );
		$file->save();

		return $file;
	}

	private static function getFileCloudDetails( Array $array, $filename )
    {

        foreach ($array as $subarray) {
            $filename2 = $subarray['filename'] . '.' . $subarray['extension'];
            if (strcmp($filename2, $filename) == 0) {
                return $subarray;
            }
        }

        return null;
    }

	public static function fileCreationDate() {
		$now = \Carbon\Carbon::now()->toDateTimeString();
		$now = str_replace( ':', '_', $now );
		$now = str_replace( '-', '_', $now );

		return $now;
	}
}

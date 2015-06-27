<?php
/* creates a compressed zip file */
function create_zip( $files = array(), $destination = '', $overwrite = false, $keepDirStructure = true ) {
	//if the zip file already exists and overwrite is false, return false
	if( file_exists($destination) && !$overwrite ) { echo '1'; return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            echo '2';
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
            $fileName = $file;
            if( $keepDirStructure == false ){
                $fileName = basename($fileName);
            }
			$zip->addFile($file, $fileName);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else{
            echo '3';
		return false;
	}
}

function list_zip_files( $fileName, $bBaseNameOnly = false ){
    $za = new ZipArchive(); 
    $aFiles = array();
    $za->open($fileName); 

    for( $i = 0; $i < $za->numFiles; $i++ ){ 
        $stat = $za->statIndex( $i ); 
        if( $bBaseNameOnly ){
            array_push( $aFiles, basename($stat['name']) ); 
        }else{
            array_push( $aFiles, $stat['name'] ); 
        }
    } 
    return $aFiles;
}

function getZipErrorCodeStr( $code ){
    switch($code){
        case ZIPARCHIVE::ER_EXISTS: return 'File already exists.';
        case ZIPARCHIVE::ER_INCONS: return 'Zip archive inconsistent.';
        case ZIPARCHIVE::ER_INVAL: return 'Invalid argument.';
        case ZIPARCHIVE::ER_MEMORY: return 'Malloc failure.';
        case ZIPARCHIVE::ER_NOENT: return 'No such file.';
        case ZIPARCHIVE::ER_NOZIP: return 'Not a zip archive.';
        case ZIPARCHIVE::ER_OPEN: return 'Can\'t open file.';
        case ZIPARCHIVE::ER_READ: return 'Read error.';
        case ZIPARCHIVE::ER_SEEK: return 'Seek error.';
        default: return 'Unknown code: ' . $code;
    }
    return 'Weird error: ' . $code;
}
?>

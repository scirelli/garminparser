<?php

	function gfe($filename) {
		$pathinfo = pathinfo($filename);
		return $pathinfo['extension'];
	}

    function getDirFiles( $dir = './', $fileExt = '' ){
        $aRtn = array();
        $pos = strrchr( $dir, DIRECTORY_SEPARATOR );
        if( $pos === false ) $dir .= DIRECTORY_SEPARATOR;
        // Open a known directory, and proceed to read its contents
        if (is_dir($dir)){
            if ($dirHandle = opendir($dir)){
                while( ($file = readdir($dirHandle)) !== false ){
                    if( filetype($dir . $file) != 'dir' ){
                        if( $fileExt ){
                            if( $fileExt == gfe($file) )
                                array_push($aRtn,$file);
                            continue;
                        }
                        array_push($aRtn,$file);
                    }
                }
                closedir($dirHandle);
            }
        }
        return $aRtn;
    }
?>

<?php
/*
Plan:
    * Upload a zip or Garmin export
    * Extract/copy file(s) to data folder
    * foreach file convert it to gpx and move it to converted folder
    * Zip converted folder 
    * Remove converted files
    * Remove extracted files
*/
    require_once('../lib/simpleFileLog.php');
    require_once('../lib/fileZipper.php');
    require_once('../setup.php');
    require_once('../lib/getDirFiles.php');
    require_once('../lib/checkSet.php');
    require_once('../lib/GarminConverter.php');
    require_once('../lib/TCXFileSplitter.php');
    require_once('../lib/GPSVisualizerFileDownload.php');
    require_once('../db/dbo/user.php');
    require_once('../db/dbo/converted_files.php');
    require_once('../php/RemoveDups.php');

    header('Content-type: application/json');

    $tmpFileName = isset($_FILES["ffFile"]) ? $_FILES["ffFile"]["tmp_name"] : '';
    $sessionID = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '';
    $response = array( 
        'uid'           => $sessionID,
        'action'        => '/garmin/ready.php',
        'ffGPXFileName' => $tmpFileName,
        'errors'        => array(),
        'msgs'          => array(),
        'convertedFile' => ''
    );

    function output( $msg ){
        echo json_encode( $msg );
    }

    logToFile( 'Begin conversion.' );
//goto cur;
    //---- File Validation ---------------------------
    //Check to see if a files was uploadded
    if( isset($_FILES["ffFile"]) == false || $_FILES["ffFile"]["error"] > 0){
        logToFile( 'Error uploading file. No file uploaded.' );
        array_push($response['errors'],"Error: uploading file");
        output($response);
        exit(0);
    }else{
        array_push($response['msgs'],
            "Upload: "    . $_FILES["ffFile"]["name"],
            "Type: "      . $_FILES["ffFile"]["type"],
            "Size: "      . ($_FILES["ffFile"]["size"] / 1024) . " kB",
            "Stored in: " . $_FILES["ffFile"]["tmp_name"]
        );
    }

    //Check to make sure it's a zip file. If not then Zip files from upload
    if( $_FILES['ffFile']['type'] != 'application/x-zip-compressed') {
        logToFile( 'Not a standard zip file. Will now compress your file.' );
        array_push($response['msgs'],"Not a standard zip file. Will now compress your file.");
        $zipFileName = $_FILES["ffFile"]["name"] . '_'. $sessionID . '.zip';
        $zipFile = $paths['dataDir'] . $zipFileName;
        create_zip( array($_FILES["ffFile"]["tmp_name"]), $zipFile, true, false);
    }else{
        logToFile( 'A zip file was uploaded, using that.' );
        $zipFileName = $_FILES["ffFile"]["tmp_name"];
    }
    //--------------------------------------------------

    //---- Split the zip ----
    array_push($response['msgs'],'Splitting file.');
    logToFile( 'Splitting file.' );
    $splitter = new TCXFileSplitter();
    $url = $splitter->splitTCX( $zipFile );
    //Download the split archive
    logToFile( 'Downloading split archive.' );
    array_push($response['msgs'],'Downloading split archive.');
    $vfd  = new GPSVisualizerFileDownload($paths['dataDir']);
    $url = $vfd->save( $url );
    //------------------------

    //---- Check for dup files here using user accounts ----
    logToFile( 'Removing dups.' );
    array_push($response['msgs'],'Removing dups.');
    $zipURL = $url;
    $user = new DataObjects_User($sessionID );
    $dups = new GPSRemoveDups( $paths['trackDir'], $user ); 
    $zipURL = $dups->removeDups($zipURL);
    //------------------------------------------------------

//cur:
    $trackDir     = $paths['trackDir'] . $user->user_id . DIRECTORY_SEPARATOR;
    $dataDir      = $paths['dataDir'];
    $convertedDir = $paths['convertedDir'];
//goto cur2;
    if( $zipURL ){
        $zip = new ZipArchive();
        $zip->open($zipURL);
        $zip->extractTo( $trackDir );
        $zip->close();

        $aFiles = getDirFiles( $trackDir, 'tcx' ); 
        $cvtr = new GarminConverter();
        $vfd  = new GPSVisualizerFileDownload($trackDir);
        logToFile( 'Converting located in - ' . $trackDir );
        for( $i=0,$l=count($aFiles); $i<$l; $i++ ){
            logToFile( "\tConverting file - " . $aFiles[$i] . "\n" );
            $url  = $cvtr->convert( $trackDir . $aFiles[$i] );
            if( $url ){
                $vfd->saveAs( $aFiles[$i] . '.gpx', $url );
                logToFile( "\t\tSaving file - " . $aFiles[$i] . "\n");
            }
        }
//cur2:
        $aGPXFiles    = getDirFiles($trackDir, 'gpx');
        $aTCXFiles    = getDirFiles($trackDir, 'tcx');
        $aTCXFiles    = array_merge($aTCXFiles, getDirFiles($dataDir, 'tcx'));
        $aZipFiles    = getDirFiles($dataDir, 'zip');
        $aDTCXFiles   = getDirFiles($dataDir, 'tcx');
        $aTrackFiles  = getDirFiles($trackDir, 'zip' );
        $allTempFiles = array_merge( $aGPXFiles, $aTCXFiles, $aZipFiles );
        $zipFileName  = basename($tmpFileName) . '.zip';
        $zipFile      = $convertedDir . $zipFileName;
        $tmpArray = array();
        for( $i=0,$l=count($aGPXFiles),$dir=$trackDir; $i<$l; $i++ ){
            $tmpArray[] = $dir . $aGPXFiles[$i];
        }
        $suc = create_zip( $tmpArray, $zipFile, true, false);
        if( $suc ){
            $response['convertedFile'] = 'data/converted/' .$user->user_id . '/' . $zipFileName;
        }else{
            logToFile( 'Error zipping.' );
            array_push($response['errors'],'Error zipping.');
            array_push($response['errors'], $zipFile);
            array_push($response['errors'],implode(',',$tmpArray));
        }
        array_push($response['msgs'],'Removing all temp files...');
        logToFile( 'Removing all temp files...' );
        $allTempFiles = array_merge($aGPXFiles, $aTCXFiles, $aTrackFiles );
        logToFile('Removing all files from - ' . $trackDir );
        for( $i=0,$l=count($allTempFiles),$dir=$trackDir; $i<$l; $i++ ){
            $file = $allTempFiles[$i];
            if(file_exists($dir.$file)){
                logToFile("\tRemoving file - " . $file );
                @unlink($dir.$file);
            }
        }
        $allTempFiles = array_merge($aZipFiles, $aDTCXFiles );
        logToFile('Removing all files from - ' . $dataDir);
        for( $i=0,$l=count($allTempFiles),$dir=$dataDir; $i<$l; $i++ ){
            $file = $allTempFiles[$i];
            if(file_exists($dir.$file)){
                logToFile("\tRemoving file - " . $file );
                @unlink($dir.$file);
            }
        }
    }else{
        logToFile( 'Complete. Nothing to do.' );
        array_push($response['msgs'],'Complete. Nothing to do.');
    }

    logToFile( '***************************************** Conversion complete ************************************' );
    output( $response );
/*
    $url = 'http://www.gpsvisualizer.com/forerunner/split';
    array(
        'include_invalid'   => '0',
        'uploaded_file'     => '@',
        'submit'            => 'Submit',
        'include_trackless' => '0'
    );
    $postArr = array(
        'convert_format'       => 'gpx',
        'submitted'            => 'Convert',
        'data'                 => 'name,desc,latitude,longitude',
        'force_type'           => '',
        'remote_data'          => 'http://www.cirelli.org/garmin/data/Tracks/20090522-225227.tcx',
        'convert_delimiter'    => 'tab',
        'units'                => 'metric',
        'convert_add_speed'    => '1',
        'convert_add_course'   => '1',
        'convert_add_slope'    => '1',
        'convert_add_distance' => '1',
        'convert_add_pace'     => '1',
        'vmg_point'            => '',
        'add_elevation'        => '0',
        'trk_stats'            => '1',
        'trk_simplify'         => '',
        'tickmark_interval'    => '',
        'trk_as_wpt'           => '0',
        'trk_as_wpt_name'      => '',
        'trk_as_wpt_desc'      => '',
        'show_wpt'             => '3',
        'synthesize_name'      => '',
        'synthesize_desc'      => '',
        'reference_point'      => '',
        'reference_point_name' => '',
        'wpt_interpolate'      => '1',
        'wpt_interpolate_offset'    => '',
        'time_offset'               => '',
        'utm_output'                => '0',
        'moving_average'            => '1',
        'frequency_count'           => 'none',
        'special'                   => '',
        'connect_segments'          => '0',
        'cumulative_distance'       => '0',
        'wifi_mode'                 => '3',
        'forerunner_laps'           => '1',
        'trk_preserve_attr'         => '1',
        'wpt_preserve_attr'         => '1',
        'convert_routes'            => 't_aw',
        'padding'                   => '10',
        'wpt_name_filter'           => '',
        'wpt_desc_filter'           => '',
        'convert_add_climb'         => '',
        'convert_add_slope_degrees' => ''
    );
    //$path = 'data/Tracks/test.gpx';
    //$fp = fopen($path, 'w');
    //$ch = curl_init($url);
    //curl_setopt($ch, CURLOPT_FILE, $fp);
    //$data = curl_exec($ch);
    //curl_close($ch);
    //fclose($fp);

    //C:\\Users\\steve.cirelli\\My_Programs\\MyPrograms\\Webdesign\\htdocs\\garmin\\data\\Tracks\\
    //$ch = curl_init();
    //curl_setopt( $ch, CURLOPT_URL, $url );
    //curl_setopt( $ch, CURLOPT_POST, 1 );
    //curl_setopt( $ch, CURLOPT_POSTFIELDS, $postArr );
    //curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    //$response = curl_exec($ch);
    //curl_close($ch);
exit(0);
    $response = file_get_contents('./doc/test.htm', false );
    $doc = new DomDocument();
    @$doc->loadHTML( $response );
    $a = $doc->getElementsByTagName('a');
    echo '<pre>';
    foreach( $a as $anchor ){
        $value = $anchor->getAttribute('href');
        if( strpos($value,'download/convert') !== false ){
            $a = $value;
            if( strpos( $value, 'http://www.gpsvisualizer.com' ) === false ){
                $value = 'http://www.gpsvisualizer.com' . $value;
            }
            break;
        }
    }
    var_dump($value);
    echo '</pre>';
*/
?>

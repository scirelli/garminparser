<html>
    <body>
        <?php
            require_once('./lib/fileZipper.php');
            require_once('./setup.php');
            require_once('./lib/getDirFiles.php');
            require_once('./lib/checkSet.php');
            require_once('./lib/GarminConverter.php');
            require_once('./lib/TCXFileSplitter.php');
            require_once('./lib/GPSVisualizerFileDownload.php');
            
            $ffGPXFileName = checkSet('ffGPXFileName', microtime(true), false); 
            /*
            //Start Converting
            $files = getDirFiles( './data/Tracks', 'tcx' ); 
            $cvtr = new GarminConverter();
            $vfd  = new GPSVisualizerFileDownload();
            echo '<table><thead><tr><th>Count</th><th>Files</th></tr></thead><tbody>';
            for( $i=0,$l=count($files); $i<$l; $i++ ){
                echo '<tr><td>' . $i . '</td><td>';
                echo 'Converting: ' . $files[$i] . '<br/>';
                $url  = $cvtr->convert( $paths['trackDir'] . $files[$i] );
                if( $url ){
                    $vfd->saveAs( $files[$i] . '.gpx', $url );
                    echo 'Saved: ' . $files[$i] . '.gpx';
                }else{
                    echo $url;
                }
                echo '</td></tr>';
                flush();
            }
            echo '</tbody></table><br/>';
            */
            echo 'Done!';    
            flush();
            $aGPXFiles = getDirFiles($paths['trackDir'], 'gpx');
            $aTCXFiles = getDirFiles($paths['trackDir'], 'tcx');
            $aTCXFiles = array_merge( $aTCXFiles, getDirFiles($paths['dataDir'], 'tcx'));
            $aZipFiles = getDirFiles($paths['dataDir'], 'zip');
            $allTempFiles = array_merge($aGPXFiles, $aTCXFiles, $aZipFiles);

            echo 'Compressing converted files.<br/>';
            $zipFileName = $ffGPXFileName  . '.zip';
            $zipFile = $paths['convertedDir'] . $zipFileName;
            $tmpArray = array();
            for( $i=0,$l=count($aGPXFiles),$dir=$paths['trackDir']; $i<$l; $i++ ){
                $tmpArray[] = $dir . $aGPXFiles[$i];
            }
            $suc = create_zip( $tmpArray, $zipFile, true, false);
            if( $suc ){
                echo 'Your file is ready to <a href="data/converted/' . $zipFileName . '">download</a>.<br/>';
            }else{
                echo 'Error zipping.<pre>';
                var_dump($tmpArray);
                var_dump($zipFile);
                echo '</pre>';
                exit(0);
            }

            echo 'Removing all temp files...<br/>';
            flush();
            $allTempFiles = array_merge($aGPXFiles, $aTCXFiles, $aZipFiles);
            for( $i=0,$l=count($allTempFiles),$dir=$paths['trackDir']; $i<$l; $i++ ){
                $file = $allTempFiles[$i];
                if(file_exists($dir.$file)) @unlink($dir.$file);
            }
            echo 'Done!<br/> <a href="/garmin">Convert another</a>.';
        ?>
    </body>
</html>

<?php
    class GPSRemoveDups{
        private $extractTo = './';
        private $userId    = '';
        private $oUser     = null;
        private $aExtractedFileNames = array();

        function __construct( $extractTo, $oUser ){
            if( isset($extractTo) == false || isset($oUser) == false ){
                throw new Exception('GPSRemoveDups params 1 and 2 must be set.' . $extractTo . ' ' . $userId );
            }
            $this->setUser( $oUser );
            $this->userId    = $oUser->user_id;
            $this->setExtractTo( $extractTo );
        }

        public function removeDups( $zipFile ){
            $this->extractZip( $zipFile );
            $this->aExtractedFileNames = $this->getFileNames();
            $this->createTempTable();
            $this->fillTempTable();
            $rtn = $this->zipNotDups();
            $this->insertNotDups();
            $this->cleanUp();
            if( $rtn ){
                return $this->extractTo . $this->userId . '.zip';
            }else{
                return false;
            }
        }
         
        public function setUser( $oUser ){
            if( $oUser instanceof DataObjects_User ){
                $this->oUser = $oUser;
            }else{
                throw( new Exception('GPSRemoveDups param 2 must be of type DataObjects_User.') );
            }
        }

        public function setExtractTo( $extractTo ){
            $this->extractTo = $extractTo . $this->userId . '\\';
        }

        private function extractZip( $zipFile ){
            $zip = new ZipArchive;
            if( $zip->open($zipFile) === TRUE ){
                $zip->extractTo($this->extractTo);
                $zip->close();
            }
        }
        
        private function zipNotDups(){
            $aFiles = array();
            $results = $this->getNotDups();
            while( $row = mysql_fetch_assoc($results) ){
                array_push( $aFiles, $this->extractTo . $row['file_name'] );
            }
            return create_zip( $aFiles, $this->extractTo . $this->userId . '.zip', true, false );
        }

        private function getFileNames(){
            return getDirFiles( $this->extractTo, 'tcx' );
        }

        private function removeFiles(){
            for( $i=0,$l=count($this->aExtractedFileNames),$dir=$this->extractTo; $i<$l; $i++ ){
                $file = $this->aExtractedFileNames[$i];
                if(file_exists($dir.$file)) @unlink($dir.$file);
            }
        }

        private function createTempTable(){
            $sql = <<<EOT
                CREATE TEMPORARY TABLE %s (
                  id int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY  (id),
                  file_name VARCHAR(256) NOT NULL,
                  file_md5  CHAR(32) NOT NULL,
                  dup  BOOLEAN NOT NULL DEFAULT 0,
                  INDEX(file_md5)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
EOT;
            $sql = sprintf($sql, $this->userId); 
            return mysql_query( $sql );
        }

        private function fillTempTable(){
            $bQ = true;
            $sql = "INSERT INTO " . $this->userId . " (file_name, file_md5) VALUES ('%s', '%s')";
            mysql_query("BEGIN");
            
            for( $i=0,$l=count($this->aExtractedFileNames),$dir=$this->extractTo, $tmp=''; $i<$l; $i++ ){
                $file = $this->aExtractedFileNames[$i];
                $md5  = '';
                if(file_exists($dir.$file)){
                    $md5 = md5_file($dir.$file);
                    $tmp = sprintf($sql, $file, $md5 );
                    $bQ = $bQ && mysql_query($tmp);
                }
            }

            if($bQ) {
                mysql_query("COMMIT");
            } else {        
                mysql_query("ROLLBACK");
                return false;
            }
        }

        private function insertNotDups(){
            $sql = <<<EOT
            INSERT INTO Converted_Files ( file_name, file_md5, user_id )
                SELECT tmp.file_name, tmp.file_md5, '%s' AS user_id
                FROM %s tmp
                    LEFT JOIN Converted_Files cf ON cf.file_md5 = tmp.file_md5
                WHERE cf.file_md5 IS NULL
EOT;
            $sql = sprintf($sql, $this->oUser->id, $this->userId); 
            return mysql_query( $sql );
        }
        
        private function getNotDups(){
            $sql = <<<EOT
                SELECT tmp.file_name, tmp.file_md5, '%s' AS user_id
                FROM %s tmp
                    LEFT JOIN Converted_Files cf ON cf.file_md5 = tmp.file_md5
                WHERE cf.file_md5 IS NULL
EOT;
            $sql = sprintf($sql, $this->oUser->id, $this->userId); 
            return mysql_query( $sql );
        }

        //Can't drop temp tables
        private function dropTempTable(){
            $sql = <<<EOT
            DROP TABLE %s
EOT;
            
            $sql = sprintf($sql, $this->userId); 
            return mysql_query( $sql );
        }

        private function cleanUp(){
            //$this->dropTempTable();
            $this->removeFiles();
        }
    }
?>

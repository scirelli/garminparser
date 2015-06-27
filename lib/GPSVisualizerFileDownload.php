<?php
    class GPSVisualizerFileDownload{
        private $downloadDir = '';

        function __construct( $downloadDir = 'data\\Tracks\\'){
            $this->setDlDir( $downloadDir );
        }

        public function setDlDir( $dir ){
            $pos = strrchr( $dir, DIRECTORY_SEPARATOR );
            if( $pos === false ) $dir .= DIRECTORY_SEPARATOR;
            $this->downloadDir = $dir;
        }

        public function getFileFromURL( $url ){
            $url = explode('/', $url);
            return $url[count($url)-1];
        }

        public function save( $url ){
            return $this->saveAs( $this->getFileFromURL($url), $url );
        }
        public function saveAs( $fileName, $url ){
            $filepath = $this->downloadDir . $fileName;
            $fp = fopen($filepath, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $data = curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            return $filepath;
        }
    }
?>

<?php
    class TCXFileSplitter{
        private $hCurl     = null;
        private $sBaseURL   = 'http://www.gpsvisualizer.com/forerunner';
        private $sURL      = 'http://www.gpsvisualizer.com/forerunner/split';
        private $aPostData = array(
            'include_invalid'   => '0',
            'uploaded_file'     => '',
            'submit'            => 'Submit',
            'include_trackless' => '0'
        );
        private $sResponse = '';

        function __construct(){
        }
        function __destruct(){
        }

        public function closeCURL(){
            if( $this->hCurl ){
                curl_close($this->hCurl);
                $this->hCurl = null;
            }
        }

        public function initCURL(){
            if( $this->hCurl ){
                $this->closeCURL();
            }
            $this->hCurl = curl_init();
            curl_setopt( $this->hCurl, CURLOPT_URL, $this->sURL );
            curl_setopt( $this->hCurl, CURLOPT_POST, 1 );
            curl_setopt( $this->hCurl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $this->hCurl, CURLOPT_VERBOSE, true );
            curl_setopt( $this->hCurl, CURLOPT_STDERR, fopen('./error.txt', 'a') );
            return $this->hCurl;
        }
        
        public function splitTCX( $filePath ){
            if( file_exists( $filePath ) === false ) throw 'File does not exist';
            $this->initCURL();
            $rtn = $this->splitFile( $filePath );
            $this->sResponse = $rtn;
            $this->closeCURL();
            return $this->parseOutDownloadLink( $rtn );
        }

        public function setConverterURL( $sURL ){
            $this->sURL = sURL;
        }

        public function getResponse(){
            return $this->sResponse;
        }

        private function splitFile( $filePath ){
            $this->aPostData['uploaded_file'] = '@' . $filePath;
            curl_setopt( $this->hCurl, CURLOPT_POSTFIELDS, $this->aPostData );
            set_time_limit(0);
            return curl_exec($this->hCurl);
        }
        
        private function parseOutDownloadLink( $HTMLDoc ){
            $value = '';
            $doc   = new DomDocument();
            $a     = '';

            @$doc->loadHTML( $HTMLDoc );
            $a = $doc->getElementsByTagName('a');
            foreach( $a as $anchor ){
                $value = $anchor->getAttribute('href');
                if( strpos($value,'tmp/Tracks') !== false ){
                    $a = $value;
                    if( strpos( $value, $this->sBaseURL ) === false ){
                        $value = $this->sBaseURL . '/' . $value;
                    }
                    break;
                }
                $value = '';
            }
            return $value;
        }
    }
?>

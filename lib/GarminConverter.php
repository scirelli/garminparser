<?php
    class GarminConverter {
        private $hCurl         = null;
        private $sURL          = 'http://www.gpsvisualizer.com/convert?output';
        private $aPostData     = array(
            'uploaded_file_1'      => '',
            'uploaded_file_2'      => '',
            'uploaded_file_3'      => '',
            'uploaded_file_4'      => '',
            'uploaded_file_5'      => '',
            'uploaded_file_6'      => '',
            'uploaded_file_7'      => '',
            'uploaded_file_8'      => '',
            'uploaded_file_9'      => '',
            'uploaded_file_10'     => '',
            'convert_format'       => 'gpx',
            'submitted'            => 'Convert',
            'data'                 => 'name,desc,latitude,longitude',
            'force_type'           => '',
            'remote_data'          => '',
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
        
        public function convert( $filePath ){
            if( file_exists( $filePath ) === false ) throw new Exception('File does not exist');
            $this->initCURL();
            $rtn = $this->requestGPX( $filePath );
            $this->closeCURL();
            return $this->parseOutDownloadLink( $rtn );
        }

        public function setConverterURL( $sURL ){
            $this->sURL = sURL;
        }

        private function requestGPX( $filePath ){
            $this->aPostData['uploaded_file_1'] = '@' . $filePath;
            curl_setopt( $this->hCurl, CURLOPT_POSTFIELDS, $this->aPostData );

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
                if( strpos($value,'download/convert') !== false ){
                    $a = $value;
                    if( strpos( $value, 'http://www.gpsvisualizer.com' ) === false ){
                        $value = 'http://www.gpsvisualizer.com' . $value;
                    }
                    break;
                }
                $value = '';
            }
            return $value;
        }
    }
?>

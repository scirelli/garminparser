<?PHP
require('iResponseQ.php');

class GeneralResponseQ implements iResponseQ{
    private $msgs   = array();
    private $errors = array();

    public function getNextMsg( $type=iResponseQ::MSG_TYPE_NORMAL ){
        if( $type == iResponseQ::MSG_TYPE_NORMAL ){
            $tmp = current($this->msgs);
            next($this->msgs);
            return $tmp;
        }else{
            $tmp = current($this->errors);
            next($this->errors);
            return $tmp;
        }
    }

    public function pushMsg( $msg, $type=iResponseQ::MSG_TYPE_NORMAL ){
        if( $msg instanceof iResponse ){
            if( $type == iResponseQ::MSG_TYPE_NORMAL )
                $this->msgs[] = $msg;
            else
                $this->errors[] = $msg;
            return sizeof($this->msgs);
        }else
            return false;
    }
}
?>


<?PHP
require('iResponseQPrinter.php');

class XMLResponseQPrinter implements iResponseQPrinter{
    private $queue = null;
    private $xml   = null;

    function __construct( $q=null ){
        if(!$this->setQ( $q ))
            throw new Exception('Could not set queue.');
    }
    public function setQ( $q ){
        if( $q instanceof iResponseQ ){
            $this->queue = $q; 
            return true;
        }
        return false;
    }
    public function generateXML(){
        $this->xml = '<replymsgs>';
        while( $msg = $this->queue->getNextMsg(iResponseQ::MSG_TYPE_NORMAL) ){
            $this->xml .= '<replymsg><![CDATA[' . $msg->toString() . ']]></replymsg>';    
        }
        $this->xml .= '</replymsgs> <errormsgs>';
        while( $msg = $this->queue->getNextMsg(iResponseQ::MSG_TYPE_ERROR) ){
            $this->xml .= '<errormsg><![CDATA[' . $msg->toString() . ']]></errormsg>';    
        }
        $this->xml .= '</errormsgs>';
    }

    public function sprintXML($regen = false){
        if( $regen === true || $this->xml == null ) $this->generateXML();
        return $this->xml;
    }
}
?>


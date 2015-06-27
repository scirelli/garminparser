<?PHP
class TimerSimple implements ITimer{
    private $iStartTime;

    public function __construct(){
        $this->iStartTime = 0;
    }

    public function start(){
        $st                 = microtime(true);
        $this->iStartTime = $st;
        return $st;
    }

    public function stop(){
        $iStop = microtime(true) - $this->iStartTime;
        $this->iStartTime = microtime(true);
        return $iStop;
    }

    public function elapsed(){
        $iStop = microtime(true) - $this->iStartTime;
        return $iStop;
    }

    public function resetTimer(){
        $iStop = microtime(true) - $this->iStartTime;
        $this->iStartTime = microtime(true);
        return $iStop;
    }
}
?>

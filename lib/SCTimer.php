<?PHP
class Timer implements ITimer{
    private $iStartTime;
    private $aStartTime;
    private $aTimes;
    private $sCol1   = 'Item';
    private $sCol2   = 'Time(sec)';
    private $nCount  = 0;
    private $bTiming = false;
    private $nTotal  = 0;

    public function __construct( $sCol1 = 'Item', $sCol2 = 'Time(sec)' ){
        $this->iStartTime = 0;
        $this->aStartTime = array();
        $this->aTimes     = array();
        $this->nCount     = 0;
        $this->bTiming    = false;
        $this->nTotal     = false;
        $this->sCol1      = $sCol1;
        $this->sCol2      = $sCol2;
    }

    public function start( $item = 'Item' ){
        $st                 = microtime(true);
        $this->aStartTime[] = array('time'=>$st, 'count'=>$this->nCount++);//push the most recent start
        $this->aTimes[]     = array($this->sCol1 => $item, $this->sCol2 => -1 );//nCount is an index into this array
        return $st;
    }

    public function stop(){
        $iStop = microtime(true);
        $tmp   = array_pop( $this->aStartTime );
        $st    = $tmp['time'];
        $iStop -= $st;
        $count = $tmp['count'];

        $this->aTimes[$count][$this->sCol2] = $iStop;
        $this->nTotal += $iStop;
        return $iStop;
    }

    public function elapsed(){
        $iStop = microtime(true);
        $tmp   = $this->aStartTime[count($this->aStartTime)] ;
        $st    = $tmp['time'];
        $iStop -= $st;
        return $iStop;
    }

    public function resetTimer(){
        $iStop = microtime(true);
        $count = count($this->aStartTime);
        $tmp   = $this->aStartTime[$count];
        $st    = $tmp['time'];
        $iStop -= $st;
        $this->aStartTime[$count]['time'] = microtime(true);
        return $iStop;
    }

    public function sprintArrayToHTML( $print=false ){
        $this->aTimes[] = array( $this->sCol1 => '<b>Sum</b>', $this->sCol2 => $this->nTotal );
        $aTimer = $this->aTimes;

        $table  = '<table border="1">%s %s %s</table>';
        $tableH = '<thead>%s</thead>';
        $tableB = '<tbody>%s</tbody>';
        $tableF = '<tfoot>%s</tfoot>';
        $thr    =  '<tr>%s</tr>';
        $th     = '';
        $td     = '';

        if( !is_array($aTimer) ){ return 'Not an array'; }
        $aRow = $aTimer[0];
        foreach( $aRow as $key => $value ){
            $th .= '<th>' . $key . '</th>';
        }
        reset($aTimer);
        for( $i=0, $l=count($aTimer); $i<$l; $i++ ){
            $aRow = $aTimer[$i];
            $td .= '<tr>';
            foreach( $aRow as $key => $value ){
                $td .= '<td>' . $value . '</td>';
            }
            $td .= '</tr>';
        }
        $thr    = sprintf($thr, $th);
        $tableH = sprintf($tableH, $thr);
        $tableB = sprintf($tableB, $td);
        $tableF = sprintf($tableF, '');
        $table  = sprintf($table, $tableH, $tableB, $tableF);
        if( $print == true ) echo $table;
        return $table;

    }
}
?>

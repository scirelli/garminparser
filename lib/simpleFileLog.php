<?PHP
function logToFile( $msg, $openFor = 'a', $fileName = 'log.txt'){
    $myFile = $fileName;
    
    $beginDate = new DateTime();
    $beginDate = $beginDate->format('m/d/Y h:m:s');
    $msg .= $beginDate . ': ' . $msg . PHP_EOL;
    $fh = fopen($myFile, $openFor) or die('cant find file');// $fh = fopen($myFile, 'r+');
    fwrite($fh, $msg);

    fclose($fh);
}
?>

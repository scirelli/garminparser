<?PHP
interface ITimer{
    public function start();
    public function stop();
    public function elapsed();
    public function resetTimer();
}
?>

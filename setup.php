<?php
    //-------------------------------------------------------------
    // File: Setup.php - Main setup file for site.
    // Author: Steve Cirelli
    // Desc: Open a database connection and init global variables.
    //-------------------------------------------------------------

    //require_once 'System.php';
    //require_once 'DB/DataObject.php';

    if( !isset($_GET['db'])) $_GET['db'] = 0;//debug info on
    if( !isset($_REQUEST["pg"])) $_REQUEST["pg"] = 'home';


    switch( $_SERVER['SERVER_NAME'] )
    {
    case "crazybashtud.com":
    case "jerinaw.org":
        $dbhost = '';//173.201.217.77 cirellijerinaw
        $dbuser = 'phpuser';//cirellijerinaw
        $dbadminUser = 'phpuser';
        $dbpass = 'steve';
        $dbAdminPass = 'steve';
        $dbname = 'GPSConverter';
        $dbbodreadOnlyUser = "phpuser";//password bodUser1234
        $dbbodreadOnlyUserPass = 'steve';
        $serverRoot = "/home/content/s/t/e/stevejc75/html/";
        $rootFolder = "garmin";
        break;
    case "localhost":
        $dbhost = 'localhost';
        $dbuser = 'phpuser';
        $dbadminUser = 'phpuser';
        $dbpass = 'steve';
        $dbAdminPass = 'steve';
        $dbname = 'GPSConverter';

        $serverRoot = "C:\\Users\\steve.cirelli\\My_Programs\\MyPrograms\\Webdesign\\htdocs\\garmin";
        $rootFolder = "garmin";
        break;
    default:
        echo 'Error';
        die(0);
    }

    $adminEmail = "gps_converter@crazybashtud.com";
    $httpRoot = "http://" . $_SERVER["HTTP_HOST"] . "/garmin/"; 
    $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('error connecting to mysql');
    echo mysql_error();
    mysql_select_db($dbname);

    $paths = array( 
                    'serverRoot' => $serverRoot,
                    'httpRoot'   => $httpRoot,
                    'rootFolder' => $rootFolder,
                    'libRoot'  => $serverRoot . $rootFolder . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR,
                    'libCommon'=> $serverRoot . $rootFolder . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR,
                    'images'   => $httpRoot . "images/",
                    'adminEmail' => $adminEmail,
                    'newsletters'   => $httpRoot . "newsletters/",
                    'dbObjects'   => $serverRoot . $rootFolder . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR,
                    'dataDir'    => $serverRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR ,
                    'trackDir'   => $serverRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'Tracks' . DIRECTORY_SEPARATOR,
                    'convertedDir'   => $serverRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'converted' . DIRECTORY_SEPARATOR
    );

    $slash = DIRECTORY_SEPARATOR;
 
    if( $_GET['db'] == 1){
        echo '<pre>';
        echo $_SERVER['SERVER_NAME'] . '\n';
        echo 'cwd: ' . getcwd(); 
        var_dump($paths);
        var_dump( $_SERVER );
        echo '</pre>';
    }

    require('lib/phpmailer/phpmailer.inc.php');
    /*
     *
        $mail = new phpmailer;

        $mail->IsSMTP(); // set mailer to use SMTP
        $mail->From = "from@email.com";
        $mail->FromName = "Mailer";
        $mail->Host = "smtp1.site.com;smtp2.site.com";  // specify main and backup server
        $mail->AddAddress("josh@site.com", "Josh Adams");
        $mail->AddAddress("ellen@site.com");   // name is optional
        $mail->AddReplyTo("info@site.com", "Information");
        $mail->WordWrap = 50;    // set word wrap
        $mail->AddAttachment("c:\\temp\\js-bak.sql");  // add attachments
        $mail->AddAttachment("c:/temp/11-10-00.zip");

        $mail->IsHTML(true);    // set email format to HTML
        $mail->Subject = "Here is the subject";
        $mail->Body = "This is the message body";
        $mail->Send(); // send message
     *
     * */
?>

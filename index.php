<?php session_start(); 
    require_once('lib/checkSet.php');
    $serverURL = 'http://' . $_SERVER['HTTP_HOST'] . '/garmin';
    $sessionID = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '';
    $uid       = checkSet('uid', '', false);
?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Garmin TCX to GPX</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if eq IE]>
            <p class="chromeframe">You are using a <strong>PoS</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
        <div id="wrap">
            <div class="container">
                <div class="page-header">
                    <h1 class="txt-cntr warning">*** This page is not for public use. ***</h1>
                </div>
                <p class="lead">
                    I created this page for personal use only. To automate the conversion of my 
                    <a href="https://buy.garmin.com/shop/shop.do?pID=349">Garmin Foreruner 305</a> exported files. I'm 
                    using <a href="http://adamschneider.net/">Adam Schneider's</a> <a href="http://www.gpsvisualizer.com/convert_input">converter</a> and his <a href="http://www.gpsvisualizer.com/forerunner">splitter</a> pages. Please visit his <a href="http://www.gpsvisualizer.com/">pages</a>, 
                    and use his apps directly. If you find them useful...or if you decide to use this app... please make a donation to him. 
                </p>
                <p>
                    Note: This takes a very long time to run. Be patient.
                </p>
                <?php if( $sessionID ){ ?>
                <div id="divSessionID">Your Id: <a href="<?php echo $serverURL . '?uid=' . $sessionID; ?>"><?php echo $sessionID;?></a></div>
                <?php } ?>
                <form id="fForm" action="act/act_fileupload.php" method="post" enctype="multipart/form-data">
                    <label for="ffId">
                        <?php if( $sessionID ){ ?>
                        If you've been here before and have a different <a class="id-info" href="#" title="I do not keep any information except the file names of the files you upload. This is to ensure no duplicates are processed.">ID</a> please paste it below.
                        <?php }else{ ?>
                        If you've been here before and have an <a class="id-info" href="#" title="I do not keep any information except the file names of the files you upload. This is to ensure no duplicates are processed.">ID</a> please paste it below.
                        <?php } ?>
                    </label>
                    <input type="text" id="ffId" name="ffId" readonly="readonly" value="<?php echo $uid; ?>" />

                    <label for="ffFile"></label>
                    <input type="file" name="ffFile" id="ffFile"><br/>
                    <input type="submit" name="submit" value="Submit"><br/>
                    <textarea id="ffOutput" name="ffOutput" form="fForm" readonly="readonly"><?php
                    ?></textarea>
                    <div id="link"></div>
                </form>
            </div>
            <div id="push"></div>
        </div>
        <div id="loading"></div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="js/lib/jquery.form.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>

<?php

date_default_timezone_set('Europe/Helsinki');
//mb_internal_encoding("UTF-8"); 
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
//This is a server using Windows
    $delim = ";";
    $slash = "\\";
} else {
//This is a server not using Windows!
    $delim = ":";
    $slash = "/";
}

define('APP_HOME', dirname((__FILE__)));
define('SLASH', $slash);

//echo '\nAplication_home:'.APP_HOME.' \n';
ini_set("include_path",  ini_get("include_path") .$delim . '/home/iordanov/php');

ini_set('include_path',  ini_get('include_path') . 
        $delim . '/home/iordanov/common/lib' . $delim . '/home/iordanov/common/lib/iiordan'.
        $delim . '/home/iordanov/common/lib/epcx' . $delim . '/home/iordanov/common/lib/epcx/com' .
        $delim . '/home/iordanov/common/lib/log4php' . 
        $delim . '/home/iordanov/common//lib/log4php/configurators');


//display_errors = On
ini_set("display_errors", "1");
ob_start();

//header('Cache-control: private');
//header("Content-Type: text/html; charset=utf-8");
//header('Access-Control-Allow-Origin: https://bwtws.iordanov.info/*');
session_start();

?>



<!DOCTYPE HTML>
<html>
    <head>
        <title>☘ Web Services | EPCX Writing Tool</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="EPCX Writing Tool">
        <meta name="author" content="I Z Iordanov">
        <link rel="shortcut icon" href="#">
        <link rel="stylesheet" href="https://common.ams.iordanov.info/prologue/assets/css/main.css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
    </head>
    <body>
        
        <!-- Header -->
        <div id="header">

            <div class="top">

                <!-- Logo -->
                <div id="logo">
                    <span class="image avatar48"><img src="https://common.ams.iordanov.info/prologue/images/avatar.jpg" alt="" /></span>
                    <h1 id="title">I.Z.Iordanov</h1>
                    <p>Web Services EPCX</p>
                </div>

                <!-- Nav -->
                <nav id="nav">
                    <!--

                            Prologue's nav expects links in one of two formats:

                            1. Hash link (scrolls to a different section within the page)

                               <li><a href="#foobar" id="foobar-link" class="icon fa-whatever-icon-you-want skel-layers-ignoreHref"><span class="label">Foobar</span></a></li>

                            2. Standard link (sends the user to another page/site)

                               <li><a href="http://foobar.tld" id="foobar-link" class="icon fa-whatever-icon-you-want"><span class="label">Foobar</span></a></li>

                    -->
                    <ul>
                        <li>
                            <a href="#" id="pink-link" class="skel-layers-ignoreHref" onclick="pingApi()">
                                <span class="icon material-icons">network_ping</span> Ping
                            </a>
                        </li>
                        <!--
                        <li><a href="#top" id="top-link" class="skel-layers-ignoreHref"><span class="icon fa-home">Intro</span></a></li>
                        <li><a href="#ams_wad" id="portfolio-link" class="skel-layers-ignoreHref"><span class="icon fa-th">Word Airports</span></a></li>
                        <li><a href="#about" id="about-link" class="skel-layers-ignoreHref"><span class="icon fa-user">About Me</span></a></li>
                        <li><a href="#contact" id="contact-link" class="skel-layers-ignoreHref"><span class="icon fa-envelope">Contact</span></a></li>
                        -->
                    </ul>
                </nav>

            </div>

            <div class="bottom">

                <!-- Social Icons -->
                <ul class="icons">
                    <li><a href="http://ws.bwt.iordanov.info/ping" class="icon material-icons" target="_blank" title="PING">done</a></li>
                    <li><a href="http://ws.bwt.iordanov.info/register" class="icon material-icons" target="_blank" title="REGISTER">person_add</span></a></li>
                    <li><a href="http://ws.bwt.iordanov.info/login" class="icon material-icons" target="_blank" title="Login">perm_identity</span></a></li>
                    <li><a href="#" class="icon fa-dribbble"><span class="label">Dribbble</span></a></li>
                    <li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
                    <li><a href="info_php.php" class="icon fa-envelope"><span class="label">PhP Info</span></a></li>
                </ul>

            </div>

        </div>

        <!-- Main -->
        <div id="main">

            <!-- Intro -->
            <section id="top" class="one dark cover">
                <div class="container">

                    <header>
                        

                    </header>



                </div>
            </section>

            <!-- WAD -->
            <section id="ams_wad" class="two">
                <div class="container">

                    <header>
                        <h2>EPCX</h2>
                    </header>

                    <p>
                        <b>Electronic Procurement and Construction Exchange</b> is the initial name of this project. 
                        Abbreviation is <b>NxEPCX</b>. The project aims to develop an e-procurement system. 
                        E-procurement also referred to as supplier exchange, is the purchase and sale of supplies, products and services.
                    </p>
                    <div class="row">
                        
                        
                    </div>

                </div>
            </section>

            

        </div>

        <!-- Footer -->
        <div id="footer">

            <!-- Copyright -->
            <ul class="copyright">
                <li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
            </ul>

        </div>

        <!-- Scripts -->
        <script src="https://common.ams.iordanov.info/prologue/assets/js/jquery.min.js"></script>
        <script src="https://common.ams.iordanov.info/prologue/assets/js/jquery.scrolly.min.js"></script>
        <script src="https://common.ams.iordanov.info/prologue/assets/js/jquery.scrollzer.min.js"></script>
        <script src="https://common.ams.iordanov.info/prologue/assets/js/skel.min.js"></script>
        <script src="https://common.ams.iordanov.info/prologue/assets/js/util.js"></script>
        <!--[if lte IE 8]><script src="https://common.ams.iordanov.info/prologue/assets/js/ie/respond.min.js"></script><![endif]-->
        <script src="https://common.ams.iordanov.info/prologue/assets/js/main.js"></script>
        <script trype="text/javascript">
            

            function pingApi() {
                console.log('pingApi -> ');
                var url = 'https://epcx.ws.iordanov.info/ping';
            
                try {
                    // 1. Make a GET request using fetch()
                     fetch(url).then(body => body.json()).then(response => {
                         console.log('pingApi -> response:', response);
                    // 2. Check if the response was successful (HTTP status code 200-299)
                        if (!(response.status === 'success')) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        } else{
                            console.log('✅ API is online. Response:', response);
                        }
                    });
                   

                } catch (error) {
                    // 5. Catch any errors (e.g., network failure, fetch error, or non-200 status)
                    console.error('❌ Failed to ping the API:', error.message);
                }
            }

        </script>
    </body>
</html>
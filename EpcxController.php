<?php

/*
 * -----------------------------------------------------------------------------
 *  Project             : epcx
 *  Module              : epcx.ws
 *  url                 : ws.epcx.iordanov.info    
 *  Date Creation       : Mart 19, 2024 
 *  Filename            : EpcxController.php
 *  Author              : IZIordanov
 * -----------------------------------------------------------------------------
 *  Copyright(C) 2000-2018 IZIordanov
 *  
 *  This program is free software; you can redistribute it and/or modify it under 
 *  the terms of the GNU General Public License published by the Free Software Foundation.
 * -----------------------------------------------------------------------------
 * This is a Controller file that receives the request and dispatches it to 
 * respective hendler for processing. 
 * ‘view’ key is used to identify the URL request.
 * -----------------------------------------------------------------------------
 */

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

define('APP_HOME', dirname(dirname((__FILE__))));
define('SLASH', $slash);

ini_set("include_path", ini_get("include_path") . $delim . '/home/iordanov/php');

ini_set('include_path', ini_get('include_path') .
        $delim . '/home/iordanov/common/lib' . $delim . '/home/iordanov/common/lib/iiordan' .
        $delim . '/home/iordanov/common/lib/epcx' . $delim . '/home/iordanov/common/lib/epcx/com' .
        $delim . '/home/iordanov/common/lib/log4php' .
        $delim . '/home/iordanov/common//lib/log4php/configurators');

$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
//setcookie('cookiename', 'https://epcx.ws.iordanov.info/', time() + 60 * 60 * 24 * 365, '/', $domain, false);
//display_errors = On
ini_set("display_errors", "1");

ob_start();

$mn = "EpcxController";
//--- Include CORS
require_once("rest_cors_header.php");
require_once("EpcxConnection.php");
require_once("EpcxLogger.php");
require_once("Functions.php");
require_once("EpcxRestHandler.php");
require_once("EpcxUserModel.class.php");
require_once("EpcxCookieConsent.class.php");

EpcxLogger::logBegin($mn);

$view = "";

if (isset($_REQUEST["view"])) {
    $view = $_REQUEST["view"];
}
EpcxLogger::log($mn, "view=" . $view);

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    $restHendler = new EpcxRestHandler();
    $restHendler->Option();
    EpcxLogger::logEnd($mn);
} else {

    // get the HTTP method, path and body of the request
    $method = $_SERVER['REQUEST_METHOD'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    /*
      controls the RESTful services URL mapping
     */
    switch ($view) {

        case "ping":
            if (isset($remoteIp)) {
                EpcxLogger::log($mn, "remoteIp: " . $remoteIp);
                $location = null;
                try {
                    $url = "https://free.freeipapi.com/api/json/$remoteIp";
                    $location = file_get_contents($url);
                    $location = json_decode($location, false);
                    EpcxLogger::log($mn, "location: " . json_encode($location));
                    /*
                      $location->latitude;
                      $location->longitude:
                     */
                } catch (Exception $ex) {
                    BwtLogger::logError($mn, $ex);
                }
            }
            $rh = new EpcxRestHandler();
            $response = $rh->Ping();
            $response->addData("location", $location);
            $rh->EncodeResponce($response);
            //EpcxLogger::log($mn, "ping executed");
            break;

        // <editor-fold defaultstate="collapsed" desc="Cookie Consent">

        case "epcx_consent_save":
            $rh = new EpcxRestHandler();
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                if (isset($dataJson->consent)) {
                    $consent = $dataJson->consent;
                    $rh->EpcxCookieConsentSave($consent);
                } else {
                    $response = new Response("error", 'Missing required parameters.');
                    $response->statusCode = 412;
                    $rh->EncodeResponce($response);
                    return;
                }
            }
            break;

        case "epcx_consent_get_id":
            $rh = new EpcxRestHandler();
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                $rh->EpcxCookieConsentGetById($dataJson->id);
            }
            break;

        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="Epcx User Methods">

        case "login":
            // to handle REST Url /pcpd/
            $restHendler = new EpcxRestHandler();
            // read JSon input
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                //EpcxLogger::log($mn, "[login] dataJson: " . $dataJson->username . " ");
                $restHendler->EpcxLogin($dataJson->email, $dataJson->password);
            }
            break;
        case "refreshtoken":
            // to handle REST Url /pcpd/
            $restHendler = new EpcxRestHandler();
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                $restHendler->RefreshToken($dataJson->refresh);
            } else {
                $response = new Response("error", 'Required parameters missing in request.');
                $response->statusCode = 412;
                $rh->EncodeResponce($response);
                return;
            }

            break;
        case "epcx-user-save":
            // to handle REST Url /pcpd/
            $restHendler = new EpcxRestHandler();
            // read JSon input
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                //EpcxLogger::log($mn, "[login] dataJson: " . json_encode($dataJson) . " ");
                $restHendler->EpcxUserSave($dataJson->user);
            }
            break;
        case "epcx-pwd-change":
            // to handle REST Url /pcpd/
            $restHendler = new EpcxRestHandler();
            // read JSon input
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                //EpcxLogger::log($mn, "[login] dataJson: " . json_encode($dataJson) . " ");
                $restHendler->EpcxChangePassword($dataJson);
            }
            break;
        case "epcx-sign-up":
            // to handle REST Url /pcpd/
            $restHendler = new EpcxRestHandler();
            // read JSon input
            $payload = file_get_contents('php://input');

            if (isset($payload)) {
                $dataJson = json_decode($payload);
                //EpcxLogger::log($mn, "[login] dataJson: " . json_encode($dataJson) . " ");
                $restHendler->EpcxUserRegister($dataJson->email, $dataJson->password);
            }
            break;

        // </editor-fold>

        default:
            EpcxLogger::log($mn, "No heandler for view: " . $view);
            break;
    }
}

EpcxLogger::logEnd($mn);


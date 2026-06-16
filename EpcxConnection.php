<?php

/* * ****************************** HEAD_BEG ************************************
 *
 * Project                	: epcx
 * Module                       : epcx.ws
 * Responsible for module 	: IordIord
 *
 * Filename               	: EpcxConnection.php
 *
 * Database System        	: ORCL, MySQL
 * Created from			: IordIord
 * Date Creation		: 11.11.2025
 * ------------------------------------------------------------------------------
 *                        Description
 * ------------------------------------------------------------------------------
 * @TODO Insert some description.
 * 	 
 * ------------------------------------------------------------------------------
 *                        History
 * ------------------------------------------------------------------------------
 * HISTORY:
 * <br>--- $Log: EpcxConnection.php,v $
 * <br>---
 * <br>--- 
 *
 * ******************************** HEAD_END ************************************
 */

global $bwtDbConnection;
global $contentPage;

require_once("config.inc.php");
require_once("ConnectionBase.class.php");
require_once("EpcxLogger.php");
require_once("Response.class.php");


// <editor-fold defaultstate="collapsed" desc="Connect Class">

class EpcxConnection extends ConnectionBase{
    private $dbHost=null;
    private $dbName=null;
    private $dbUser=null;
    private $dbPassword=null;
    private $dbPort=null;
    
    //establish db connection
    public function __construct() {
        $mn = "EpcxConnection:__construct()";
        EpcxLogger::logBegin($mn);
        parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        $this->dbHost = DB_HOST;
        $this->dbUser = DB_USER;
        $this->dbPassword = DB_PASS;
        $this->dbName = DB_NAME;
        $this->dbPort = DB_PORT;
        try {
            $this->connection->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");


            // Will not affect $mysqli->real_escape_string();
            $this->connection->query("SET CHARACTER SET utf8");

            // But, this will affect $mysqli->real_escape_string();
            $this->connection->set_charset('utf8');

            $charset = $this->connection->character_set_name();
            
            EpcxLogger::log($mn, "Connection to ".DB_NAME." established. Charset:".$charset);
            if (mysqli_connect_errno()) {
                EpcxLogger::log("$mn", "Database connect Error : " . mysqli_connect_error($this->connection));
                //header('Location: /dberror.html');
                //die();
                //header("Location: ".$url);
                //ob_flush();
            }
        } catch (Exception $ex) {
            echo 'Exception:' . $ex;
            EpcxLogger::logError($mn, $ex);
        }
        EpcxLogger::logEnd($mn);
    }
    
    public static function dbConnect() {
        global $epcxDbConnection;
        if(!isset($epcxDbConnection))
        {
            $epcxDbConnection = new EpcxConnection();
        }
        
        return $epcxDbConnection;
        
    }
}


// </editor-fold>
/**
 * ******************************************************************************
 *                        Iordan Iordanov 2025
 * ******************************************************************************
 * */

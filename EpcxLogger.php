<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $_epcxLogger;

require_once("LoggerBase.php");
Logger::configure(dirname(__FILE__) . '/appender_pdo.properties');
/**
 * Description of Log
 *
 * @author izior
 */
class EpcxLogger extends LoggerBase
{
    
    // <editor-fold defaultstate="collapsed" desc="__construct">
    public function __construct()
    {
        parent::__construct();
        $this->MN = "EpcxLogger: ";
        try {


            $this->logger = Logger::getRootLogger();

            $this->logger->debug("EpcxLogger init");

        } catch (Exception $ex) {
            echo "EpcxLogger Error: " . $ex . "<br/>";
        }
        //logEndST($MN, $ST);
    }
    
    // </editor-fold>
    
    
    // <editor-fold defaultstate="collapsed" desc="Methods">

    public static function loggerEpcx()
    {
        global $_epcxLogger;
        if (!isset($_epcxLogger)) {
            $_epcxLogger = new EpcxLogger();
        }

        return $_epcxLogger;
    }

    public static function currLogger()
    {
        global $_epcxLogger;
        if (!isset($_epcxLogger)) {
            $_epcxLogger = new EpcxLogger();
        }

        return $_epcxLogger;
    }

    public static function logBegin($mn)
    {
        EpcxLogger::currLogger()->begin($mn);
    }

    public static function logEnd($mn)
    {
        EpcxLogger::currLogger()->end($mn);
    }

    public static function log($mn, $msg)
    {
        EpcxLogger::currLogger()->debug($mn, $msg);
    }

    public static function logError($mn, $ex)
    {
        EpcxLogger::currLogger()->error($mn);
    }
    // </editor-fold>
}

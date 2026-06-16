<?php

/* * ****************************** HEAD_BEG ************************************
 *
 * Project                	: epcx
 * Module                       : epcx.ws
 * Responsible for module 	: IordIord
 *
 * Filename               	: EpcxRestHandler.class.php
 *
 * Database System        	: MySQL
 * Created from                 : IordIord
 * Date Creation		:Mart 19 2024
 * ------------------------------------------------------------------------------
 *                        Description
 * ------------------------------------------------------------------------------
 * @TODO Insert some description.
 *
 * ------------------------------------------------------------------------------
 *                        History
 * ------------------------------------------------------------------------------
 * HISTORY:
 * <br>--- $Log: EpcxRestHandler.class.php,v $
 * <br>---
 * <br>---
 *
 * ******************************** HEAD_END ************************************
 */
require_once("SimpleRest.class.php");
require_once("Response.class.php");
require_once("EpcxConnection.php");
require_once("EpcxLogger.php");
require_once("JwtAuth.php");
require_once("EpcxUserModel.class.php");
require_once("EpcxCookieConsent.class.php");


/**
 * Description of EpcxRestHandler
 *
 * @author IZIordanov
 */
class EpcxRestHandler extends SimpleRest
{
    
    // <editor-fold defaultstate="collapsed" desc="Option and Ping">

    public function Option()
    {
        $mn = "EpcxRestHandler::Option()";
        $response = new Response("success", "Service working.");

        $rh = new EpcxRestHandler();
        $rh->EncodeResponce($response);
    }

    public function Ping()
    {
        $mn = "EpcxRestHandler::Ping()";
        EpcxLogger::logBegin($mn);
        $response = null;
        try {
            $conn = EpcxConnection::dbConnect();
            if (isset($conn)) {
                EpcxLogger::log($mn, " response = " . "Service working");
                $response = new Response("success", "Service working.");
            } else {
                $response = new Response("success", "There is something wrong but generati I am alive.");
            }

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        return $response;//$this->EncodeResponce($response);
    }

     // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Bwt CookieConsent Methods">
    
    public function EpcxCookieConsentSave($value) {
        $mn = "EpcxRestHandler::EpcxCookieConsentSave()";
        EpcxLogger::logBegin($mn);
        $response = new Response();
        try {
            $obj = EpcxCookieConsent::Save($value);
            $response = new Response("success", "User Consent data saved.");
            $response->addData("consent", $obj);
        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }

        // EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);
        $this->EncodeResponce($response);
    }
    
    public function EpcxCookieConsentGetById($value) {
        $mn = "EpcxRestHandler::EpcxCookieConsentGetById(".$value.")";
        EpcxLogger::logBegin($mn);
        $response = new Response();
        try {
            $obj = EpcxCookieConsent::LoadById($value);
            $response = new Response("success", "User Consent get.");
            $response->addData("consent", $obj);
        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }

        // EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);
        $this->EncodeResponce($response);
    }
    
     // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="JWT Tocken and EpcxUser Methods New">

    public static function decryptPwd($mn, $encrypted){
        $key = pack("H*", "0123456789abcdef0123456789abcdef");
        $iv =  pack("H*", "abcdef9876543210abcdef9876543210");
        $password = null;
        try {
            $encrypted2 = base64_decode($encrypted);
            $password1 =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted2, MCRYPT_MODE_CBC, $iv);
            //EpcxLogger::log($mn, " password1 = " . $password1);
            $pad = substr($password1, -1);
            $password = rtrim($password1, $pad);
        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
        }
        return $password;
    }
    
    public function EpcxLogin($email, $encrypted )
    {
        $mn = "EpcxLogin()";
        EpcxLogger::logBegin($mn);
        $response = null;
        //EpcxLogger::log($mn, " email = " . $email);
        try {
            $password = EpcxRestHandler::decryptPwd($mn, $encrypted);
            //EpcxLogger::log($mn, " password = " . $password);
            $user = EpcxUser::login($email, $password);
            //EpcxLogger::log($mn, " user = " . json_encode($user));
            if (isset($user)) {
                //EpcxLogger::log($mn, " user = " . json_encode($user));
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                //EpcxLogger::log($mn, " ipAddress = " . $ipAddress);
                if (JwtAuth::signTocken($user->userId, $user)) {
                    $response = new Response("success", "JwtAuth Set.");
                    $response->addData("current_user", $user);
                    $refreshToken = JwtAuth::geRrefreshTockenByUserId($user->userId, $ipAddress);
                    $response->addData("refreshToken", $refreshToken);
                } else {
                    $response = new Response("error", "JwtAuth NOT Set.");
                    $response->statusCode = 401;
                }
            } else {
                $response = new Response("error", "Invalid Credentials.");
                $response->statusCode = 401;
            }

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        //EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        $this->EncodeResponce($response);
    }
    
    public function RefreshToken($refresh)
    {
        $mn = "EpcxRestHandler::RefreshToken()";
        EpcxLogger::logBegin($mn);
        $response = null;
        try {
            $user = JwtAuth::RefreshTocken($refresh);
            //EpcxLogger::log($mn, " user = " .json_encode($user));
             if (isset($user)) {
                 $ipAddress = $_SERVER['REMOTE_ADDR'];
                  $currUser = EpcxUser::LoadById($user->userId);
                
                if (JwtAuth::signTocken($user->userId, $user)) {
                    $response = new Response("success", "JwtAuth Set.");
                    $response->addData("current_user", $currUser);
                    $refreshToken = JwtAuth::geRrefreshTockenByUserId($user->userId, $ipAddress);
                    $response->addData("refreshToken", $refreshToken);
                } else {
                    $response = new Response("error", "JwtAuth NOT Set.");
                    $response->statusCode = 412;
                }
            } else {
                $response = new Response("error", "Invalid Refresh Token.");
                $response->statusCode = 412;
            }
            

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        //EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        $this->EncodeResponce($response);
    }
    
    public function EpcxUserSave($user )
    {
        $mn = "EpcxRestHandler::EpcxUserSave()";
        EpcxLogger::logBegin($mn);
        $response = null;
        
        try {
            EpcxLogger::log($mn, " user = " . json_encode($user));
            if (isset($user) && isset($user->id)) {
                //EpcxLogger::log($mn, " user = " . json_encode($user));
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                //$ipAddress = preg_replace('/(^"\\"|\\""$)/', '', $ipAddress);
                //EpcxLogger::log($mn, " user = " . json_encode($user));
                $user = EpcxUser::Save($user);
                $user->password = null;
                $response = new Response("success", "User Updated");
                $response->addData("current_user", $user);
            } else {
                $response = new Response("error", "Invalid Credentials.");
                $response->statusCode = 401;
            }

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        //EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        $this->EncodeResponce($response);
    }
    
    public function EpcxChangePassword($data )
    {
        $mn = "EpcxRestHandler::EpcxChangePassword()";
        EpcxLogger::logBegin($mn);
        $response = null;
        
        try {
            EpcxLogger::log($mn, " data = " . json_encode($data));
            $password = EpcxRestHandler::decryptPwd($mn, $data->password);
            
            if (isset($data) && isset($data->userId) && isset($password)) {
                $userIn = EpcxUser::LoadById($data->userId);
                $userIn->password = $password;
                //EpcxLogger::log($mn, " user = " . json_encode($user));
                $user = EpcxUser::ChangePassword($userIn);
                $response = new Response("success", "Password Updated");
                $response->addData("current_user", $user);
            } else {
                $response = new Response("error", "Invalid Credentials.");
                $response->statusCode = 401;
            }

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        //EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        $this->EncodeResponce($response);
    }
    
    public function EpcxUserRegister($eMail, $pwd)
    {
        $mn = "EpcxRestHandler::EpcxUserRegister -> ";
        EpcxLogger::logBegin($mn);
        $response = null;
        $errMsg;
        EpcxLogger::log($mn, " eMail = " . $eMail);
        try {
            $wrongEmail = false;
            $password = EpcxRestHandler::decryptPwd($mn, $pwd);
            if(!isset($eMail) || !isset($password)){
                $wrongEmail = true;
            }
             
            if(!$wrongEmail ){
                $wrongEmail = !checkEmail($eMail);
                EpcxLogger::log($mn, "checkEmail = " . $wrongEmail?'true':'false');
            }
            
            if(((bool)$wrongEmail) == FALSE){
                $conn = EpcxConnection::dbConnect();
                $logModel = EpcxLogger::currLogger()->getModule($mn);
                
                $objArrJ = EpcxUser::CheckEmailJson($eMail, $conn, $mn, $logModel);
                if(isset($objArrJ) && count($objArrJ)>0){
                    $val = json_decode(json_encode($objArrJ[0]));
                     EpcxLogger::log($mn, " val = " . json_encode($val));
                     $wrongEmail = (($val->rowCount > 0)?true:false);
                 }
                if(((bool)$wrongEmail)){
                     $errMsg = "E-Mail already registered.";
                }
            }
            
            EpcxLogger::log($mn, "wrongEmail = " . $wrongEmail);
            if (!$wrongEmail) {
               
                $payload = [
                    'email' => $eMail, 
                    'password' => $password,
                    ];
                EpcxLogger::log($mn, " payload = " . json_encode($payload));
                $dataJson = json_encode($payload);
                $val = json_decode($dataJson);
                $user = EpcxUser::Save($val);
                
                EpcxLogger::log($mn, "new user = " . json_encode($user));
                if (JwtAuth::signTocken($user->userId, $user)) {
                    $response = new Response("success", "Registration succesfull.");
                    $response->addData("current_user", $user);
                    $ipAddress = $_SERVER['REMOTE_ADDR'];
                    $refreshToken = JwtAuth::geRrefreshTockenByUserId($user->userId, $ipAddress);
                    $response->addData("refreshToken", $refreshToken);
                } else {
                    $response = new Response("error", "JwtAuth NOT Set.");
                    $response->statusCode = 401;
                }
            } else {
                if(!isset($errMsg))
                {
                    $errMsg = "Invalid values provided";
                }
                $response = new Response("error", $errMsg);
                $response->statusCode = 200;
            }

        } catch (Exception $ex) {
            EpcxLogger::logError($mn, $ex);
            $response = new Response($ex);
        }
        EpcxLogger::log($mn, " response = " . $response->toJSON());
        EpcxLogger::logEnd($mn);

        $this->EncodeResponce($response);
    }
    
    // </editor-fold>
    
}

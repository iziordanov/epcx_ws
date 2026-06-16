<?php 
	//database connection settings

	define('DB_HOST', 'localhost'); // database host
        define('DB_PORT', '3306'); // database port IordanMac 3317, dancholenhost 3333
	define('DB_USER', 'iordanov_amsone'); // username
	define('DB_PASS', 'Amsone1968'); // password dancholap(ams1770) or IordanMac(Ams1968)
	define('DB_NAME', 'iordanov_epcx_com'); // database name
	define('DB_CONV_REV', '0'); //If require DB Convert and Revert text set to 1
        define('ADMIN', '10'); //administrator role ID
	define('REG_USER', '1'); //user role I
        define('GUEST', '2'); //user role ID
        define('SECRET_SERVER_KEY', '12345678901234567890'); //hared secret key used for generating the HMAC variant of the message digest.
        define('SEVER_NAME', 'epcx.ws.iordanov.info');
        /**********************************************************************/
        define('ERROR_INCORECT_ACCESS', 'Incorect access registered.');
        
?>

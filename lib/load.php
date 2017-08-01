<?php
/**
* This file helps you to load all your libraries and 3rd party software.
*
* You don't have to do anything here if ATK14`s autoload subsystem will be able to load files itself.
*/

// composer
if(file_exists(__DIR__."/../vendor/autoload.php")){
	require(__DIR__."/../vendor/autoload.php");
}

if(
	!TEST &&
	!$HTTP_REQUEST->xhr() &&
	class_exists("Tracy\Debugger") &&
	php_sapi_name()!="cli" // we do not want Tracy in cli
){
	Tracy\Debugger::enable(PRODUCTION, __DIR__ . '/../log/',ATK14_ADMIN_EMAIL);
}

require_once(__DIR__ . "/functions.php");

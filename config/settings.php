<?php
/**
 * Either some parts of ATK14 system (i.e. mailing subsystem) or some third party libs
 * could be configured by constants or variables.
 * 
 * This file is the right place to do such configuration.
 *
 * You can inspect all ATK14 application`s constants in atk14/default_settings.php
 * 
 * All the application constants should be inspected by calling:
 *	$ ./scripts/dump_settings
 * 
 * A certain constant should be inspected this way:
 *	$ ./scripts/dump_settings DEFAULT_EMAIL
 */

define("DEFAULT_EMAIL","your@email.com");
define("ATK14_ADMIN_EMAIL",DEFAULT_EMAIL); // the address for sending error reports and so on...

define("ATK14_APPLICATION_NAME","BOOTSTRAP4 PLAYGROUND");
define("ATK14_APPLICATION_DESCRIPTION","Yet another application running on ATK14 Framework");

define("ATK14_HTTP_HOST",PRODUCTION ? "www.bootstrap4-playground.com" : "bootstrap4-playground.localhost");

date_default_timezone_set('Europe/Prague');

define("USING_BOOTSTRAP4",true);

// If you don't want to let users to register freely (e.g. your app is an closed alpha),
// set the constant INVITATION_CODE_FOR_USER_REGISTRATION.
// See app/forms/users/create_new_form.php for more info
// define("INVITATION_CODE_FOR_USER_REGISTRATION","some great secret");

if(DEVELOPMENT || TEST){
	// a place for development and testing environment settings

	ini_set("display_errors","1");
}

if(PRODUCTION){
	// a place for production environment settings

	if(php_sapi_name()!="cli"){
		ini_set("display_errors","0");
	}
}

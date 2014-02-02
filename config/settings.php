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

define("SECRET_TOKEN",PRODUCTION ? Files::GetFileContent(dirname(__FILE__)."/secret_token.txt") : "_please_put_here_a_lot_of_random_chars_");

define("DEFAULT_EMAIL","your@email.com");

define("ATK14_APPLICATION_NAME","ATK14 Skelet");
define("ATK14_HTTP_HOST",PRODUCTION ? "atk14skelet.atk14.net" : "atk14skelet.localhost");

if(DEVELOPMENT || TEST){
	// a place for development and testing environment settings

	ini_set("display_errors","1");
}

if(PRODUCTION){
	// a place for production environment settings

}

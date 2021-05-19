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

definedef("DEFAULT_EMAIL","your@email.com");
definedef("ATK14_ADMIN_EMAIL",DEFAULT_EMAIL); // the address for sending error reports and so on...

definedef("ATK14_APPLICATION_NAME","ATK14 Skelet");
definedef("ATK14_APPLICATION_DESCRIPTION","Yet another application running on ATK14 Framework");

definedef("ATK14_HTTP_HOST",PRODUCTION ? "skelet.atk14.net" : "atk14skelet.localhost");

date_default_timezone_set('Europe/Prague');

definedef("USING_BOOTSTRAP4",true);
definedef("USING_BOOTSTRAP5",true);
definedef("USING_FONTAWESOME",true);

definedef("REDIRECT_TO_SSL_AUTOMATICALLY",PRODUCTION);

// Automatic redirection to the ATK14_HTTP_HOST
definedef("REDIRECT_TO_CORRECT_HOSTNAME_AUTOMATICALLY",false);

// If you don't want to let users to register freely (e.g. your app is an closed alpha),
// set the constant INVITATION_CODE_FOR_USER_REGISTRATION.
// See app/forms/users/create_new_form.php for more info
// definedef("INVITATION_CODE_FOR_USER_REGISTRATION","some great secret");

// Or if you don't want to let users to register at all, set USER_REGISTRATION_ENABLED to false.
definedef("USER_REGISTRATION_ENABLED",true);

definedef("PUPIQ_API_KEY","101.DemoApiKeyForAccountWithLimitedFunctions");
definedef("PUPIQ_HTTPS",REDIRECT_TO_SSL_AUTOMATICALLY);

definedef("ARTICLE_BODY_MAX_WIDTH",825);

// If these two constant are properly defined (see https://github.com/atk14/RecaptchaField#installation),
// the re-captcha field is being automatically added into the contact form.
// define("RECAPTCHA_SITE_KEY","");
// define("RECAPTCHA_SECRET_KEY","");

// Google Analytics tracking code,
// see app/views/shared/trackers/google/_analytics.tpl and app/layouts/default.tpl.
// definedef("GOOGLE_ANALYTICS_TRACKING_ID","UA-123456789-1");

// Temporary files uploads (these settings effects use of AsyncFileField)
// definedef("TEMPORARY_FILE_UPLOADS_ENABLED",true);
// definedef("TEMPORARY_FILE_UPLOADS_DIRECTORY",__DIR__ . "/../tmp/temporary_file_uploads/");
// definedef("TEMPORARY_FILE_UPLOADS_MAX_FILESIZE",512 * 1024 * 1024); // 512MB
// definedef("TEMPORARY_FILE_UPLOADS_MAX_AGE", 2 * 60 * 60); // 2 hours

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

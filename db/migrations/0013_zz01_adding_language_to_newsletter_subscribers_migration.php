<?php
class Zz01AddingLanguageToNewsletterSubscribersMigration extends ApplicationMigration {

	function up(){
		global $ATK14_GLOBAL;

		$default_lang = $ATK14_GLOBAL->getDefaultLang();

		$this->dbmole->doQuery("ALTER TABLE newsletter_subscribers ADD language CHAR(2)");
		$this->dbmole->doQuery("UPDATE newsletter_subscribers SET language=:default_lang",[":default_lang" => $default_lang]);
		$this->dbmole->doQuery("ALTER TABLE newsletter_subscribers ALTER language SET NOT NULL");
	}
}

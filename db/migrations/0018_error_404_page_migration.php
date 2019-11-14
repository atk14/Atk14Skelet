<?php
class Error404PageMigration extends ApplicationMigration {

	function up(){
		$page = Page::CreateNewRecord([
			"code" => "error404",
			"indexable" => false,
			"visible" => false,

			"title_en" => "Error 404: Page not found",
			"body_en" => trim('
We are deeply sorry, but the requested page wasn\'t found.

[Go to the homepage](/en/)
			'),

			"title_cs" => "Chyba 404: Stránka nebyla nalezena",
			"body_cs" => trim('
Je nám velmi líto, ale požadovaná stránka nebyla nalezena.

[Jděte na homepage](/cs/)
			'),
		]);
	}
}

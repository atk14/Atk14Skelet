<?php
class FulfillingStaticPagesMigration extends Atk14Migration{
	function up(){
		$about = StaticPage::CreateNewRecord(array(
			"id" => 1, // we just need that the page #1 is the About Page

			"title_en" => "About ATK14 Skelet",
			"slug_en" => "about",
			"body_en" => "It all begins when a young boy meets a ...",

			"title_cs" => "O ATK14 Skeletu",
			"slug_cs" => "o-nas",
			"body_cs" => "Všechno to začalo, když jeden mladý muž potkal...",
		));

		$this->dbmole->doQuery("ALTER SEQUENCE seq_static_pages RESTART WITH 2");

		$media = StaticPage::CreateNewRecord(array(
			"parent_static_page_id" => $about,
			
			"title_en" => "For Media",
			"body_en" => "Currently we have no information for media.",

			"title_cs" => "Pro média",
			"body_cs" => "V této chvíli nemáme pro média žádné informace."
		));

		$contact_data = StaticPage::CreateNewRecord(array(
			"parent_static_page_id" => $about,
			
			"title_en" => "Contact Data",
			"body_en" => trim("
## Address

    Elm Street 1428
    Springwood
    Ohio
    United States
"),

			"title_cs" => "Kontaktní údaje",
			"body_cs" => "
## Adresa

    Elm Street 1428
    Springwood
    Ohio
    United States
"
		));
	}
}

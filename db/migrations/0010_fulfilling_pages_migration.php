<?php
class FulfillingPagesMigration extends Atk14Migration{

	function up(){
    if(TEST){ return; }

		$homepage = Page::CreateNewRecord(array(
			"code" => "homepage",

			"title_en" => "Welcome at ATK14 Catalog!",
			"teaser_en" => "ATK14 Catalog is an skeleton suitable for applications of kind like _Products introduction_, _E-shop_, etc. ATK14 Catalog is built on top of _ATK14 Skelet_ — another great skeleton.",
			"body_en" => "",

			"title_cs" => "Vítejte v ATK14 Katalogu!",
			"teaser_cs" => "ATK14 Katalog je skelet vhodný pro aplikace typu _produktový katalog_, _e-shop_ atd. ATK14 Katalog je postaven na _ATK14 Skeletu_ — dalším skvělém skeletu.",
			"body_cs" => "",
		));

		$about = Page::CreateNewRecord(array(
			"code" => "about_us",

			"title_en" => "About Us",
			"body_en" => "It all begins when a young boy meets a ...",

			"title_cs" => "O nás",
			"body_cs" => "Všechno to začalo, když jeden mladý muž potkal...",
		));

		$media = Page::CreateNewRecord(array(
			"parent_page_id" => $about,
			
			"title_en" => "For Media",
			"body_en" => "Currently we have no information for media.",

			"title_cs" => "Pro média",
			"body_cs" => "V této chvíli nemáme pro média žádné informace."
		));

		$contact_data = Page::CreateNewRecord(array(
			"parent_page_id" => $about,
			"code" => "contact",
			
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

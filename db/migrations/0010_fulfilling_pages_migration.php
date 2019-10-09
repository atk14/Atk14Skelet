<?php
class FulfillingPagesMigration extends Atk14Migration{

	function up(){
    if(TEST){ return; }

		$homepage = Page::CreateNewRecord(array(
			"code" => "homepage",

			"title_en" => "Welcome at ATK14 Skelet!",
			"teaser_en" => "_ATK14 Skelet_ is a very basic application written on top of [the ATK14 Framework](https://www.atk14.net/). As the Skelet is simple and minimal it may be usefull for developers as a good start point for any other application.",
			"body_en" => "",
			"page_title_en" => "ATK14 Skelet",

			"title_cs" => "Vítejte v ATK14 Skeletu!",
			"teaser_cs" => "_ATK14 Skelet_ je základní aplikace napsaná ve [fameworku ATK14](https://www.atk14.net/). Vzhledem k tomu, jak je Skelet jednoduchý a minimální, může být pro vývojáře užitečný jako dobrý výchozí bod pro jakoukoli jinou aplikaci.",
			"body_cs" => "",
			"page_title_cs" => "ATK14 Skelet",
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

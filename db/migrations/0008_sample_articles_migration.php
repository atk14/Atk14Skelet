<?php
class SampleArticlesMigration extends ApplicationMigration{

	function up(){

		$news = Tag::GetInstanceByCode("news");
		
		// *** Happy Millenium ***
		$article = Article::CreateNewRecord(array(
			"author_id" => 1,
			"published_at" => "2001-01-01",

			"image_url" => "http://i.pupiq.net/i/65/65/27e/2927e/1272x920/9cUpr1_800x578_26254b6a433fc4a9.jpg",

			// english version
			"title_en" => "Happy Millenium",
			"body_en" => "We wish you Happy 21st Millenium!\n\nMay all ATK14 developers are doing their job in peace.\n\nATK14 Development Team",

			// czech version
			"title_cs" => "Šťastné 21. století",
			"body_cs" => "Přejeme Vám štastné 21. století!\n\nKéž všichni ATK14 vývojáři dělají svou práci v míru.\n\nVývojový tým ATK14",
		));
		$article->setTags(array($news));

		// *** Welcome to ATK14 Skelet ***
		$article = Article::CreateNewRecord(array(
			"author_id" => 1,
			"published_at" => "2013-04-12",

			"image_url" => "http://i.pupiq.net/i/65/65/27c/2927c/1272x920/JuSG6C_800x578_0cecc732df82ad65.jpg",
			
			// english version
			"title_en" => "Welcome to ATK14 Skelet",
			"teaser_en" => "Watch out! ATK14 Skelet is out now!",
			"body_en" => trim("
We are happy to announce the availability of ATK14 Skelet. This is a carefully selected & minimalistic set of functionality you may require at the start of your next web project.

You can find more informations on the following addresses

  * [AKT14 Skelet on Github](https://github.com/atk14/Atk14Skelet)
  * [Atk14 Site](http://www.atk14.net/)
  * [Atk14 Book](http://book.atk14.net/)

Remember! Your projects shall run on [ATK14 Framework](http://www.atk14.net/), for now and ever after!
			"),

			// czech version
			"title_cs" => "Vítejte na ATK14 Skelet",
			"body_cs" => trim("
Jsme šťastní, že Vám můžeme oznámit zpřistupnění ATK14 Skeletu. Jedná se o minimalistickou a pečlivě vybranou sadu funkcí, kterou můžete potřebovat na startu Vašeho dalšího webového projektu.

Více informací naleznete na těchto adresách:

  * [AKT14 Skelet na Githubu](https://github.com/atk14/Atk14Skelet)
  * [Stránky o frameworku Atk14](http://www.atk14.net/)
  * [Kniha o Atk14](http://book.atk14.net/)

Pamatujte si, že Váš další projekt by měl běžet na [frameworku ATK14](http://www.atk14.net/), od teď až na věky
			"),
		));
		$article->setTags(array($news));

		
	}
}

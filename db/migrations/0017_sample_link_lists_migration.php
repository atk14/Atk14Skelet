<?php
class SampleLinkListsMigration extends ApplicationMigration {

	function up(){
		$main_menu = LinkList::CreateNewRecord([
			"system_name" => "Main menu",
			"code" => "main_menu",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $main_menu,
			"title_en" => "Main page",
			"title_cs" => "Úvod",
			"url" => "/"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $main_menu,
			"title_en" => "About Us",
			"title_cs" => "O nás",
			"url" => "/about-us/"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $main_menu,
			"title_en" => "Contact",
			"title_cs" => "Kontakt",
			"url" => "/about-us/contact-data/"
		]);

		// ---

		$footer_1 = LinkList::CreateNewRecord([
			"system_name" => "Footer #1",
			"code" => "footer_1",
			"title_en" => "Links",
			"title_cs" => "Odkazy"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 1",
			"title_en" => "Odkaz 1",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 2",
			"title_en" => "Odkaz 2",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 3",
			"title_en" => "Odkaz 3",
			"url" => "/#",
		]);

		// ---

		$footer_2 = LinkList::CreateNewRecord([
			"system_name" => "Footer #2",
			"code" => "footer_2",
			"title_en" => "Another Links",
			"title_cs" => "Další odkazy"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_2,
			"title_en" => "Link 4",
			"title_en" => "Odkaz 4",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_2,
			"title_en" => "Link 5",
			"title_en" => "Odkaz 5",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_2,
			"title_en" => "Link 6",
			"title_en" => "Odkaz 6",
			"url" => "/#",
		]);
	}
}

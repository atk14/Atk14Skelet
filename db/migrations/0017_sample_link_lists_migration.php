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
			"title_en" => "Footer Links",
			"title_cs" => "Odkazy v patičce"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 1",
			"title_cs" => "Odkaz 1",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 2",
			"title_cs" => "Odkaz 2",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_1,
			"title_en" => "Link 3",
			"title_cs" => "Odkaz 3",
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
			"title_cs" => "Odkaz 4",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_2,
			"title_en" => "Link 5",
			"title_cs" => "Odkaz 5",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_2,
			"title_en" => "Link 6",
			"title_cs" => "Odkaz 6",
			"url" => "/#",
		]);

		// ---

		$footer_3 = LinkList::CreateNewRecord([
			"system_name" => "Footer #3",
			"code" => "footer_3",
			"title_en" => "Yet Another Links",
			"title_cs" => "Ještě další odkazy"
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_3,
			"title_en" => "Link 7",
			"title_cs" => "Odkaz 7",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_3,
			"title_en" => "Link 8",
			"title_cs" => "Odkaz 8",
			"url" => "/#",
		]);

		LinkListItem::CreateNewRecord([
			"link_list_id" => $footer_3,
			"title_en" => "Link 9",
			"title_cs" => "Odkaz 9",
			"url" => "/#",
		]);
	}
}

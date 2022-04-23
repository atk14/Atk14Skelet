<?php
/**
 *
 * @fixture pages
 *
 * this needs to be the last fixture
 * @fixture link_list_items
 */
class TcLinkListItem extends TcBase {

	function test(){
		$li_testing_page = $this->link_list_items["main_menu__testing_page"];
		$target = $li_testing_page->getTargetObject();
		$this->assertEquals(true,is_a($target,"Page"));
		$this->assertEquals("testing_page",$target->getCode());

		$li_homepage = $this->link_list_items["main_menu__homepage"];
		$target = $li_homepage->getTargetObject();
		$this->assertEquals(true,is_a($target,"Page"));
		$this->assertEquals("homepage",$target->getCode());

		$li_external = $this->link_list_items["main_menu__external"];
		$this->assertEquals(null,$li_external->getTargetObject());
	}

	function test_getSubmenu(){
		$lli = $this->link_list_items["main_menu__testing_page"];
		$submenu = $lli->getSubmenu();
		$this->assertNotNull($submenu);
		$items = $submenu->getItems();
		$this->assertEquals(1,sizeof($items)); // there is one subpage
		$this->assertEquals(Atk14Url::BuildLink(["namespace" => "", "action" => "pages/detail", "id" => $this->pages["testing_subpage"]]),$items[0]->getUrl());

		$lli = $this->link_list_items["main_menu__external"];
		$this->assertEquals(null,$lli->getSubmenu());

		// submenu linked by the same code (main_menu_code)
		$lli = $this->link_list_items["test_list__main_menu"];
		$submenu = $lli->getSubmenu();
		$items = $submenu->getItems();
		$this->assertEquals(3,sizeof($items));
		$this->assertEquals("Home",$items[0]->getTitle());
		$this->assertEquals("/",$items[0]->getUrl());
		$this->assertEquals("Testing page",$items[1]->getTitle());
		$this->assertEquals("/testing-page/",$items[1]->getUrl());
		$this->assertEquals("ATK14 Framework",$items[2]->getTitle());
		$this->assertEquals("https://www.atk14.net/",$items[2]->getUrl());

		$lli->s("code","different_code");
		$this->assertEquals(null,$lli->getSubmenu());
	}

	function test_changing_url_according_to_language(){
		$item = $this->link_list_items["main_menu__testing_page"];
		$this->assertEquals("/testing-page/",$item->getUrl());

		$lang = "cs";
		Atk14Locale::Initialize($lang);
		$this->assertEquals("/testovaci-stranka/",$item->getUrl());

		$lang = "en";
		Atk14Locale::Initialize($lang);
		
		$item->s("url","/testing-page/#anchor");

		$this->assertEquals("/testing-page/#anchor",$item->getUrl());

		$lang = "cs";
		Atk14Locale::Initialize($lang);
		$this->assertEquals("/testovaci-stranka/#anchor",$item->getUrl());
		$this->assertEquals("/testovaci-stranka/#test",$item->getUrl(["anchor" => "test"]));
		$this->assertEquals("/testovaci-stranka/",$item->getUrl(["anchor" => ""]));
	}

	function test_specific_url_for_language(){
		$item = $this->link_list_items["main_menu__testing_page"];

		$item->s("url_localized_en","/testing-page/?english=1");

		$this->assertEquals("/testing-page/?english=1",$item->getUrl());
		$this->assertEquals("/testing-page/?english=1",$item->getUrl("en"));
		$this->assertEquals("/testovaci-stranka/",$item->getUrl("cs"));

		$lang = "cs";
		Atk14Locale::Initialize($lang);
		$this->assertEquals("/testovaci-stranka/",$item->getUrl());
		$this->assertEquals("/testing-page/?english=1",$item->getUrl("en"));
		$this->assertEquals("/testovaci-stranka/",$item->getUrl("cs"));
	}
}

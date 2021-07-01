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
	}
}

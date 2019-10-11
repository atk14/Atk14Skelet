<?php
/**
 *
 * @fixture pages
 */
class TcLinkToPage extends TcBase {

	function test(){
		global $ATK14_GLOBAL;

		Atk14Require::Helper("modifier.link_to_page");

		$ATK14_GLOBAL->setValue("lang","en");

		$link = smarty_modifier_link_to_page("testing_page");
		$this->assertEquals("/testing-page/",$link);

		$link = smarty_modifier_link_to_page("testing_page","with_hostname");
		$this->assertEquals("http://".ATK14_HTTP_HOST."/testing-page/",$link);

		$link = smarty_modifier_link_to_page($this->pages["testing_subpage"]);
		$this->assertEquals("/testing-page/testing-subpage/",$link);

		$link = smarty_modifier_link_to_page("weird_code");
		$this->assertEquals("/en/main/page_not_found/?page=weird_code",$link);

		$ATK14_GLOBAL->setValue("lang","cs");

		$link = smarty_modifier_link_to_page("testing_page");
		$this->assertEquals("/testovaci-stranka/",$link);

		$link = smarty_modifier_link_to_page($this->pages["testing_subpage"]);
		$this->assertEquals("/testovaci-stranka/testovaci-podstranka/",$link);

		$link = smarty_modifier_link_to_page("weird_code");
		$this->assertEquals("/cs/main/page_not_found/?page=weird_code",$link);

		$link = smarty_modifier_link_to_page("weird_code","with_hostname");
		$this->assertEquals("http://".ATK14_HTTP_HOST."/cs/main/page_not_found/?page=weird_code",$link);
	}
}

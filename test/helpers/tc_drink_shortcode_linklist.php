<?php
/**
 * @fixture link_lists
 * @fixture link_list_items
 */
class TcDrinkShortcodeLinklist extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.markdown");

		$link_list = $this->link_lists["test_list"];
		$link_list->s(array("code" => "test_list_code", "title_en" => "Useful links"));

		// default type, with the title
		$html = smarty_modifier_markdown('[linklist code="test_list_code"][/linklist]');
		$this->assertStringContains('class="drink-shortcode linklist--shortcode"',$html);
		$this->assertStringContains('<div class="linklist">',$html);
		$this->assertStringNotContains('linklist--images',$html);
		$this->assertStringContains('Useful links',$html);
		$this->assertStringContains('Main page',$html);
		$this->assertStringNotContains('Invisible link',$html);

		// with images, without the title, with own class and style
		$html = smarty_modifier_markdown('[linklist code="test_list_code" type="with_images" show_title=0 class="my-links" style="max-width: 40rem;"][/linklist]');
		$this->assertStringContains('linklist--images',$html);
		$this->assertStringNotContains('Useful links',$html);
		$this->assertStringContains('class="drink-shortcode linklist--shortcode my-links"',$html);
		$this->assertStringContains('style="max-width: 40rem;"',$html);

		// no debugging output
		$this->assertStringNotContains('<p>default</p>',$html);
		$this->assertStringNotContains('<p>with images</p>',$html);

		// an unknown link list renders nothing
		$html = smarty_modifier_markdown('[linklist code="no_such_list"][/linklist]');
		$this->assertStringNotContains('linklist--shortcode',$html);
	}
}

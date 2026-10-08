<?php
class TcMarkdownField extends TcBase {

	function test_block_shortcodes(){
		$block_shortcodes = MarkdownField::GetBlockShortcodes();
		$this->assertContains("row",$block_shortcodes);
		$this->assertContains("col",$block_shortcodes);
		$this->assertContains("div",$block_shortcodes);
		$this->assertContains("tabs",$block_shortcodes); // app/helpers/block.drink_shortcode__tabs.php
		$this->assertNotContains("span",$block_shortcodes); // inline block shortcode

		$f = new MarkdownField(["base_href" => "/admin/en/pages/"]);
		$html = $f->widget->render("body","# Hello");
		$this->assertStringContains('data-provide="markdown"',$html);
		$this->assertStringContains('data-base_href="/admin/en/pages/"',$html);
		$this->assertStringContains('data-block_shortcodes="'.h(json_encode($block_shortcodes)).'"',$html);
	}
}

<?php
class TcDrinkShortcodeRow extends TcBase {

	function test_class_and_style(){
		Atk14Require::Helper("modifier.markdown");

		$html = smarty_modifier_markdown(join("\n",[
			'[row class="x" style="gap: 1rem;"]',
			'[col style="background: #eee;"]A[/col]',
			'[col class="y"]B[/col]',
			'[/row]',
		]));

		$this->assertContains('<div class="row row--shortcode x" style="gap: 1rem;">',$html);
		// the column's own style follows its order
		$this->assertContains('<div class="col-12 col-xs-12 col-md-6 col--shortcode" style="order: 0; background: #eee;">',$html);
		$this->assertContains('<div class="col-12 col-xs-12 col-md-6 col--shortcode y" style="order: 0;">',$html);

		// no style, no attribute
		$html = smarty_modifier_markdown("[row]\n[col]A[/col]\n[/row]");
		$this->assertContains('<div class="row row--shortcode">',$html);
	}
}

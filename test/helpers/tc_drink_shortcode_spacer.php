<?php
class TcDrinkShortcodeSpacer extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.markdown");

		$this->assertEquals('<div class="spacer" style="height: 3rem;" aria-hidden="true"></div>',trim(smarty_modifier_markdown('[spacer height="3rem"]')));

		// a standalone spacer is not wrapped in a paragraph
		$html = smarty_modifier_markdown("Hello\n\n[spacer height=\"20px\"]\n\nWorld");
		$this->assertStringContains("<p>Hello</p>",$html);
		$this->assertStringContains('<div class="spacer" style="height: 20px;" aria-hidden="true"></div>',$html);
		$this->assertStringContains("<p>World</p>",$html);
		$this->assertStringNotContains('<p><div class="spacer"',$html);

		foreach([
			'[spacer height="1.5em"]' => "1.5em",
			'[spacer height="1.5 EM"]' => "1.5em",
			'[spacer height=".5rem"]' => ".5rem",
			'[spacer height="40"]' => "40px", // a bare number means px
			'[spacer]' => "2rem", // the default
			'[spacer height="10vh"]' => "2rem", // an unsupported unit
			'[spacer height="1px; background: red"]' => "2rem", // nothing gets into the style
		] as $src => $height){
			$this->assertStringContains('style="height: '.$height.';"',smarty_modifier_markdown($src),$src);
		}
	}
}

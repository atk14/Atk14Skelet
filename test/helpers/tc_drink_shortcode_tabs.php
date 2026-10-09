<?php
class TcDrinkShortcodeTabs extends TcBase {

	function test_tab_names(){
		Atk14Require::Helper("modifier.markdown");

		$html = smarty_modifier_markdown(join("\n",[
			'[tabs]',
			'[tab name="Foundation \"S2\""]',
			'A',
			'[/tab]',
			'[tab name=\'Rock "n" roll\']',
			'B',
			'[/tab]',
			'[tab class="x" name="Plain"]',
			'C',
			'[/tab]',
			'[/tabs]',
		]));

		preg_match_all('/<button class="nav-link[^>]*>(.*?)<\/button>/s',$html,$matches);
		$this->assertEquals(['Foundation "S2"','Rock "n" roll','Plain'],$matches[1]);

		// the first tab is active
		preg_match_all('/class="tab-pane fade( show active)?/',$html,$matches);
		$this->assertEquals([" show active","",""],$matches[1]);
	}
}

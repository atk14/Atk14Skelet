<?php
class TcDisplayPhone extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.display_phone");

		$this->assertEquals("",smarty_modifier_display_phone(""));

		$nbsp = html_entity_decode("&nbsp;");

		$this->assertEquals("+420{$nbsp}605{$nbsp}111{$nbsp}222",smarty_modifier_display_phone("+420.605111222"));
	}
}

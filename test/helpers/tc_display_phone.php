<?php
class TcDisplayPhone extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.display_phone");

		$this->assertEquals("",smarty_modifier_display_phone(""));

		$this->assertEquals("+420 605 111 222",smarty_modifier_display_phone("+420.605111222"));
	}
}

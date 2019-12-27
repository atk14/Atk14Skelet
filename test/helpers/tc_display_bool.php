<?php
class TcDisplayBool extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.display_bool");

		$this->assertEquals("Yes",smarty_modifier_display_bool("1"));
		$this->assertEquals("No",smarty_modifier_display_bool("0"));

		$this->assertEquals("Yes",smarty_modifier_display_bool("true"));
		$this->assertEquals("No",smarty_modifier_display_bool("false"));

		$this->assertEquals("Yes",smarty_modifier_display_bool("Y"));
		$this->assertEquals("No",smarty_modifier_display_bool("N"));
	}
}

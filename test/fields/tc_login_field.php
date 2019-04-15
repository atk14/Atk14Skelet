<?php
class TcLoginField extends TcBase {

	function test(){
		$this->field = new LoginField();

		$login = $this->assertValid("john.doe");
		$this->assertEquals("john.doe",$login);

		$login = $this->assertValid("SAMANTHA92");
		$this->assertEquals("samantha92",$login);
	}
}

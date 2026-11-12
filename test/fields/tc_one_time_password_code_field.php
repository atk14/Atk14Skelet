<?php
class TcOneTimePasswordCodeField extends TcBase {

	function test(){
		$this->field = new OneTimePasswordCodeField();

		$code = $this->assertValid("12345678");
		$this->assertEquals("12345678",$code);

		// whitespace inside and around the code should be stripped
		$code = $this->assertValid(" 1234 5678 ");
		$this->assertEquals("12345678",$code);

		$code = $this->assertValid("12\t34\n56");
		$this->assertEquals("123456",$code);

		// required field
		$this->assertInvalid("");

		// custom label/max_length can be overridden
		$this->field = new OneTimePasswordCodeField(["label" => "Kód", "max_length" => 6]);
		$this->assertEquals("Kód",$this->field->label);
		$this->assertInvalid("1234567");
	}
}

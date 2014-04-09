<?php
class TcTextField extends TcBase{
	function test_cleaning(){
		$this->field = $f = new TextField();

		// by default there is no white characters removal
		$this->assertEquals(" Hello World\nby ATK14 ",$this->clean(" Hello World\nby ATK14 "));

		$err_msg = $this->assertInvalid("");
		$this->assertEquals($f->messages["required"],$err_msg); // $this->assertEquals("This field is required.",$err_msg);

		// white characters only...
		$err_msg = $this->assertInvalid("  \n  ");
		$this->assertEquals($f->messages["required"],$err_msg);

		$this->field = $f = new TextField(array("required" => false));

		$this->assertEquals("  \n  ",$this->clean("  \n  "));
	}

	function test_rendering(){
		$form = new Atk14Form();
		$form["body"] = new TextField(array(
			"initial" => " Hello World! "
		));

		$this->assertEquals('<textarea cols="40" rows="10" class="form-control" id="id_body" name="body"> Hello World! </textarea>',$form["body"]->as_widget());
	}
}

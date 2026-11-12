<?php
/**
 * @fixture users
 */
class TcUserField extends TcBase{

	function test_class_name_and_suggesting_url_are_derived_from_the_field_class_name(){
		$f = new UserField();

		$this->assertEquals("User",$f->class_name);
		$this->assertEquals("yes",$f->widget->attrs["data-suggesting"]);
		$this->assertStringContains("/suggestions/users/",$f->widget->attrs["data-suggesting_url"]);
		$this->assertStringContains("format=json",$f->widget->attrs["data-suggesting_url"]);
	}

	function test_format_initial_data(){
		$rambo = $this->users["rambo"];
		$f = new UserField();

		$this->assertEquals("",$f->format_initial_data(null));
		$this->assertEquals("rambo (John Rambo) [#".$rambo->getId()."]",$f->format_initial_data($rambo));
		$this->assertEquals("rambo (John Rambo) [#".$rambo->getId()."]",$f->format_initial_data($rambo->getId()));
	}

	function test_cleaning(){
		$rambo = $this->users["rambo"];
		$this->field = $f = new UserField();

		// a bare id
		$user = $this->assertValid((string)$rambo->getId());
		$this->assertEquals($rambo->getId(),$user->getId());

		// "Name #id" notation, as typed by a human before picking a suggestion
		$user = $this->assertValid("John Rambo #".$rambo->getId());
		$this->assertEquals($rambo->getId(),$user->getId());

		// "Name (...) [#id]" notation, as produced by format_initial_data()
		$user = $this->assertValid($f->format_initial_data($rambo));
		$this->assertEquals($rambo->getId(),$user->getId());

		// a non existing id
		$err = $this->assertInvalid("999999999");
		$this->assertEquals($f->messages["not_found"],$err);
	}

	function test_return_object_false_returns_the_id_instead_of_the_object(){
		$rambo = $this->users["rambo"];
		$this->field = $f = new UserField(["return_object" => false]);

		$id = $this->assertValid((string)$rambo->getId());
		$this->assertEquals($rambo->getId(),$id);
	}
}

<?php
class TcFormatBytes extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.format_bytes");

		$this->assertEquals("",smarty_modifier_format_bytes(""));

		// Bytes
		$this->assertEquals("100 Bytes",smarty_modifier_format_bytes("100"));
		$this->assertEquals("999 Bytes",smarty_modifier_format_bytes("999"));

		// kB
		$this->assertEquals("1.0 kB",smarty_modifier_format_bytes("1000"));
		$this->assertEquals("1.0 kB",smarty_modifier_format_bytes("1024"));
		$this->assertEquals("2.2 kB",smarty_modifier_format_bytes("2222"));

		// MB
		$this->assertEquals("3.4 MB",smarty_modifier_format_bytes("3584707"));

		// GB
		$this->assertEquals("1.3 GB",smarty_modifier_format_bytes("1380674586"));

		// TB
		$this->assertEquals("2.2 TB",smarty_modifier_format_bytes("2418925581107"));

		// locale
		$locale = "cs";
		Atk14Locale::Initialize($locale);
		$this->assertEquals("1,3 GB",smarty_modifier_format_bytes("1380674586"));
	}
}

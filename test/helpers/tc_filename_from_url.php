<?php
class TcFilenameFromUrl extends TcBase {

	function test(){
		Atk14Require::Helper("modifier.filename_from_url");

		$this->assertEquals("",smarty_modifier_filename_from_url(""));
		$this->assertEquals("",smarty_modifier_filename_from_url(null));

		// basic filename extraction
		$this->assertEquals("terms.pdf",smarty_modifier_filename_from_url("https://example.com/files/terms.pdf"));
		$this->assertEquals("terms.pdf",smarty_modifier_filename_from_url("https://example.com/terms.pdf"));

		// query string is stripped
		$this->assertEquals("terms.pdf",smarty_modifier_filename_from_url("https://example.com/files/terms.pdf?token=abc123"));
		$this->assertEquals("terms.pdf",smarty_modifier_filename_from_url("https://example.com/files/terms.pdf?foo=1&bar=2"));

		// no extension
		$this->assertEquals("document",smarty_modifier_filename_from_url("https://example.com/path/document"));

		// no filename
		$this->assertEquals("file",smarty_modifier_filename_from_url("https://example.com/path/"));
	}
}

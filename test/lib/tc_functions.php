<?php
class TcFunctions extends TcBase {

	function test_now(){
		$now = now();
		$this->assertTrue(!!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',$now));
		$this->assertTrue((time() - strtotime($now))<2); // for slow testing machines

		$this->assertEquals(date("Y-m-d"),now("Y-m-d"));
	}

	function test_array_flatten(){
		$ar = ["a", ["b", "c"], [["d",["e","f"]]]];
		$this->assertEquals(["a","b","c","d","e","f"],array_flatten($ar));

		$ar = ["x" => "a", "y" => ["z" => "b", "c"]];
		$this->assertEquals(["x" => "a", "z" => "b", 0 => "c"],array_flatten($ar));

		// TODO
		//$ar = ["x" => "a", "y" => ["z" => "b", "x" => "c"]];
		//$this->assertEquals(["x" => "a", "z" => "b", 0 => "c"],array_flatten($ar));
	}
}

<?php
class TcArrayFlatten extends TcBase {

	function test(){
		$ar = ["a", ["b", "c"], [["d",["e","f"]]]];
		$this->assertEquals(["a","b","c","d","e","f"],array_flatten($ar));

		$ar = ["x" => "a", "y" => ["z" => "b", "c"]];
		$this->assertEquals(["a", "b", "c"],array_flatten($ar));
		$this->assertEquals(["a", "b", "c"],array_flatten($ar,["preserve_keys" => false]));
		$this->assertEquals(["x" => "a", "z" => "b", 0 => "c"],array_flatten($ar,["preserve_keys" => true]));

		$ar = ["x" => "a", "y" => ["z" => "b", "x" => "c"]];
		$this->assertEquals(["a", "b", "c"],array_flatten($ar));
		$this->assertEquals(["a", "b", "c"],array_flatten($ar,["preserve_keys" => false]));
		$this->assertEquals(["x" => "c", "z" => "b"],array_flatten($ar,["preserve_keys" => true]));
	}
}

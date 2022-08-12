<?php
class TcFunctions extends TcBase {

	function test_now(){
		$now = now();
		$this->assertTrue(!!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',$now));
		$this->assertTrue((time() - strtotime($now))<2); // for slow testing machines

		$this->assertEquals(date("Y-m-d"),now("Y-m-d"));
	}
}

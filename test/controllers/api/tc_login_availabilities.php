<?php
class TcLoginAvailabilities extends TcBase{
	function test_detail(){
		$data = $this->_get("login_availabilities/detail",array("login" => "john.root"),$response_code);
		$this->assertEquals(200,$response_code);
		$this->assertEquals(array("status" => "available"),$data);

		$data = $this->_get("login_availabilities/detail",array("login" => "admin"),$response_code);
		$this->assertEquals(200,$response_code);
		$this->assertEquals(array("status" => "taken"),$data);
	}
}

<?php
class TcInvalidLoginAttempt extends TcBase {

	function test_IsRemoteAddressBlocked(){
		$remote_addr = "8.8.8.8";
		$another_remote_addr = "9.9.9.9";
		$current_time = time();

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time]));
		$this->assertEquals(null,$realease_time);

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 20 * 60), // too old, has no effect on blocking
			"created_from_addr" => $remote_addr,
		]);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time]));

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 5 * 60 + 10),
			"created_from_addr" => $remote_addr,
		]);

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 5 * 60 + 20),
			"created_from_addr" => $remote_addr,
		]);

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 5 * 60 + 30),
			"created_from_addr" => $remote_addr,
		]);

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 5 * 60 + 40),
			"created_from_addr" => $remote_addr,
		]);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time]));
		$this->assertEquals(null,$realease_time);

		InvalidLoginAttempt::CreateNewRecord([
			"login" => "admin",
			"created_at" => date("Y-m-d H:i:s",$current_time - 5 * 60 + 50),
			"created_from_addr" => $remote_addr,
		]);

		$this->assertEquals(true,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time]));
		$this->assertEquals(50,$realease_time);

		$this->assertEquals(true,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time + 49]));
		$this->assertEquals(1,$realease_time);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($remote_addr,$realease_time,["current_time" => $current_time + 50]));
		$this->assertEquals(null,$realease_time);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($another_remote_addr,$realease_time,["current_time" => $current_time]));
	}

	function test_BuildLoginAttemptDelayMessage(){
		$this->assertEquals("Delay the next sign-in attempt for 10 minutes",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(620));
		$this->assertEquals("Delay the next sign-in attempt for 11 minutes",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(630));

		$this->assertEquals("Delay the next sign-in attempt for 2 seconds",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(2));
		$this->assertEquals("Delay the next sign-in attempt for 40 seconds",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(40));

		$this->assertEquals("Delay the next sign-in attempt for a second",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(1));
		$this->assertEquals("Delay the next sign-in attempt for a second",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(0));
		$this->assertEquals("Delay the next sign-in attempt for a second",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(-1));

		$this->assertEquals("Delay the next sign-in attempt for 2 minutes",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(122));
		$this->assertEquals("Delay the next sign-in attempt for 2 minutes and 30 seconds",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(150));
		
		$this->assertEquals("Delay the next sign-in attempt for one minute",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(60));
		$this->assertEquals("Delay the next sign-in attempt for one minute",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(55));
		$this->assertEquals("Delay the next sign-in attempt for one minute",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(62));
		$this->assertEquals("Delay the next sign-in attempt for one minute and 10 seconds",InvalidLoginAttempt::BuildLoginAttemptDelayMessage(70));
	}
}

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
			"created_at" => date("Y-m-d H:i:s",$current_time - 20 * 60), // within 2hr lookback but outside the penalty window
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

	function test_exponential_backoff(){
		$ip = "5.5.5.5";
		$base = time();

		// Phase 1: 5 attempts 90 minutes ago (within 2hr lookback) — simulates a served first block
		for($i = 0; $i < 5; $i++){
			InvalidLoginAttempt::CreateNewRecord([
				"login" => "attacker",
				"created_at" => date("Y-m-d H:i:s",$base - 90 * 60 + $i * 30),
				"created_from_addr" => $ip,
			]);
		}

		// 5 old attempts, last one 90 min ago — no active block
		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $base]));
		$this->assertEquals(null,$realease_time);

		// Phase 2: 5 fresh attempts — total 10 within 2hr window → 10-minute lockout
		// attempts at: base-600, base-540, base-480, base-420, base-360
		for($i = 0; $i < 5; $i++){
			InvalidLoginAttempt::CreateNewRecord([
				"login" => "attacker",
				"created_at" => date("Y-m-d H:i:s",$base - 10 * 60 + $i * 60),
				"created_from_addr" => $ip,
			]);
		}

		// 10 total → block_round=1 → threshold=10min
		// last attempt at base-360, release_time = (base-360 + 600) - base = 240s
		$this->assertEquals(true,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $base]));
		$this->assertEquals(240,$realease_time);

		$this->assertEquals(true,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $base + 239]));
		$this->assertEquals(1,$realease_time);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $base + 240]));
		$this->assertEquals(null,$realease_time);

		// Phase 3: 5 more attempts after 10-min block expires → total 15 → 20-minute lockout
		// Check at $t2 = $base+1200 (20 min later). Attempts at $t2-300 to $t2-60 (5-1 min before $t2).
		$t2 = $base + 1200;
		for($i = 0; $i < 5; $i++){
			InvalidLoginAttempt::CreateNewRecord([
				"login" => "attacker",
				"created_at" => date("Y-m-d H:i:s",$t2 - 5 * 60 + $i * 60),
				"created_from_addr" => $ip,
			]);
		}

		// 15 total → block_round=2 → threshold=1200s
		// last_attempt=$t2-60, release_time = ($t2-60+1200)-$t2 = 1140s
		$this->assertEquals(true,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $t2]));
		$this->assertEquals(1140,$realease_time);

		$this->assertEquals(false,InvalidLoginAttempt::IsRemoteAddressBlocked($ip,$realease_time,["current_time" => $t2 + 1140]));
		$this->assertEquals(null,$realease_time);
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

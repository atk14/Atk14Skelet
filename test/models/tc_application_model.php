<?php
/**
 * Put here tests for model`s base class when needed.
 *
 * @fixture users
 */
class TcApplicationModel extends TcBase{

	function test_true(){
		$this->assertTrue(true);
	}

	function test_GetInstanceByToken(){
		$user = $this->users["rambo_tester"];

		$token = $user->getToken();
		$token_salted = $user->getToken("Extra_Spicy_Salt");

		$this->assertTrue($token!=$token_salted);

		$u = User::GetInstanceByToken($token);
		$this->assertEquals($user->getId(),$u->getId());

		$u = User::GetInstanceByToken($token_salted,"Extra_Spicy_Salt");
		$this->assertEquals($user->getId(),$u->getId());

		$this->assertNull(User::GetInstanceByToken(""));
		$this->assertNull(User::GetInstanceByToken("nonsence"));
		$this->assertNull(User::GetInstanceByToken($token."a_hacking_attempt"));
		$this->assertNull(User::GetInstanceByToken($token_salted));
		$this->assertNull(User::GetInstanceByToken($token,"Extra_Spicy_Salt"));
	}

	function test_do_not_set_update_time() {
		$user = $this->users["rambo_tester"];
		$this->assertNull($user->getUpdatedAt());

		$user->s("email", "rambouch@email.com", array("set_update_time" => false));
		$this->assertNull($user->getUpdatedAt());

		$user->s("email", "rambouch@gmail.com");
		$this->assertNotNull($user->getUpdatedAt());
	}
}

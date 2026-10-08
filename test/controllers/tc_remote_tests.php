<?php
class TcRemoteTests extends TcBase {

	function test(){
		$client = $this->client;

		$client->get("remote_tests/success");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue(!!preg_match('/^ok/',$client->getContent()));

		$client->get("remote_tests/fail");
		$this->assertEquals(500,$client->getStatusCode());
		$this->assertTrue(!!preg_match('/^fail/',$client->getContent()));

	}

	function test_admin_default_password(){
		$client = $this->client;

		// right after migrations the seeded admin user still has the default password
		$client->get("remote_tests/admin_default_password");
		$this->assertEquals(500,$client->getStatusCode());

		// once the default password is changed, the check must pass
		$admin = User::FindById(User::ID_SUPERADMIN);
		$admin->s("password","No-longer-the-default-1");

		$client->get("remote_tests/admin_default_password");
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue(!!preg_match('/^ok/',$client->getContent()));
	}
}

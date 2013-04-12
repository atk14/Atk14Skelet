<?php
class TcApplication extends TcBase{
	function test_access(){
		$this->client->get("main/index");
		$this->assertEquals(403,$this->client->getStatusCode());

		$user = User::CreateNewRecord(array(
			"login" => "ordinary_testing_user",
			"password" => "x",
		));
		$this->client->session->s("logged_user_id",$user->getId());
		$this->assertEquals(403,$this->client->getStatusCode());

		// only administrator can access
		$this->client->session->s("logged_user_id",1); // admin
		$this->client->get("main/index");
		$this->assertEquals(200,$this->client->getStatusCode());
	}
}

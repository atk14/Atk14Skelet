<?php
class TcUsers extends TcBase{
	function test_destroy(){
		// attempt to destroy non existing user
		$this->client->post("users/destroy",array(
			"id" => 1234,
		));
		$this->assertEquals(404,$this->client->getStatusCode()); // not found

		// attempt to destroy system user #1
		$this->client->post("users/destroy",array(
			"id" => 1,
		));
		$this->assertEquals(403,$this->client->getStatusCode()); // forbidden

		// delete normal user
		$user = User::CreateNewRecord(array(
			"login" => "to_be_deleted",
			"password" => "britney",
		));
		$this->client->post("users/destroy",array(
			"id" => $user->getId(),
		));
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting
	}

	function test_edit_password(){
		$vagus = User::CreateNewRecord(array(
			"login" => "va.gus",
			"password" => "secret",
		));

		$this->client->post("users/edit_password",array(
			"id" => $vagus->getId(),
			"password" => "rambo",
		));
		$this->assertEquals(303,$this->client->getStatusCode());

		$this->assertEquals(false,!!User::Login("va.gus","secret"));
		$this->assertEquals(true,!!User::Login("va.gus","rambo"));
	}
}

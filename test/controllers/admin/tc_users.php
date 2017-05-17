<?php
/**
 * @fixture users
 */
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

		$this->client->post("users/destroy",array(
			"id" => $this->users["rambo"]->getId(),
		));
		$this->assertEquals(303,$this->client->getStatusCode()); // redirecting
	}

	function test_edit_password(){
		$this->assertEquals(true,!!User::Login("rocky","secret"));
		$this->assertEquals(false,!!User::Login("rocky","Bimbo"));

		$this->client->post("users/edit_password",array(
			"id" => $this->users["rocky"]->getId(),
			"password" => "Bimbo",
		));
		$this->assertEquals(303,$this->client->getStatusCode());

		$this->assertEquals(false,!!User::Login("rocky","secret"));
		$this->assertEquals(true,!!User::Login("rocky","Bimbo"));
	}
}

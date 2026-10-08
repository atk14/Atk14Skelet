<?php
/**
 * @fixture users
 * @fixture pictures
 */
class TcIobjects extends TcBase{

	function test_edit(){
		$this->_loginUser(1); // admin
		$picture = $this->pictures["astronaut"];

		$this->client->get("iobjects/edit",array("id" => $picture->getId()));
		$this->assertEquals(302,$this->client->getStatusCode());
		$params = Atk14Url::RecognizeRoute($this->client->getLocation());
		$this->assertEquals("admin",$params["namespace"]);
		$this->assertEquals("pictures",$params["controller"]);
		$this->assertEquals("edit",$params["action"]);
		$this->assertEquals($picture->getId(),$params["get_params"]["id"]);

		$this->client->get("iobjects/edit",array("id" => 9999999));
		$this->assertEquals(404,$this->client->getStatusCode());
	}
}

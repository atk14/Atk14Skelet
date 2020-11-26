<?php
/**
 *
 * @fixture users
 */
class TcPasswordRecoveries extends TcBase {

	function test_create_new(){
		$client = $this->client;

		$ctrl = $client->post("password_recoveries/create_new",array(
			"login" => "rambo",
		));
		$this->assertEquals(303,$client->getStatusCode());
		$this->assertFalse($ctrl->form->has_errors());
		$this->assertContains('Have you forgotten your password?',$ctrl->mailer->body);

		$ctrl = $client->post("password_recoveries/create_new",array(
			"login" => "inactive.user",
		));
		$this->assertEquals(200,$client->getStatusCode());
		$this->assertTrue($ctrl->form->has_errors());
		$this->assertContains("This user account has been deactivated. Password recovery process can not be started.",$client->getContent());

	}
}

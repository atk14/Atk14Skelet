<?php
/**
 * @fixture users
 */
class TcUser extends TcBase{

	function testHashingPassword(){
		// see test/fixtures/users.yml
		$rambo = $this->users["rambo"];
		$rocky = $this->users["rocky"];

		$this->assertTrue($rambo->getPassword()!="secret");
		$this->assertTrue($rocky->getPassword()!=$rambo->getPassword()); // same passwords, different salts, different hashes

		$this->assertNull(User::Login("rambo","bad try",$bad_password));
		$this->assertTrue($bad_password);

		$user = User::Login("rambo","secret");
		$this->assertEquals($rambo->getId(),$user->getId());

		$user = User::Login("rocky","secret");
		$this->assertEquals($rocky->getId(),$user->getId());

		$rambo->s("password","CrazyWolf");
		$this->assertTrue($rambo->getPassword()!="CrazyWolf");
		$this->assertNull(User::Login("rambo","secret"));
		$this->assertNotNull(User::Login("rambo","CrazyWolf"));

		$hash = $rambo->g("password");
		$rambo->s("password",$hash);
		$this->assertEquals($hash,$rambo->g("password"));

		$rambo->s("password","CrazyWolf");
		$this->assertNotEquals($hash,$rambo->g("password"));
		$this->assertNotNull(User::Login("rambo","CrazyWolf"));

		$this->assertNull(User::Login("mr.cooler","infinity",$bad_password));
		$this->assertEquals(false,$bad_password);
	}
}

<?
class TcUser extends TcBase{
	function testHashingPassword(){
		$rambo = User::CreateNewRecord(array("login" => "rambo", "password" => "secret"));
		$rocky = User::CreateNewRecord(array("login" => "rocky", "password" => "secret"));

		$this->assertTrue($rambo->getPassword()!="secret");
		$this->assertTrue($rocky->getPassword()!=$rambo->getPassword()); // different salts, different hashes

		$this->assertNull(User::Login("rambo","bad try"));

		$user = User::Login("rambo","secret");
		$this->assertEquals($rambo->getId(),$user->getId());

		$user = User::Login("rocky","secret");
		$this->assertEquals($rocky->getId(),$user->getId());

		$rambo->s("password","CrazyWolf");
		$this->assertTrue($rambo->getPassword()!="CrazyWolf");
		$this->assertNull(User::Login("rambo","secret"));
		$this->assertNotNull(User::Login("rambo","CrazyWolf"));
	}
}

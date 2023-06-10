<?php
/**
 * @fixture users
 */
class TcUser extends TcBase{

	function test(){
		$rambo = $this->users["rambo"];

		$this->assertEquals("John Rambo",$rambo->getName());
		$this->assertEquals("John Rambo","$rambo"); // toString
		$rambo->s([
			"firstname" => "",
			"lastname" => "",
		]);
		$this->assertEquals("rambo",$rambo->getName());
		$this->assertEquals("rambo","$rambo"); // toString


		$this->assertEquals(true,$rambo->isActive());
		$this->assertEquals(false,$rambo->isAdmin());
		$this->assertEquals(false,$rambo->isSuperAdmin());

		$rambo->s("active",false);
		$this->assertEquals(false,$rambo->isActive());

		$rambo->s("is_admin",true);
		$this->assertEquals(true,$rambo->isAdmin());
		$this->assertEquals(false,$rambo->isSuperAdmin());

		$superadmin = User::FindById(User::ID_SUPERADMIN);
		$this->assertEquals(true,$superadmin->isAdmin());
		$this->assertEquals(true,$superadmin->isSuperAdmin());
	}

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

	function test_null_and_empty_password(){
		$rambo = $this->users["rambo"];

		$this->assertTrue($rambo->isPasswordCorrect("secret"));

		$rambo->s("password",null);
		$this->assertTrue($rambo->getPassword()===null);

		$this->assertFalse($rambo->isPasswordCorrect("secret"));
		$this->assertFalse($rambo->isPasswordCorrect(null));
		$this->assertFalse($rambo->isPasswordCorrect(""));

		$rambo->s("password","");
		$this->assertTrue($rambo->getPassword()==="");

		$this->assertFalse($rambo->isPasswordCorrect("secret"));
		$this->assertFalse($rambo->isPasswordCorrect(null));
		$this->assertFalse($rambo->isPasswordCorrect(""));
	}

	function test_Login(){
		$rambo = User::Login("rambo","secret",$bad_password);
		$this->assertTrue(!!$rambo);
		$this->assertEquals("rambo",$rambo->getLogin());
		$this->assertFalse($bad_password);

		$rambo_tester = User::Login("rambo.tester","Secret123",$bad_password);
		$this->assertTrue(!!$rambo_tester);
		$this->assertEquals("rambo.tester",$rambo_tester->getLogin());
		$this->assertFalse($bad_password);

		$rambo = User::Login("rambo","badguess",$bad_password);
		$this->assertNull($rambo);
		$this->assertTrue($bad_password);

		$rambo = User::Login("rambo.xyz","secret",$bad_password);
		$this->assertNull($rambo);
		$this->assertFalse($bad_password);

		// inactive user
		$this->users["rambo"]->s("active",false);
		$rambo = User::Login("rambo","secret",$bad_password);
		$this->assertNull($rambo);
		$this->assertFalse($bad_password);

		// deleted user
		$this->users["rambo_tester"]->destroy(false);
		$rambo_tester = User::Login("rambo.tester","Secret123",$bad_password);
		$this->assertNull($rambo_tester);
		$this->assertFalse($bad_password);

		$rambo_tester = User::Login("rambo.tester~deleted-".$this->users["rambo_tester"]->getId(),"Secret123",$bad_password);
		$this->assertNull($rambo_tester);
		$this->assertFalse($bad_password);
	}

	function test_destroy(){
		$rambo = $this->users["rambo"];
		$rambo_id = $rambo->getId();

		$this->assertFalse($rambo->isDeleted());
		$this->assertNull($rambo->getDeletedAt());
		$this->assertTrue($rambo->isActive());
		$this->assertNotNull($rambo->getPassword());
		
		// destroy not for real
		$rambo->destroy();

		$rambo = User::GetInstanceById($rambo_id);
		$this->assertNotNull($rambo);

		$this->assertTrue($rambo->isDeleted());
		$this->assertNotNull($rambo->getDeletedAt());
		$this->assertFalse($rambo->isActive());
		$this->assertTrue($rambo->g("active"));
		$this->assertEquals("rambo~deleted-$rambo_id",$rambo->g("login"));
		$this->assertEquals("rambo",$rambo->getLogin());
		$this->assertNull($rambo->getPassword());

		// destroy for real
		$rambo->destroy(true);

		$rambo = User::GetInstanceById($rambo_id);
		$this->assertNull($rambo);
	}
}

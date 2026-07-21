<?php
class TcOneTimePassword extends TcBase {

	function test(){
		$password1 = "123456";
		$rec1 = OneTimePassword::CreateNewRecordFor("user_login_2fa",123,$password1,"john@doe.com");

		$password2 = "111222";
		$rec2 = OneTimePassword::CreateNewRecordFor("user_login_2fa",123,$password2,"john@doe.com");

		// Bad try
		$this->assertNull(OneTimePassword::GetActiveInstanceFor("user_login_2fa",123,"666666"));
	
		// Good password ($password1)
		$rec = OneTimePassword::GetActiveInstanceFor("user_login_2fa",123,$password1);
		$this->assertNotNull($rec);
		$this->assertEquals($rec1->getId(),$rec->getId());

		// Good password ($password2)
		$rec = OneTimePassword::GetActiveInstanceFor("user_login_2fa",123,$password2);
		$this->assertNotNull($rec);
		$this->assertEquals($rec2->getId(),$rec->getId());

		// Different $purpose
		$this->assertNull(OneTimePassword::GetActiveInstanceFor("user_update_confirmation",123,$password1));

		// Different $object_key
		$this->assertNull(OneTimePassword::GetActiveInstanceFor("user_login_2fa",456,$password1));

		// Expired record
		$rec1->s("expires_at",date("Y-m-d H:i:s",time() - 60));

		$this->assertNull(OneTimePassword::GetActiveInstanceFor("user_login_2fa",123,$password1));

		$rec = OneTimePassword::GetActiveInstanceFor("user_login_2fa",123,$password2);
		$this->assertNotNull($rec);
		$this->assertEquals($rec2->getId(),$rec->getId());

		// Method markAsUsed
		global $HTTP_REQUEST;
		$HTTP_REQUEST->setRemoteAddr("8.8.8.8");

		$this->assertEquals(false,$rec1->isUsed());
		$rec1->markAsUsed();
		$this->assertEquals(true,$rec1->isUsed());
		$this->assertEquals("8.8.8.8",$rec1->g("used_from_addr"));
	}
}

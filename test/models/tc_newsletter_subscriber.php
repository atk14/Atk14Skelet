<?php
class TcNewsletterSubscriber extends TcBase{
	function test(){
		global $HTTP_REQUEST;

		$HTTP_REQUEST->setRemoteAddr("1.2.3.4");

		$ns = NewsletterSubscriber::SignUp("john@doe.com");
		$ns2 = NewsletterSubscriber::SignUp("john@doe.com");

		$this->assertEquals($ns->getId(),$ns2->getId());
		$this->assertEquals("1.2.3.4",$ns->getCreatedFromAddr());
		$this->assertEquals(null,$ns->getUpdatedAt());
		$this->assertEquals(null,$ns->getUpdatedFromAddr());
		$this->assertEquals(null,$ns->getVocative());
		$this->assertEquals(null,$ns->getName());

		$HTTP_REQUEST->setRemoteAddr("5.5.5.5");

		$ns3 = NewsletterSubscriber::SignUp("john@doe.com",array(
			"vocative" => "mr",
			"name" => "John Doe",
		));

		$this->assertEquals($ns->getId(),$ns3->getId());
		$this->assertEquals("1.2.3.4",$ns3->getCreatedFromAddr());
		$this->assertEquals("5.5.5.5",$ns3->getUpdatedFromAddr());
		$this->assertEquals("mr",$ns3->getVocative());
		$this->assertEquals("John Doe",$ns3->getName());

		$HTTP_REQUEST->setRemoteAddr("8.8.8.8");

		$ns4 = NewsletterSubscriber::SignUp("john@doe.com");

		$this->assertEquals($ns->getId(),$ns4->getId());
		$this->assertEquals("1.2.3.4",$ns3->getCreatedFromAddr());
		$this->assertEquals("5.5.5.5",$ns3->getUpdatedFromAddr());
		$this->assertEquals("mr",$ns3->getVocative());
		$this->assertEquals("John Doe",$ns3->getName());

		$HTTP_REQUEST->setRemoteAddr("9.9.9.9");

		$ns5 = NewsletterSubscriber::SignUp("john@doe.com",array("name" => "John Doe aka Foo Bar"));

		$this->assertEquals($ns->getId(),$ns5->getId());
		$this->assertEquals("1.2.3.4",$ns5->getCreatedFromAddr());
		$this->assertEquals("9.9.9.9",$ns5->getUpdatedFromAddr());
		$this->assertEquals("mr",$ns5->getVocative());
		$this->assertEquals("John Doe aka Foo Bar",$ns5->getName());
	}
}

<?php
class NewsletterSubscriber extends ApplicationModel{

	/**
	 * Signing up for newsletter
	 *
	 * It does nothing if the given email is already signed up
	 *
	 * NewsletterSubscriber::SignUp("john@doe.com");
	 * NewsletterSubscriber::SignUp("john@doe",array("name" => "John Doe"));
	 * NewsletterSubscriber::SignUp("john@doe",array("vocative" => "Mr.", "name" => "John Doe"));
	 */
	static function SignUp($email,$values = array()){
		// TODO: What about different character sizes in the same email address?
		$ns = NewsletterSubscriber::FindByEmail($email);
		if($ns){
			$upd_array = array();
			foreach($values as $k => $v){
				if(strlen($v)==0){ continue; }
				if($ns->g($k)!=$v){
					$upd_array[$k] = $v;
				}
			}
			if($upd_array){
				$upd_array["updated_from_addr"] = $GLOBALS["HTTP_REQUEST"]->getRemoteAddr();
				$ns->s($upd_array);
			}
		}else{
			$values["email"] = $email;
			$values["created_from_addr"] = $GLOBALS["HTTP_REQUEST"]->getRemoteAddr();
			$ns = NewsletterSubscriber::CreateNewRecord($values);
		}

		return $ns;
	}
}

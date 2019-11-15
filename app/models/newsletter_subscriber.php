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
	static function SignUp($user_or_email,$values = array()){
		$values_create = array();
		if(is_a($user_or_email,"User")){
			$ns = NewsletterSubscriber::FindFirstByUserId($user_or_email);
			$values_create["user_id"] = $user_or_email;
		}else{
			$ns = NewsletterSubscriber::FindFirst("LOWER(email)=LOWER(:email)",array(":email" => "$user_or_email"));
			$values_create["email"] = $user_or_email;
		}

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
			$values["created_from_addr"] = $GLOBALS["HTTP_REQUEST"]->getRemoteAddr();
			$ns = NewsletterSubscriber::CreateNewRecord($values + $values_create);
		}

		return $ns;
	}
}

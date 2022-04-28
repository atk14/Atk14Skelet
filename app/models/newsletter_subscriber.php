<?php
class NewsletterSubscriber extends ApplicationModel{

	static function GetInstancesByEmail($email){
		$query = "
			SELECT id FROM (
				SELECT
					id,created_at
				FROM
					newsletter_subscribers
				WHERE
					LOWER(email)=LOWER(:email)
				--	UNION
				--	SELECT
				--		newsletter_subscribers.id,newsletter_subscribers.created_at
				--	FROM
				--		users,
				--		newsletter_subscribers
				--	WHERE
				--		LOWER(users.email)=LOWER(:email) AND
				--		newsletter_subscribers.user_id=users.id
			)q ORDER BY created_at DESC, id DESC
		";

		$dbmole = self::GetDbmole();
		$ids = $dbmole->selectIntoArray($query,[":email" => $email]);

		return Cache::Get("NewsletterSubscriber",$ids);
	}

	static function GetMostRecentInstanceByEmail($email,$options = []){
		if($ns_ar = self::GetInstancesByEmail($email)){
			return $ns_ar[0];
		}
	}

	static function CreateNewRecord($values,$options = []){
		global $ATK14_GLOBAL;
		
		$values += [
			"language" => $ATK14_GLOBAL->getLang(),
		];

		return parent::CreateNewRecord($values,$options);
	}

	/**
	 * Signing up for newsletter
	 *
	 * It does nothing if the given email is already signed up
	 *
	 * NewsletterSubscriber::SignUp("john@doe.com");
	 * NewsletterSubscriber::SignUp("john@doe",array("name" => "John Doe"));
	 * NewsletterSubscriber::SignUp("john@doe",array("vocative" => "Mr.", "name" => "John Doe"));
	 */
	static function SignUp($user_or_email,$values = array(),&$subscription_just_created = false,$options = array()){
		global $ATK14_GLOBAL, $HTTP_REQUEST;

		$values += [
			"language" => $ATK14_GLOBAL->getLang(),
		];

		$subscription_just_created = false;

		$options += array(
			"request" => $HTTP_REQUEST
		);


		$request = $options["request"];

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
				$upd_array["updated_from_addr"] = $request->getRemoteAddr();
				$ns->s($upd_array);
			}
		}else{
			$values += [
				"created_from_addr" => $request->getRemoteAddr(),
				"subscribed_at_url" => $request->getUrl(),
			];
			$ns = NewsletterSubscriber::CreateNewRecord($values + $values_create);
			$subscription_just_created = true;
		}

		return $ns;
	}
}

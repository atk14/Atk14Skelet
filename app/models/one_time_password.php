<?php
class OneTimePassword extends ApplicationModel {

	static function CreateNewRecord($values,$options = []){
		$values += [
			"expires_at" => date("Y-m-d H:i:s", time() + 60 * 60 * 4), // 4 hours
		];

		return parent::CreateNewRecord($values,$options);
	}

	/**
	 *
	 *	$password = (string)String4::RandomNumericString(10);
	 *	$otp = OneTimePassword::CreateNewRecordFor("user_login_2fa",$user->gertId(),$password,$user->getEmail());
	 */
	static function CreateNewRecordFor($purpose,$object_key,$password_plain,$recipient,$values = []){
		$password_plain = (string)$password_plain;
		myAssert(strlen($password_plain)>0);

		$password_hash = password_hash($password_plain,defined("PASSWORD_ARGON2ID") ? constant("PASSWORD_ARGON2ID") : PASSWORD_DEFAULT);
		myAssert(strlen($password_hash)>0);

		$values += [
			"purpose" => (string)$purpose,
			"object_key" => (string)$object_key,
			"password" => $password_hash,
			"recipient" => (string)$recipient,
		];

		return self::CreateNewRecord($values);
	}

	/**
	 *
	 *	$password = $_POST["code"]; // e.g. "1948362";
	 *	$otp = OneTimePassword::GetActiveInstanceFor("user_login_2fa",$user->getId(),$password);
	 *	if(!$otp){
	 *		// Bad password
	 *		// do something...
	 *		return;
	 *	}
	 *	$otp->markAsUsed();
	 */
	static function GetActiveInstanceFor($purpose,$object_key,$password){
		$password = (string)$password;
		foreach(self::FindAll([
			"conditions" => "purpose=:purpose AND object_key=:object_key AND expires_at>:now",
			"bind_ar" => [
				":purpose" => (string)$purpose,
				":object_key" => (string)$object_key,
				":now" => now(),
			],
			"order_by" => "created_at DESC, id DESC",
		]) as $rec){
			if(password_verify($password,$rec->getPassword())){
				return $rec;
			}
		}
	}

	function isUsed(){
		return !is_null($this->g("used_at"));
	}

	function markAsUsed(){
		global $HTTP_REQUEST;

		$this->s([
			"used_at" => now(),
			"used_from_addr" => $HTTP_REQUEST->getRemoteAddr(),
			"used_from_hostname" => $HTTP_REQUEST->getRemoteHostname(),
		]);
	}
}

<?php
/**
 * Model class for user records
 *
 * Uses lib/myblowfish.php for passwords hashing.
 * See test/models/tc_user.php for basic usage.
 */
class User extends ApplicationModel{

	/**
	 * Returns user when a correct combination of login and password is given.
	 * 
	 * $user = User::Login("rambo","secret"); // returns user when login and password are correct
	 */
	static function Login($login,$password,&$bad_password = false){
		$bad_password = false;
	  $user = User::FindByLogin($login);
		if(!$user){ return; }
	  if($user->isPasswordCorrect($password)){
			return $user;
		}
		$bad_password = true;
	}

	/**
	 * $user = User::CreateNewRecord(array(
	 *  "login" => "rambo",
	 *  "password" => "secret"
	 * )); // returns user with hashed password
	 */
	static function CreateNewRecord($values,$options = array()){
		if(isset($values["password"])){
			$values["password"] = MyBlowfish::Hash($values["password"]);
		}

	  return parent::CreateNewRecord($values,$options);
	}

	/**
	 * On a record update it provides transparent password hashing
	 *
	 * A new password won't be stored in database in plain form:
	 *
	 *	 $rambo->setValues(array("password" => "secret123"));
	 *	 // or $rambo->setValue("password","secret123");
	 *	 // or $rambo->s("password","secret123");
	 */
	function setValues($values,$options = array()){
		if(isset($values["password"])){
			$values["password"] = MyBlowfish::Hash($values["password"]);
		}
		return parent::setValues($values,$options);
	}

	function isPasswordCorrect($password){
		return MyBlowfish::CheckPassword($password,$this->getPassword());
	}

	function isAdmin(){ return $this->getIsAdmin(); }

	function toString(){ return $this->getLogin(); }

	function isDeletable(){ return $this->getId()!=1; }
}

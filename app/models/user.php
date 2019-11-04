<?php
/**
 * Model class for user records
 *
 * Uses lib/myblowfish.php for passwords hashing.
 * See test/models/tc_user.php for basic usage.
 */
class User extends ApplicationModel{

	const ID_SUPERADMIN = 1;

	/**
	 * Returns user when a correct combination of login and password is given.
	 * 
	 * $user = User::Login("rambo","secret"); // returns user when login and password are correct
	 */
	static function Login($login,$password,&$bad_password = false){
		$bad_password = false;
	  $user = User::FindByLogin($login);
		if(!$user){ return; }
		if(!$user->isActive()){ return; }
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
			$values["password"] = MyBlowfish::Filter($values["password"]);
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
			$values["password"] = MyBlowfish::Filter($values["password"]);
		}
		return parent::setValues($values,$options);
	}

	function isPasswordCorrect($password){
		return MyBlowfish::CheckPassword($password,$this->getPassword());
	}

	function getName(){
		$name = trim($this->getFirstname()." ".$this->getLastname());
		if(strlen($name)){ return $name; }
		return $this->getLogin();
	}

	function isAdmin(){ return $this->getIsAdmin(); }

	function isSuperAdmin(){ return $this->getId()==self::ID_SUPERADMIN; }

	function toString(){ return (string)$this->getName(); }

	function isActive(){ return $this->g("active"); }

	function isDeletable(){ return !in_array($this->getId(),array(self::ID_SUPERADMIN)); }
}

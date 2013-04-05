<?php
class User extends ApplicationModel{

  /**
	 * Returns user when a correct combination of login and password is given.
	 * 
   * $user = User::Login("rambo","secret"); // returns user when login and password are correct
   */
  static function Login($login,$password){
    $user = User::FindByLogin($login);
    if($user && $user->isPasswordCorrect($password)){
      return $user;
    }
  }

  /**
   * $user = User::CreateNewRecord(array(
   *  "login" => "rambo",
   *  "password" => "secret"
   * )); // returns user with hashed password
   */
  static function CreateNewRecord($values,$options = array()){
		if(!MyBlowfish::IsHash($values["password"])){
			$values["password"] = MyBlowfish::GetHash($values["password"]);
		}
    return parent::CreateNewRecord($values,$options);
  }

	/**
	 *
	 * Among others provides transparent password hashing.
	 */
	function setValues($values,$options = array()){
		if(isset($values["password"]) && !MyBlowfish::IsHash($values["password"])){
			$values["password"] = MyBlowfish::GetHash($values["password"]);
		}
		return parent::setValues($values,$options);
	}

	function isPasswordCorrect($password){
		return MyBlowfish::CheckPassword($password,$this->getPassword());
	}

	function isAdmin(){ return $this->getIsAdmin(); }

	function toString(){ return $this->getLogin(); }
}

<?php
class PasswordRecovery extends ApplicationModel{
	function isActive(){
		return !$this->wasUsed() && (strtotime($this->getExpiryDate()) - time())>0;
	}
	function wasUsed(){
		return !is_null($this->getRecoveredAt());
	}
	function getExpiryDate(){
		return date("Y-m-d H:i:s",strtotime($this->getCreatedAt()) + 2 * 60 * 60); // 2 hours
	}
	function getUrl(){
		return Atk14Url::BuildLink(array(
			"namespace" => "",
			"action" => "password_recoveries/recovery",
			"token" => $this->getToken(),
		),array(
			"with_hostname" => true,
		));
	}
}

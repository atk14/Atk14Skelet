<?php
class NewsletterSubscriptionRequest extends ApplicationModel {
	
	static function CreateNewRecord($values,$options = []){
		global $ATK14_GLOBAL;

		$values += [
			"language" => $ATK14_GLOBAL->getLang(),
		];

		return parent::CreateNewRecord($values,$options);
	}

	function getConfirmationUrl($options = []){
		$options += [
			"with_hostname" => true,
		];
		return Atk14Url::BuildLink([
			"namespace" => "",
			"controller" => "newsletter_subscription_requests",
			"action" => "confirm",
			"token" => $this->getToken(),
		],$options);
	}

	function isConfirmed(){ return $this->g("confirmed"); }

	function isCancelled(){ return $this->g("cancelled"); }
}

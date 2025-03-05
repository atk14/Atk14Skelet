<?php
class InvalidLoginAttempt extends ApplicationModel {

	static function IsRemoteAddressBlocked($remote_addr,&$realease_time = null,$options = []){
		$realease_time = null;
		$options += [
			"current_time" => time()
		];

		$threshold = 5 * 60; // 5 minutes
		$max_attempts = 5;
		$current_time = $options["current_time"];

		$last_attempt = InvalidLoginAttempt::FindFirst("created_from_addr",$remote_addr,["order_by" => "created_at DESC"]);
		if(!$last_attempt){
			return false;
		}

		$last_attempt_time = strtotime($last_attempt->getCreatedAt());
		if(($current_time-$last_attempt_time)>=$threshold){
			return false;
		}

		$last_attempts = InvalidLoginAttempt::FindAll([
			"conditions" => [
				"created_from_addr" => $remote_addr,
				"created_at>:limit_date",
			],
			"bind_ar" => [
				":limit_date" => date("Y-m-d H:i:s",$last_attempt_time - $threshold),
			]
		]);
		
		if(sizeof($last_attempts)<$max_attempts){
			return false;
		}

		$realease_time = ($last_attempt_time + $threshold) - $current_time;
		return true;
	}

	static function BuildLoginAttemptDelayMessage($realease_time){
		$realease_time = (int)$realease_time;

		if($realease_time<2){
			return _("Delay the next sign-in attempt for a second");		}

		$minutes = floor($realease_time / 60);
		$seconds = $realease_time % 60;

		if($minutes>=3){
			$minutes = $seconds>=30 ? $minutes+1 : $minutes;
			return sprintf(_("Delay the next sign-in attempt for %s minutes"),$minutes);
		}

		if($minutes==1 || ($minutes==0 && $seconds>50)){
			if($minutes==1 && $seconds>5){
				return sprintf(_("Delay the next sign-in attempt for one minute and %s seconds"),$seconds);
			}
			return _("Delay the next sign-in attempt for one minute");
		}

		if($minutes==0){
			return sprintf(_("Delay the next sign-in attempt for %s seconds"),$seconds);
		}

		if($seconds>5){
			return sprintf(_("Delay the next sign-in attempt for %s minutes and %s seconds"),$minutes,$seconds);
		}
		return sprintf(_("Delay the next sign-in attempt for %s minutes"),$minutes);
	}
}

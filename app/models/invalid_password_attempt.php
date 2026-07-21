<?php
definedef("MAX_INVALID_LOGIN_ATTEMPTS",5);
class InvalidPasswordAttempt extends ApplicationModel {

	static function IsRemoteAddressBlocked($remote_addr,&$release_time = null,$options = []){
		$release_time = null;
		$options += [
			"purpose" => null, // e.g. "user_login_2fa"
			"object_key" => null, // e.g. "123"
			"current_time" => time(),
		];

		$base_threshold = 5 * 60;           // base lockout: 5 minutes
		$max_threshold = 60 * 60;           // maximum lockout: 60 minutes
		$max_lookback = 2 * $max_threshold; // lookback window for counting attempts: 2 hours
		$max_attempts = MAX_INVALID_LOGIN_ATTEMPTS;
		$current_time = $options["current_time"];

		$last_attempt = InvalidPasswordAttempt::FindFirst("created_from_addr",$remote_addr,["order_by" => "created_at DESC"]);
		if(!$last_attempt){
			return false;
		}

		$last_attempt_time = strtotime($last_attempt->getCreatedAt());
		if(($current_time - $last_attempt_time) >= $max_lookback){
			return false;
		}

		$conditions = $bind_ar = [];

		$conditions[] = "created_from_addr=:remote_addr";
		$conditions[] = "created_at>:limit_date";

		$bind_ar[":remote_addr"] = $remote_addr;
		$bind_ar[":limit_date"] = date("Y-m-d H:i:s",$current_time - $max_lookback);

		if(!is_null($options["purpose"])){
			$conditions[] = "purpose=:purpose";
			$bind_ar[":purpose"] = (string)$options["purpose"];
		}

		if(!is_null($options["object_key"])){
			$conditions[] = "object_key=:object_key";
			$bind_ar[":object_key"] = (string)$object_key;
		}

		// Load all attempts within the lookback window to determine the penalty round
		$recent_attempts = InvalidPasswordAttempt::FindAll([
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
		]);

		$total_recent = count($recent_attempts);
		if($total_recent < $max_attempts){
			return false;
		}

		// Exponential backoff: each additional batch of $max_attempts failures doubles the lockout
		// round 0 = 5 min, round 1 = 10 min, round 2 = 20 min, round 3 = 40 min, round 4+ = 60 min (cap)
		$block_round = max(0,(int)floor($total_recent / $max_attempts) - 1);
		$threshold = min((int)($base_threshold * pow(2,$block_round)),$max_threshold);

		if(($current_time - $last_attempt_time) >= $threshold){
			return false;
		}

		// Count attempts within the active penalty window
		$window_start = $last_attempt_time - $threshold;
		$window_count = count(array_filter($recent_attempts,function($a) use ($window_start){
			return strtotime($a->getCreatedAt()) > $window_start;
		}));

		if($window_count < $max_attempts){
			return false;
		}

		$release_time = ($last_attempt_time + $threshold) - $current_time;
		return true;
	}

	static function BuildNextAttemptDelayMessage($release_time,$style = "form"){
		$messages = [];
		$messages["form"] = [
			"less_than_2_sec" => _("Delay the form submission for a second"),
			"minutes" => _("Delay the form submission for %s minutes"),
			"one_minute_and_seconds" => _("Delay the form submission for one minute and %s seconds"),
			"one_minute" => _("Delay the form submission for one minute"),
			"seconds" => _("Delay the form submission for %s seconds"),
			"minutes_and_seconds" => _("Delay the form submission for %s minutes and %s seconds"),
		];
		$messages["login"] = [
			"less_than_2_sec" => _("Delay the next sign-in attempt for a second"),
			"minutes" => _("Delay the next sign-in attempt for %s minutes"),
			"one_minute_and_seconds" => _("Delay the next sign-in attempt for one minute and %s seconds"),
			"one_minute" => _("Delay the next sign-in attempt for one minute"),
			"seconds" => _("Delay the next sign-in attempt for %s seconds"),
			"minutes_and_seconds" => _("Delay the next sign-in attempt for %s minutes and %s seconds"),
		];

		$style = (string)$style;
		if(!isset($messages[$style])){
			$keys = array_keys($messages);
			$default_style = $keys[0];
			trigger_error("InvalidPasswordAttempt::BuildNextAttemptDelayMessage(): Unknown message style '$style'. The default style '$default_style' is used.");
			$style = $default_style;
		}

		$release_time = (int)$release_time;

		if($release_time<2){
			return $messages[$style]["less_than_2_sec"];
		}

		$minutes = floor($release_time / 60);
		$seconds = $release_time % 60;

		if($minutes>=3){
			$minutes = $seconds>=30 ? $minutes+1 : $minutes;
			return sprintf($messages[$style]["minutes"],$minutes);
		}

		if($minutes==1 || ($minutes==0 && $seconds>50)){
			if($minutes==1 && $seconds>5){
				return sprintf($messages[$style]["one_minute_and_seconds"],$seconds);
			}
			return $messages[$style]["one_minute"];
		}

		if($minutes==0){
			return sprintf($messages[$style]["seconds"],$seconds);
		}

		if($seconds>5){
			return sprintf($messages[$style]["minutes_and_seconds"],$minutes,$seconds);
		}
		return sprintf($messages[$style]["minutes"],$minutes);
	}

	/**
	 * Deletes old (useless) records
	 *
	 * Returns the count of deleted records
	 *
	 * @return int
	 */
	static function DeleteOldRecords(){
		$threshold_date = date("Y-m-d H:i:s",time() - 60 * 60 * 24 * 7); // 7 days
		$dbmole = self::GetDbmole();
		$dbmole->doQuery("DELETE FROM invalid_password_attempts WHERE created_at<=:threshold_date",[
			":threshold_date" => $threshold_date,
		]);
		return $dbmole->getAffectedRows();
	}
}

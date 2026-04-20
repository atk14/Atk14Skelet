<?php
definedef("MAX_INVALID_LOGIN_ATTEMPTS",5);
class InvalidLoginAttempt extends ApplicationModel {

	static function IsRemoteAddressBlocked($remote_addr,&$realease_time = null,$options = []){
		$realease_time = null;
		$options += [
			"current_time" => time()
		];

		$base_threshold = 5 * 60;           // base lockout: 5 minutes
		$max_threshold = 60 * 60;           // maximum lockout: 60 minutes
		$max_lookback = 2 * $max_threshold; // lookback window for counting attempts: 2 hours
		$max_attempts = MAX_INVALID_LOGIN_ATTEMPTS;
		$current_time = $options["current_time"];

		$last_attempt = InvalidLoginAttempt::FindFirst("created_from_addr",$remote_addr,["order_by" => "created_at DESC"]);
		if(!$last_attempt){
			return false;
		}

		$last_attempt_time = strtotime($last_attempt->getCreatedAt());
		if(($current_time - $last_attempt_time) >= $max_lookback){
			return false;
		}

		// Load all attempts within the lookback window to determine the penalty round
		$recent_attempts = InvalidLoginAttempt::FindAll([
			"conditions" => [
				"created_from_addr" => $remote_addr,
				"created_at>:limit_date",
			],
			"bind_ar" => [
				":limit_date" => date("Y-m-d H:i:s",$current_time - $max_lookback),
			]
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

		$realease_time = ($last_attempt_time + $threshold) - $current_time;
		return true;
	}

	static function BuildLoginAttemptDelayMessage($realease_time){
		$realease_time = (int)$realease_time;

		if($realease_time<2){
			return _("Delay the next sign-in attempt for a second");
		}

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

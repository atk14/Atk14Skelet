<?php
/**
 * Displays name of the given user
 *
 * {$user|user_name}
 */
function smarty_modifier_user_name($user){
	$user = is_object($user) ? $user : Cache::Get("User",$user);
	if(!$user){ return; }
	return $user->getName();
}

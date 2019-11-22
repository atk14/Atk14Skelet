<?php
/**
 * Converts phone number to a more natural form for man
 *
 *	{"+420.605123456"|display_phone} -> +420 605 123 456
 */
function smarty_modifier_display_phone($phone){
	if(!$phone){
		return;
	}

	$phone = preg_replace('/^(\+\d+)\.(\d{3})(\d{3})(\d+)$/','\1 \2 \3 \4',$phone);
	$phone = str_replace(' ',html_entity_decode('&nbsp;'),$phone);
	return $phone;
}

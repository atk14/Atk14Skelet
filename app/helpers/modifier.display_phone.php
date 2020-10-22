<?php
/**
 * Converts phone number to a more natural form for humans
 *
 *	{"+420.605123456"|display_phone} -> +420 605 123 456
 *	{!"+420.605123456"|display_phone:true} -> <a href="tel:+420605123456">+420 605 123 456</a>
 */
function smarty_modifier_display_phone($phone,$linkify = false){
	if(!$phone){
		return;
	}

	$out = preg_replace('/^(\+\d+)\.(\d{3})(\d{3})(\d+)$/','\1 \2 \3 \4',$phone);
	$out = str_replace(' ',html_entity_decode('&nbsp;'),$out);
	if($linkify){
		$out = sprintf('<a href="tel:%s">%s</a>',h(str_replace('.','',$phone)),h($out));
	}
	return $out;
}

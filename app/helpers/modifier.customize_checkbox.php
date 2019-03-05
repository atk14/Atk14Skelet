<?php
/**
 * It adds into a <input type="checkbox"> new attribute or update the existing one: class="custom-control-input"
 *
 * It is useful for Bootstrap4
 */
function smarty_modifier_customize_checkbox($checkbox){
	if(preg_match('/class="/',$checkbox)){
		$checkbox = preg_replace('/class="/','class="custom-control-input ',$checkbox);
		return $checkbox;
	}
	$checkbox = preg_replace('/<input/','<input class="custom-control-input"',$checkbox);
	return $checkbox;
}

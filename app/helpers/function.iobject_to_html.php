<?php
/**
 * Renders an Iobject into HTML
 *
 * It uses a proper template in app/views/shared/helpers/iobjects/
 * 
 * {iobject_to_html iobject=$iobject}
 */
function smarty_function_iobject_to_html($params,$template){
	$smarty = atk14_get_smarty_from_template($template);

	$iobject = $params["iobject"];
	$theme = $iobject->getTheme(); // "default", "metalic", "matte"

	$tpl_name = $object_name = String4::ToObject($iobject->getObjectType())->underscore()->toString(); // "DynamicMap" -> "dynamic_map"
	if($theme!=="default" && strlen($theme)){
		$tpl_name .= ".$theme"; // dynamic_map.metalic
	}

	$smarty->assign("iobject",$iobject);
	$smarty->assign($object_name,$iobject->getObject());


	$out = $smarty->fetch("shared/helpers/iobjects/_$tpl_name.tpl");

	// rendering admin menu for the object
	Atk14Require::Helper("function.admin_menu");
	$admin_menu .= smarty_function_admin_menu([
		"for" => $iobject->getObject(),
	],$template);
	$admin_menu = trim($admin_menu);

	if(strlen($admin_menu)>0){
		if(preg_match('/^\s*<.*?>/s',$out)){
			// placing $admin_menu after the first opening tag
			$out = preg_replace('/^(\s*<.*?>)/s','\1'.$admin_menu,$out);
		}else{
			$out = $admin_menu.$out;
		}
	}

	return $out;
}

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

	return $smarty->fetch("shared/helpers/iobjects/_$tpl_name.tpl");
}

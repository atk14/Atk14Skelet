<?php
/**
 *
 * Usage:
 *
 *	{js_notify type="success"}Successfully saved{/js_notify}
 *
 * See:
 * - window.UTILS.Notifications
 */
function smarty_block_js_notify($params,$content,$template,&$repeat){
	if($repeat){ return; }
	$smarty = atk14_get_smarty_from_template($template);

	$params += array(
		"type" => "info",
	);

	$message = $content;
	if(!strlen($message)){ return; }

	$tr_table = array(
		"error" => "danger",
	);
	$type = $params["type"];
	$type = isset($tr_table[$type]) ? $tr_table[$type] : $type;

	$options = array(
		"message" => $message,
	);

	$settings = array(
		"type" => $type,
		"delay" => 4000,
	);

	return sprintf('window.UTILS.Notifications.show(%s,%s);',json_encode($options),json_encode($settings));
}

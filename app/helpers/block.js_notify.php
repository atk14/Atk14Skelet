<?php
/**
 *
 * Usage:
 *
 *	{js_notify type="success"}Successfully saved{/js_notify}
 *
 * See:
 * - http://bootstrap-notify.remabledesigns.com/
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
		"delay" => 2000,
		"animate" => array(
			"enter" => "animated fadeInUp",
			"exit" => "animated fadeOutDown"
		),
		"placement" => array(
			"from" => "bottom",
			"align" => "right"
		)
	);

	//return sprintf('$.notify(%s,%s);',json_encode($options),json_encode($settings));
	return sprintf('window.UTILS.Notifications.show(%s,%s);',json_encode($options),json_encode($settings));
}

<?php
/**
 * {message}Message text{/message}
 *
 * {message type="info" close_button=0 block=0}Message text{/message}
 * {message type="error" close_button=1 block=1}Error message{/message}
 * {message type="warning" close_button=1 block=1}Warning! Something is going to be bad...{/message}
 * {message type="success" close_button=1 block=1}Congrats! You`ve made it!{/message}
 */
function smarty_block_message($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += array(
		"type" => "warning",
		"close_button" => 1,
		"block" => 0
	);

	$classes = array();

	$classes[] = "alert";
	$classes[] = "alert-$params[type]";

	$params["close_button"] && ( $content = '<button type="button" class="close" data-dismiss="alert">&times;</button>' . $content );

	return '<div class="'.join(" ",$classes).'">'.$content.'</div>';
}

<?php
if(!function_exists("smarty_modifier_markdown")){
	require_once(__DIR__ . "/modifier.markdown.php");
}

/**
 * Block markdown helper
 *
 *	{markdown}
 *	# Hi there!
 *
 *	Welcome to our brand new website.
 *	{/markdown}
 */
function smarty_block_markdown($params,$content,$template,&$repeat){
	if($repeat){ return; }

	return smarty_modifier_markdown($content);
}

<?php
/**
 * Removes content when it contains only html tags with no visible textual content
 *
 *	{remove_if_contains_no_text}
 *		<p>
 *			{$message}
 *		</p>
 *	{/remove_if_contains_no_text}
 */
function smarty_block_remove_if_contains_no_text($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$_content = $content;
	$_content = strip_tags($_content);
	$_content = trim($_content);
	if(strlen($_content)>0){
		return $content;
	}
}

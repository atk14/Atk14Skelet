<?php
/**
 *
 *	{highlight_search_query}
 *		<tr>
 *			<td>{$user->getLogin()}</td>
 *			<td>{$user->getName()}</td>
 *		</tr>
 *	{/highlight_search_query}
 */
function smarty_block_highlight_search_query($params,$content,$template,&$repeat){

	Atk14Require::Helper("block.highlight_keywords");

	$_params = $template->getTemplateVars("params");
	if($_params && is_a($_params,"Dictionary")){
		$params["keywords"] = $_params->getString("search");
	}

	return smarty_block_highlight_keywords($params,$content,$template,$repeat);
}

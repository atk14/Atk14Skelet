<?php
/**
 *
 *	{highlight_keywords keywords="pizza beer"}
 *		<h1>The truth about pizza & beer</h1>
 *	{/highlight_keywords}
 */
function smarty_block_highlight_keywords($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"keywords" => "",
		"tag" => "", // <span style="background-color: #ffff00;">
	];

	if($params["tag"] && preg_match("/^<([^ ]+).*>/",$params["tag"],$matches)){
		$params["opening_tag"] = $params["tag"];
		$params["closing_tag"] = "</$matches[1]>";
	}

	$params += [
		"opening_tag" => '<mark>',
		"closing_tag" => '</mark>',
	];

	$keywords = $params["keywords"];
	$keywords = trim((string)$keywords);
	if(!strlen($keywords)){ return $content; }

	if(preg_match('/([^\s]+)[^\s]$/u',$keywords,$matches) && strlen($matches[1])>=3){
		$keywords .= " $matches[1]"; // "girls boys" -> "girls boys boy"; "lísteče" -> "lísteče lísteč"
	}

	$kh = new \Yarri\KeywordsHighlighter([
		"opening_tag" => $params["opening_tag"],
		"closing_tag" => $params["closing_tag"],
	]);

	return $kh->highlight($content,$keywords);
}

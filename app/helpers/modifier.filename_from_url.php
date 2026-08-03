<?php
/**
 *
 *	{$url|filename_from_url}
 */
function smarty_modifier_filename_from_url($url){
	if(!$url){ return ""; }

	$url = preg_replace('/\?.*$/','',$url);
	$filename = preg_replace('/^(.*\/)(.*?)$/','\2',$url);
	if(!strlen($filename)){ $filename = "file"; }
	return $filename;
}

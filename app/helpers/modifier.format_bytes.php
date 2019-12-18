<?php
/**
 *
 *	{$bytes|format_bytes} --> 1,2 KB
 */
function smarty_modifier_format_bytes($bytes){
	if(!strlen($bytes)){ return ""; }
	if($bytes>=(1000*1024*1024*1024)){ return _format_bytes(($bytes/(1024 * 1024 * 1024 * 1024)),"TB"); }
	if($bytes>=(1000*1024*1024)){ return _format_bytes(($bytes/(1024 * 1024 * 1024)),"GB"); }
	if($bytes>=(1000*1024)){ return _format_bytes(($bytes/(1024 * 1024)),"MB"); }
	if($bytes>=1000){ return _format_bytes(($bytes/1024),"kB"); }
	$bytes = (int)$bytes;
	return $bytes." Bytes";
}

function _format_bytes($mnozstvi,$jednotka){
	$mnozstvi = number_format($mnozstvi,1,".","");
	return Atk14Locale::FormatNumber($mnozstvi)." $jednotka";
}

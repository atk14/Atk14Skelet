<?php
/**
* {$bytes|format_bytes} --> 1,2 KB
*/
function smarty_modifier_format_bytes($bytes){
	if($bytes>(1024*1024)){ return _format_bytes(($bytes/(1024 * 1024)),"MB"); }
	if($bytes>1024){ return _format_bytes(($bytes/1024),"kB"); }
	return "$bytes Bytes";
}

function _format_bytes($mnozstvi,$jedotka){
	return number_format($mnozstvi,1,",","")." $jedotka";
}

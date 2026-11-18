<?php
require(__DIR__."/atk14/load.php");

// In DEVELOPMENT, an explicit Content-Length header prevents Tracy from appending
// its debug bar loader to the response (it logs "Tracy cannot display the Bar
// because the Content-Length header is being sent"), on error pages (404, ...)
// as well as regular ones. So here the response is flushed without it.
if(DEVELOPMENT){
	$response = Atk14Dispatcher::Dispatch(["display_response" => false]);
	$response->flush(); // unlike flushAll(), this never sends a Content-Length header
}else{
	Atk14Dispatcher::Dispatch();
}

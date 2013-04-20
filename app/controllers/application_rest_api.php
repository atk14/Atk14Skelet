<?php
// TODO: vsechny hlasky do anglictiny a pak lokalizovat gettextem

class ApplicationRestApiController extends ApplicationBaseController{
	var $api_status_code = null; // automaticky je status code nastaven na 200 resp. 400 pri neuspechu; zde je to mozne zmenit
	var $api_root_element = null; // pokud nevyhovuje automaticke urceni root elementu pro XML, timto je mozne nastavit (napr. u kontroleru ActiveUsersControllers muzeme nastavit api_root_element na users)
	var $api_data = null; // pole dat, ktere ma byt vyrenderovano

	var $api_internal_charset = DEFAULT_CHARSET;

	/**
	 * Vygeneruje seznam prikazu.
	 *
	 * $this->_render_command_list("/home/user/www/project/app/controllers/api/");
	 */
	function _render_command_list($dir){
		$controllers = array();

		$dir_handle = opendir($dir);
		$ctrls = array();
		while($item = readdir($dir_handle)){
			if(preg_match('/^([a-z0-9_]+)_controller\./',$item,$matches)){
				$ctrl = $matches[1];
				if(in_array($ctrl,array("main","application"))){ continue; }
				$ctrls[$ctrl] = $item;
			}
		}
		ksort($ctrls);
		foreach($ctrls as $ctrl => $item){
			$commands = array();
			$content = files::get_file_content("$dir/$item");
			preg_match_all('/(\/\*.*?\*\/|)\s*\n\s*function ([a-z][a-z_]*)/s',$content,$matches);
			foreach($matches[2] as $action){
				$documentation_ar = $this->__get_documentation_from_source_code($content,$action);

				$commands[] = array(
					"action" => $action,
					"url" => $this->_link_to(array("controller" => $ctrl, "action" => $action)),
					"inline_description" => $documentation_ar["inline_description"],
				);
			}

			$controllers[$ctrl] = array(
				"commands" => $commands
			);	
		}

		$this->tpl_data["controllers"] = $controllers;

		$this->template_name = "shared/rest_api/command_list";
		$this->page_title = "Command list";
	}

	/**
	 * Vyhleda a vyparsuje komentar k funkci daneho kontroleru.
	 *
	 * Komentar musi vypada takto:
	 *  /**
	 *   * Jednoradkovy popis funkce
	 *   * 
	 *   * Dalsi popis. Pouzivejte Markdown syntax!
	 *   *
	 *   * ... a konec komentare na samostatnem radku (mezera tam byt samozrejme nema...)
	 *   * /
	 *  
	 * $documentation_ar = $this->__get_documentation_from_source_code("..../article_controller.php","detail");
	 * $documentation_ar = $this->__get_documentation_from_source_code($file_content,"detail");
	 * echo "<h2>".$documentation_ar["inline_description"]."</h2>";
	 * echo $documentation_ar["documentation"];
	 */
	function __get_documentation_from_source_code($controller_filename,$function_name,$options = array()){
		$out = array(
			"inline_description" => "",
			"documentation" => "",
		);

		if(!$controller_filename){ return $out; }

		if(preg_match('/\n/s',$controller_filename) || !file_exists($controller_filename)){
			$content = $controller_filename;
		}else{
			$content = files::get_file_content($controller_filename);
		}

		if(preg_match('/\s*(\/\*\*.*?\*\/)\s*\n\s*function ('.$function_name.')\(/s',$content,$matches)){

			// toto je velmi podivne, v predchozi regularace nezafungovalo nehladove vyhledavani
			// a chyta se tak zacatek komentare, ktery patri nejake jine funkci...
			$matches[1] = preg_replace('/^.*\n\s*\n\s*(\/\*.*?)$/s','\1',trim($matches[1]));

			$lines = split("\n",trim($matches[1]));
			array_shift($lines); // zacatek komentare
			array_pop($lines); // konec komentare
			foreach($lines as &$line){
				$line = preg_replace('/^\s*\*/','',$line); // odstraneni hvezdicky
				$line = preg_replace('/^(\t)+/e','str_repeat("  ",strlen("\1"))',$line); // tabulator na zacatku radku se nahradi dvema mezerama
				$line = preg_replace('/^ /','',$line); // prvni mezera se odstrani
				$line = rtrim($line); // bile znaky na konci musi pryc!
			}
			$out["documentation"] = Markdown(trim(join("\n",$lines)));
			$out["inline_description"] = isset($lines[0]) ? trim($lines[0]) : "";
		}

		return $out;
	}

	//function _get_session(){ return null; }

	function _application_before_filter(){
		$this->response->setContentCharset("UTF-8");

		$this->page_title = $this->controller.($this->action=="index" ? "" : "/$this->action");
		$this->page_description = "";

		$this->params = new Dictionary(translate::trans($this->params->toArray(),"utf-8",$this->api_internal_charset));
		$this->logger->set_prefix($this->namespace);

		$this->template_name = "shared/rest_api/generic";
	}

	function _after_filter(){
		// prevod obsahu na UTF-8: docela netransparentni oser :)
		if($this->render_template){ // pokud se uz zobrazuje vystup (_display_output()), nedelame nic
			$ob = $this->response->getOutputBuffer();
			$content = translate::trans($ob->toString(),$this->api_internal_charset,"utf-8");
			$ob->clear();
			$ob->addString($content);
		}
	}

	function _before_render(){
		if($this->controller!="main"){
			// dokumentace 
			$filename = "";
			foreach(array("inc","php") as $suffix){
				if(file_exists($_f = ATK14_DOCUMENT_ROOT."app/controllers/$this->namespace/{$this->controller}_controller.$suffix")){
					$filename = $_f;
				}
			}
			$documentation_ar = $this->__get_documentation_from_source_code($filename,$this->action);
			if($documentation_ar["inline_description"]){
				$this->page_description = $documentation_ar["inline_description"];
			}
			$this->tpl_data["documentation"] = $documentation_ar["documentation"];

		}
		$this->tpl_data["button_text"] = sprintf(_("submit using method %s"),strtoupper($this->form->get_method()));
		$this->layout_name = "rest_api/default";
		if(($this->request->post() || !$this->params->isEmpty()) && $this->form->has_errors()){
			$this->_report_form_errors($this->form);
		}
		if(isset($this->api_data)){
			if(!isset($this->api_status_code)){
				$this->api_status_code = in_array($this->action,array("create_new")) ? 201 : 200;  
			}
			$this->_display_output($this->api_data,array(
				"status_code" => $this->api_status_code,
			));
		}
	}

	function error404(){
		return $this->_report_fail(_("Nothing has been found on this address"),array(
			"status_code" => 404,
		));
	}

	function error500(){
		return $this->_report_fail(_("Sorry, An internal error happened"),array(
			"status_code" => 500,
		));
	}

	/**
	 * Kladna odpoved z API.
	 * 
	 * $this->_report_success(array("id" => "123"));
	 * 
	 * Priklad odeslani PDF prilohy:
	 * 
	 *	$this->_report_success(array(),array(
	 *		"content_type" => "application/pdf",
	 *		"raw_data" => $pdf->getBody(),
	 *	));
	 */
	function _report_success($data = array(),$options = array()){
		$options = array_merge(array(
			"status_code" => 200,
			"status_message" => null,
		),$options);
		$this->_display_output($data,$options);
	}

	function _report_form_errors($form,$options = array()){
		$options = array_merge(array(
			"status_code" => isset($this->api_status_code) ? $this->api_status_code : $form->error_http_status_code,
		),$options);
		$messages = array();
		foreach($form->get_errors() as $_key => $_messages){	
			if(sizeof($_messages)==0){ continue; }
 		  $_prefix = strlen($_key) ? "$_key: " : "";
			foreach($_messages as $_m){
				$messages[] = $_prefix.$_m;
			}
		}

		$this->_report_fail($messages,$options);
	}

	function _report_fail($messages = array(),$options = array()){
		$options = array_merge(array(
			"status_code" => (isset($this->api_status_code) ? $this->api_status_code : 400), // Bad Request
			"status_message" => null,
			"root_element" => "error_messages",
		),$options);
		$data = is_array($messages) ? $messages : array($messages);
		$this->_display_output($data,$options);
	}

	function _display_output($data,$options = array()){
		$s = new String($this->controller);

		$options = array_merge(array(
			"root_element" => $this->api_root_element ? $this->api_root_element : (in_array($this->action,array("index")) ? $s : $s->singularize()),

			"content_type" => null,
			"raw_data" => null,

			"status_message" => null,
		),$options);

		$this->render_template = false;

		if(isset($options["raw_data"])){
			$this->response->setContentType($options["content_type"]);
			$this->response->write($options["raw_data"]);
			return;
		}

		$this->response->setStatusCode($options["status_code"],$options["status_message"]);

		$data = translate::trans($data,$this->api_internal_charset,"utf-8");

		switch($this->params->getString("format")){
			case "xml":
				$this->response->setContentType("text/xml");
				$this->response->write($this->_array_to_xml($data,$options["root_element"]));
				break;
			case "yaml":
				$this->response->setContentType("text/plain")	;
				$this->response->write(miniYAML::Dump($data));
				break;
			case "jsonp":
				$this->response->setContentType("text/plain");
				$this->response->write(__render_jsonp($data,$options["root_element"]));
				break;
			default:
				$this->response->setContentType("text/plain");
				$this->response->write(json_encode($data));
		}
	}

	function _array_to_xml($ar,$root_element = "",$_first_call = true){
		$out = array();
		if($_first_call){ $out[] = '<'.'?xml version="1.0" encoding="utf-8"?'.'>'; }
		if($root_element){ $out[] = "<$root_element>"; }
		foreach($ar as $k => $v){
			if(is_numeric($k)){
				$k = "item";
				if($root_element){
					// "editions" -> "edition"
					$k = new String($root_element);
					$k = $k->singularize();
				}
			}
			if(is_array($v)){
				$out[] = $this->_array_to_xml($v,$k,false);
			}else{
				if(is_bool($v)){
					$v = $v ? "true" : "false";
				}
				$out[] = "<$k>".XMole::ToXML($v)."</$k>";
			}
		}
		if($root_element){ $out[] = "</$root_element>"; }
		return join("\n",$out);
	}
}


// zachycovani DbMole chyb... TODO: slocit kod s generovanim odpovedi v kontroleru
DbMole::RegisterErrorHandler("_rest_api_dbmole_error_handler");
function _rest_api_dbmole_error_handler($dbmole){
	global $HTTP_RESPONSE,$HTTP_REQUEST;
	$HTTP_RESPONSE->setStatusCode(500);
	$HTTP_RESPONSE->setContentCharset('UTF-8');

	$msg = translate::trans("Do?lo k vnit?n? chyb?",DEFAULT_CHARSET,"utf-8");

	if($HTTP_REQUEST->getVar("format")=="json"){
		$HTTP_RESPONSE->setContentType('text/plain');
		$HTTP_RESPONSE->write(json_encode(array($msg)));
	}elseif($HTTP_REQUEST->getVar("format")=="jsonp"){
		$HTTP_RESPONSE->setContentType('text/plain');
		$HTTP_RESPONSE->write(__render_jsonp(array($msg),"error_messages"));
	}elseif($HTTP_REQUEST->getVar("format")=="yaml"){
		$HTTP_RESPONSE->setContentType('text/plain');
		$HTTP_RESPONSE->write(miniYAML::Dump(array($msg)));
	}else{
		$HTTP_RESPONSE->setContentType('text/xml');
		$HTTP_RESPONSE->write('<'.'?xml version="1.0" encoding="utf-8"?'.'>'."\n<error_messages>\n<error_message>$msg</error_message>\n</error_messages>");
	}
	$HTTP_RESPONSE->flushAll();
	$dbmole->sendErrorReportToEmail(ATK14_ADMIN_EMAIL);
	$dbmole->logErrorReport();
	exit;
}

/**
 * $out = __render_jsonp($data_ar,"article","setJsonp");
 */
function __render_jsonp($data,$var_name,$method = "setJsonp"){
	return "$method(".json_encode($data).",'$var_name');";
}

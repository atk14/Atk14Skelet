<?php
class ApplicationBaseController extends Atk14Controller{
	function index(){
		// acts like there's no index action by default
		$this->_execute_action("error404");
	}

	function error404(){
		$this->page_title = "Page not found";
		$this->response->setStatusCode(404);
		$this->template_name = "application/error404";
		if($this->request->xhr()){
			// there's no need to render anything for XHR requests
			$this->render_template = false;
		}
	}

	function _initialize(){
		$this->_prepend_before_filter("application_before_filter");

		if(!$this->rendering_component){
			// keep these lines at the end of the _initialize() method
			$this->_prepend_before_filter("begin_database_transaction"); // _begin_database_transaction() is the very first before filter
			$this->_append_after_filter("end_database_transaction"); // _end_database_transaction() is the very last after filter
		}
	}

	function _before_filter(){
		// therer's nothing here...
		// you can safely cover this method in a descendand without calling the parent
	}

	function _application_before_filter(){
		$this->response->setContentType("text/html");
		$this->response->setContentCharset(DEFAULT_CHARSET);

		// following header helps to avoid clickjacking attacks
		$this->response->setHeader("X-Frame-Options","SAMEORIGIN"); // SAMEORIGIN, DENY

		// logged in user
		$this->logged_user = null;
		if($user_id = $this->session->g("logged_user_id")){
			$this->logged_user = $this->tpl_data["logged_user"] = User::GetInstanceById($user_id);
		}

		if($this->_logged_user_required() && !$this->logged_user){
			return $this->_execute_action("error403");
		}
	}

	function _login_user($user){
		$this->session->s("logged_user_id",$user->getId());
		$this->session->changeSecretToken(); // prevent from session fixation
	}

	function _begin_database_transaction(){
		$this->dbmole->begin();
	}

	function _end_database_transaction(){
		if(TEST){ return; } // perhaps you don't want to commit a transaction when you are testing
		$this->dbmole->commit();
	}

	function _get_logged_user(){
		return $this->logged_user;
	}

	function _logged_user_required(){
		return false;
	}

	/**
	 * Attempt to instantiate object by object_name and parameter
	 * 
	 * $this->_find("user");
	 * $this->_find("static_page","page_id");
	 * $this->_find("static_page",array(
	 *		"key" => "page_id",
	 *		"execute_error404_if_not_found" => false,
	 * ));
	 *
	 * When an object is instantiated it can by found as
	 *	$this->user
	 *	$this->tpl_data["user"]
	 */
	function &_find($object_name,$options = array()){
		if(is_string($options)){
			$options = array("key" => $options);
		}

		$options += array(
			"key" => "id",
			"execute_error404_if_not_found" => true,
		);

		$class_name = String::ToObject($object_name)->camelize()->toString(); // static_page -> StaticPage

		$key = $options["key"];

		// eval je tady kvuli PHP5.2, PHP4...
		eval("\$this->$object_name = $class_name::GetInstanceById(\$this->params->getInt(\$key));");

		if(!$this->$object_name){
			$options["execute_error404_if_not_found"] && $this->_execute_action("error404");
		}
		$this->tpl_data["$object_name"] = $this->$object_name;
		return $this->$object_name;
	}

	/**
	 * Adds return_uri to the given form to it's hidden parameters.
	 *
	 * $this->_save_return_uri();
	 * $this->_save_return_uri($this->form);
	 * 
	 */
	function _save_return_uri(&$form = null){
		if(!isset($form)){ $form = $this->form; }
		$return_uri = $this->params->defined("_return_uri_") ? $this->params->getString("_return_uri_") : $this->request->getHttpReferer();
		$form->set_hidden_field("_return_uri_",$return_uri);
	}


	/**
	 * Provede presmerovani podle parametru return_uri nebo na danou akci.
	 * Redirects user 
	 *
	 * $this->_redirect_to_return_uri(); // to same jako "index" :)
	 * $this->_redirect_to_return_uri("index");
	 * $this->_redirect_to_return_uri("books/index");
	 * $this->_redirect_to_return_uri($this->_link_to(array(...)));
	 * $this->_redirect_to_return_uri("http://www.poctenicko.cz/");
	 */
	function _redirect_back($default = "index"){
		if($return_uri = $this->params->g("_return_uri_")){
			return $this->_redirect_to($return_uri);
		}
		if(is_array($default)){
			$default = $this->_link_to($default);
		}
		return $this->_redirect_to($default);
	}
}

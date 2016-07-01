<?php
require_once(dirname(__FILE__)."/../application_base.php");

class AdminController extends ApplicationBaseController{
	function _application_before_filter(){
		parent::_application_before_filter();

		$this->breadcrumbs[] = array(_("Administration"), $this->_link_to(array("namespace" => "admin", "action" => "main/index")));

		if(!$this->logged_user || !$this->logged_user->isAdmin()){
			if($this->controller=="main" && $this->action=="index" && $this->request->get() && !$this->logged_user){
				// in the case that this is the main page of administration
				// we can simply redirect not-logged user to the login form
				return $this->_redirect_to(array(
					"namespace" => "",
					"action" => "logins/create_new",
					"return_uri" => $this->request->getUri(),
				));
			}

			return $this->_execute_action("error403");
		}

		$navi = new Navigation();

		foreach(array(
			array(_("Welcome screen"),			"main"),
			array(_("Articles"),						"articles"),
			array(_("Tags"),								"tags"),
			array(_("Users"),								"users"),
			array(_("Password recoveries"),	"password_recoveries"),
			array(_("Newsletter subscribers"), "newsletter_subscribers"),
		) as $item){
			$_label = $item[0];
			$_controllers = explode(',',$item[1]); // "products,cards" => array("products","cards");
			$_action = "$_controllers[0]/index"; // "products" -> "products/index"
			$_url = $this->_link_to($_action);
			$navi->add($_label,$_url,array("active" => in_array($this->controller,$_controllers)));
			if(in_array($this->controller,$_controllers)){
				$this->breadcrumbs[] = array($_label,$this->_link_to("$_controllers[0]/index"));
			}
		}

		$this->tpl_data["section_navigation"] = $navi;
	}

	function _before_render(){
		// auto breadcrumbs
		if($this->action!="index" && !preg_match('/^error/',$this->action)){ // error404 or error403
			$this->breadcrumbs[] = $this->page_title;
		}
		parent::_before_render();
	}

	/**
	 * Generic method for deleting a record
	 *
	 * $this->_destroy($this->article);
	 * $this->_destroy(); // the object for deletion will be determined by the controller name
	 */
	function _destroy($object = null){
		if(!$this->request->post()){ return $this->_execute_action("error404"); }

		if(!$object){
			$cn = String4::ToObject(get_class($this));
			$cn = $cn->gsub('/Controller$/',''); // "NiceImagesController" -> "NiceImages"
			$cn = $cn->underscore(); // "nice_images"
			$o_name = $cn->singularize()->toString(); // "nice_images" -> "nice_image"
			$object = $this->$o_name; // $this->nice_image
		}

		$object->destroy();
		$this->template_name = "application/destroy";

		if(!$this->request->xhr()){
			$this->flash->success(_("The entry has been deleted"));
			$this->_redirect_to("index");
		}
	}
}

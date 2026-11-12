<?php
require_once(__DIR__."/../application_base.php");
require_once(__DIR__."/../trait_crud_actions.php");

class AdminController extends ApplicationBaseController{

	use TraitCrudActions;

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

		$navi = new Menu14();

		$items = array(
			array(_("Welcome screen"),			"main"),
			array(_("Articles"),						"articles"),
			array(_("Pages"),								"pages"),
			array(_("Link Lists"),					"link_lists,link_list_items"),
			array(_("Tags"),								"tags"),
			array(_("Users"),								"users"),
			array(_("Password recoveries"),	"password_recoveries"),
			array(_("Newsletter subscribers"), "newsletter_subscribers"),
			array(_("404 Redirections"),				"error_redirections"),
		);

		foreach($items as $item){
			$_label = $item[0];
			$_controllers = explode(',',$item[1]); // "products,cards" => ["products","cards"];
			$_url = preg_match('/^\//',$_controllers[0]) ?
				$_controllers[0] :
				$this->_link_to(strpos($_controllers[0],"/") ? $_controllers[0] : "$_controllers[0]/index");
			$_controllers_filtered = array_map(function($c){ return preg_replace('/\/.*$/','',$c); },$_controllers); // ["products","product_reports/list","/en/best_selling_products/"] -> ["products","product_reports",""]
			$navi->add($_label,$_url,array("active" => in_array($this->controller,$_controllers_filtered)));
			if(in_array($this->controller,$_controllers_filtered)){
				$this->breadcrumbs[] = array($_label,$_url);
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

	function _add_page_to_breadcrumbs($page){
		if(!$page){ return; }
		$pages = [$page];
		while($parent = $page->getParentPage()){
			$pages[] = $parent;
			$page = $parent;
		}
		foreach(array_reverse($pages) as $p){
			$this->breadcrumbs[] = [$p->getTitle(),$this->_link_to(["action" => "pages/edit", "id" => $p])];
		}
	}

	function _add_gallery_to_breadcrumbs($gallery){
		if(!$gallery){ return; }
		$title = _("Fotogalerie");
		if($gallery->getTitle()){
			$title .= ": ".$gallery->getTitle();
		}
		$link = ["action" => "galleries/detail", "id" => $gallery];
		$this->_add_something_to_breadcrumbs($title,$link);
	}
}

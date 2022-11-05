<?php
class ApplicationBaseController extends Atk14Controller{

	/**
	 * @var User
	 */
	var $logged_user;

	/**
	 * @var Menu14
	 */
	var $breadcrumbs;

	/**
	 * Collector for meta and link tags belonging to head.
	 *
	 * @var HeadTags
	 */
	var $head_tags;

	function error404(){
		if($this->_redirected_on_error404()){
			return;
		}

		$this->response->setStatusCode(404);

		if($this->request->xhr()){
			// there's no need to render anything for XHR requests
			$this->render_template = false;
			return;
		}

		$this->template_name = "application/error404"; // see app/views/application/error404.tpl
		$this->page_title = $this->breadcrumbs[] = _("Page not found");
	}

	function _redirected_on_error404(){
		if($this->request->get() && !$this->request->xhr() && ($redirection = ErrorRedirection::GetInstanceByHttpRequest($this->request))){
			$redirection->touch();
			$this->_redirect_to($redirection->getDestinationUrl(),array(
        "moved_permanently" => $redirection->movedPermanently(),
      ));
			return true;
		}
	}

	function error403(){
		$this->page_title = $this->breadcrumbs[] = _("Forbidden");
		$this->response->setStatusCode(403);
		$this->template_name = "application/error403";
		if($this->request->xhr()){
			// there's no need to render anything for XHR requests
			$this->render_template = false;
		}
	}

	function _initialize(){
		$this->_prepend_before_filter("application_before_filter");

		if(!$this->rendering_component){
			// keep these lines at the end of the _initialize() method
			$this->_prepend_before_filter("begin_database_transaction"); // _begin_database_transaction() is the very first filter
			$this->_append_after_filter("application_after_filter");
			$this->_append_after_filter("end_database_transaction"); // _end_database_transaction() is the very last filter
		}
	}

	function _before_filter(){
		// there's nothing here...
		// you can safely cover this method in a descendand without calling the parent
	}

	function _before_render(){
		global $ATK14_GLOBAL;

		if(!isset($this->tpl_data["breadcrumbs"]) && isset($this->breadcrumbs)){
			$this->tpl_data["breadcrumbs"] = $this->breadcrumbs;
		}
		if(!isset($this->tpl_data["head_tags"]) && isset($this->head_tags)){
			$this->tpl_data["head_tags"] = $this->head_tags;
		}


		// data for language swith, see app/views/shared/_langswitch.tpl
		$languages = array();
		$current_language = null;
		$params_homepage = array("namespace" => "", "controller" => "main", "action" => "index");
		$params = ($this->request->get() && !preg_match('/^error/',$this->action)) ? $this->params->toArray() : $params_homepage;
		foreach($ATK14_GLOBAL->getConfig("locale") as $l => $locale){
			$params["lang"] = $l;
			$item = array(
				"lang" => $l,
				"name" => isset($locale["name"]) ? $locale["name"] : $l,
				"switch_url" => $this->_link_to($params,["with_hostname" => true]),
			);
			if($this->lang==$l){
				$current_language = $item;
				continue;
			}
			$languages[] = $item;
		}
		$this->tpl_data["current_language"] = $current_language;
		$this->tpl_data["supported_languages"] = $languages;

		// It's better to write
		//	{$val|default:$mdash}
		// than
		//	{!$val|h|default:"&mdash;"}
		$this->tpl_data["mdash"] = "—";
		$this->tpl_data["nbsp"] = " ";
	}

	function _application_before_filter(){
		global $ATK14_GLOBAL;

		$this->response->setContentType("text/html");
		$this->response->setContentCharset(DEFAULT_CHARSET);
		$this->response->setHeader("Cache-Control","private, max-age=0, must-revalidate");
		$this->response->setHeader("Pragma","no-cache");

		// security headers
		$this->response->setHeader("X-Frame-Options","SAMEORIGIN"); // avoiding clickjacking attacks; "SAMEORIGIN", "DENY"
		$this->response->setHeader("X-XSS-Protection","1; mode=block");
		$this->response->setHeader("Referrer-Policy","same-origin"); // "same-origin", "strict-origin", "strict-origin-when-cross-origin"...
		$this->response->setHeader("X-Content-Type-Options","nosniff");
		//$this->response->setHeader("Content-Security-Policy","default-src 'self' data: 'unsafe-inline' 'unsafe-eval'");

		$this->response->setHeader("X-Powered-By","ATK14 Framework");

		if(
			(PRODUCTION && $this->request->get() && !$this->request->xhr() && ("www.".$this->request->getHttpHost()==ATK14_HTTP_HOST || $this->request->getHttpHost()=="www.".ATK14_HTTP_HOST)) ||
			(defined("REDIRECT_TO_CORRECT_HOSTNAME_AUTOMATICALLY") && constant("REDIRECT_TO_CORRECT_HOSTNAME_AUTOMATICALLY") && $this->request->getHttpHost()!=ATK14_HTTP_HOST)
		){
			// redirecting from http://example.com/xyz to http://www.example.com/xyz
			$scheme = (defined("REDIRECT_TO_SSL_AUTOMATICALLY") && constant("REDIRECT_TO_SSL_AUTOMATICALLY")) ? "https" : $this->request->getScheme();
			return $this->_redirect_to("$scheme://".ATK14_HTTP_HOST.$this->request->getUri(),array("moved_permanently" => true));
		}

		if(!$this->request->ssl() && defined("REDIRECT_TO_SSL_AUTOMATICALLY") && constant("REDIRECT_TO_SSL_AUTOMATICALLY")){
			return $this->_redirect_to_ssl();
		}

		// logged in user
		$this->logged_user = $this->tpl_data["logged_user"] = $this->_get_logged_user();
		$ATK14_GLOBAL->setValue("logged_user",$this->logged_user); // we need this in app/helpers/function.admin_menu.php

		$this->breadcrumbs = new Menu14();
		$this->breadcrumbs[] = array(_("Home"),$this->_link_to(array("namespace" => "", "action" => "main/index")));
		$this->head_tags = new HeadTags();
		$this->_setup_head_tags();

		if($this->_logged_user_required() && !$this->logged_user){
			return $this->_execute_action("error403");
		}
	}

	function _application_after_filter(){
		// in here everything is done
		// rendered template is in $this->response->buffer

		if(DEVELOPMENT && class_exists("Tracy\Debugger")){
			$bar = Tracy\Debugger::getBar();
			$bar->addPanel(new DbMolePanel($this->dbmole));
			$bar->addPanel(new TemplatesPanel());
			$bar->addPanel(new MailPanel($this->mailer));
		}
	}

	function _login_user($user,$options = array()){
		$options += array(
			"fake_login" => false,
		);

		$key = $options["fake_login"] ? "fake_logged_user_id" : "logged_user_id";
		$this->session->s($key,$user->getId());
		$this->session->changeSecretToken(); // prevent from session fixation

		if($options["fake_login"]){
			$this->logger->info(sprintf("User#%s (%s) just logged in administratively as User#%s (%s) from %s",$this->logged_user->getId(),"$this->logged_user",$user->getId(),"$user",$this->request->getRemoteAddr()));
		}else{
			$this->logger->info(sprintf("User#%s (%s) just logged in from %s",$user->getId(),"$user",$this->request->getRemoteAddr()));
			$user->s(array(
				"last_signed_in_at" => now(),
				"last_signed_in_from_addr" => $this->request->getRemoteAddr(),
				"last_signed_in_from_hostname" => $this->request->getRemoteHostname(),
				//
				"updated_at" => $user->g("updated_at"),
				"updated_by_user_id" => $user->g("updated_by_user_id"),
			));
		}
	}

	function _logout_user(&$stayed_logged_as_user = null){
		$stayed_logged_as_user = null;
		$logged_user = $this->_get_logged_user($really_logged_user);

		if(!$logged_user){
			// just for sure
			$this->session->clear("logged_user_id");
			$this->session->clear("fake_logged_user_id");
		}elseif($logged_user->getId()!=$really_logged_user->getId()){
			$stayed_logged_as_user = $really_logged_user;
			$this->session->clear("fake_logged_user_id");
			$this->logger->info(sprintf("User#%s (%s) logged out administratively as User#%s (%s) from %s",$really_logged_user->getId(),"$really_logged_user",$logged_user->getId(),"$logged_user",$this->request->getRemoteAddr()));
		}else{
			$this->session->clear("logged_user_id");
			$this->session->clear("fake_logged_user_id"); // just for sure
			$this->logger->info(sprintf("User#%s (%s) logged out from %s",$logged_user->getId(),"$logged_user",$this->request->getRemoteAddr()));
		}
	}

	function _begin_database_transaction(){
		$this->dbmole->begin(array(
			"execute_after_connecting" => true
		));
	}

	function _end_database_transaction(){
		if(TEST){ return; } // perhaps you don't want to commit a transaction when you are testing
		$this->dbmole->isConnected() && $this->dbmole->commit();
	}

	function _rollback_database_transaction(){
		$this->dbmole->rollback();
		$this->_begin_database_transaction();
	}

	function _get_logged_user(&$really_logged_user = null){
		$really_logged_user = User::GetInstanceById($this->session->g("logged_user_id"));
		if($really_logged_user && !$really_logged_user->isActive()){ $really_logged_user = null; }

		if($really_logged_user && $really_logged_user->isAdmin()){
			$fakely_logged_user = User::GetInstanceById($this->session->g("fake_logged_user_id"));
		}

		return isset($fakely_logged_user) ? $fakely_logged_user : $really_logged_user;
	}

	function _logged_user_required(){
		return false;
	}

	/**
	 * Attempt to instantiate object by object_name and parameter
	 *
	 * <code>
	 *	 $this->_find("user");
	 *	 $this->_find("page","page_id");
	 *	 $this->_find("page",array(
	 *			"key" => "page_id",
	 *			"execute_error404_if_not_found" => false,
	 *	 ));
	 * </code>
	 *
	 * When an object is instantiated it can by found as
	 *	$this->logged_user
	 *	$this->tpl_data["logged_user"]
	 *
	 * A very common usage is:
	 * <code>
	 *	function _before_filter(){
	 *		if(in_array($this->action,array("detail","edit","destroy"))){
	 *			$this->_find("article");
	 *		}
	 *	}
	 * </code>
	 */
	function &_find($object_name,$options = array()){
		if(is_string($options)){
			$options = array("key" => $options);
		}

		$options += array(
			"key" => "id",
			"id" => null, // 123
			"execute_error404_if_not_found" => true,
			"class_name" => null, // e.g. "User"

			"set_object_as_controller_property" => true,
			"add_object_to_template" => true,
		);

		if(!$options["class_name"]){
			$options["class_name"] = String4::ToObject($object_name)->camelize()->toString(); // page -> Page
		}

		$key = $options["key"];

		$id = isset($options["id"]) ? $options["id"] : $this->params->getInt($key);

		$object = call_user_func(array($options["class_name"], "GetInstanceById"), $id);

		$options["set_object_as_controller_property"] && ($this->$object_name = $object);
		$options["add_object_to_template"] && ($this->tpl_data["$object_name"] = $object);

		if(!$object){
			$options["execute_error404_if_not_found"] && $this->_execute_action("error404");
		}

		return $object;
	}

	/**
	 * Just finds an object with no magic on the background
	 *
	 * ... or returns null when the object was not found.
	 *
	 *	$article = $this->_just_find("article");
	 *	$article = $this->_just_find("article","article_id");
	 *	$article = $this->_just_find("article",123);
	 *	$article = $this->_just_find("article",array("id" => 123));
	 */
	function &_just_find($object_name,$options = array()){
		if(is_numeric($options)){
			$options = array("id" => $options);
		}elseif(is_string($options)){
			$options = array("key" => $options);
		}
		$options["execute_error404_if_not_found"] = false;
		$options["set_object_as_controller_property"] = false;
		$options["add_object_to_template"] = false;
		return $this->_find($object_name,$options);
	}

	/**
	 * Adds return_uri to the given form to it's hidden parameters.
	 *
	 * $this->_save_return_uri();
	 * $this->_save_return_uri($this->form);
	 *
	 *	controller SomeController extends ApplicationController{
	 *		function edit(){
	 *			// may be also in _before_filter()
	 *			$this->_save_return_uri();
	 *			if($this->params->defined("storno")){ return $this->_redirect_back(); }
	 *
	 *			if($this->request->post() && ($d = $this->form->validate($this->params))){
	 *				// ...
	 *			}
	 *		}
	 *	}
	 */
	function _save_return_uri(&$form = null){

		// An experiment: let's utilize the session for better "redirect back" ability
		if($this->request->get()){
			($return_uris = $this->session->g("return_uris")) || ($return_uris = array());
			$key = md5($this->request->getRequestUri());
			if(!isset($return_uris[$key])){
				if(sizeof($return_uris)>50){ array_shift($return_uris); } // for safety reasons there is a max limit
				$return_uris[$key] = $this->_get_return_uri();
				$this->session->s("return_uris",$return_uris);
			}
		}

		if(!isset($form)){ $form = $this->form; }
		$return_uri = $this->_get_return_uri();
		$form->set_hidden_field("_return_uri_",$return_uri);
	}

	/**
	 * Returns current return uri
	 *
	 * In fact this returns a previously saved uri (by calling $this->_save_return_uri()), value of parameter _return_uri_ (eventually return_uri) or the http referer
	 */
	function _get_return_uri($default = "index"){
		$key = md5($this->request->getRequestUri());
		($return_uris = $this->session->g("return_uris")) || ($return_uris = array());

		($return_uri = $this->params->getString("_return_uri_")) ||
		($return_uri = isset($return_uris[$key]) ? $return_uris[$key] : null) ||
		($return_uri = $this->params->getString("return_uri")) ||
		($return_uri = $this->request->getHttpReferer()) ||
		($return_uri = $this->_link_to($default));
		return $return_uri;
	}

	/**
	 * Redirects user back to return_uri, when it is know.
	 * Otherwise redirects to the $default.
	 *
	 * $this->_redirect_to_return_uri(); // same as "index" :)
	 * $this->_redirect_to_return_uri("index");
	 * $this->_redirect_to_return_uri("books/index");
	 * $this->_redirect_to_return_uri(array(...));
	 * $this->_redirect_to_return_uri($this->_link_to(array(...)));
	 * $this->_redirect_to_return_uri("http://www.atk14.net");
	 */
	function _redirect_back($default = "index"){
		$key = md5($this->request->getRequestUri());
		($return_uris = $this->session->g("return_uris")) || ($return_uris = array());

		if(isset($return_uris[$key])){
			$return_uri = $return_uris[$key];
			unset($return_uris[$key]);
			$this->session->s("return_uris",$return_uris);
		}else{
			$return_uri = $this->_get_return_uri($default);
		}

		return $this->_redirect_to($return_uri);
	}

	function _add_something_to_breadcrumbs($title,$link){
		$title = strip_tags($title);
		if(is_array($link)){ $link = $this->_link_to($link); }
		$this->breadcrumbs[] = array($title,$link);
	}

	/**
	 * adding various meta tags into head
	 *
	 */
	protected function _setup_head_tags() {
		if (defined("PUPIQ_API_KEY")) {
			# force loading class which defines constants
			new Pupiq;
			if (defined("PUPIQ_PROXY_HOSTNAME")) {
				$ppq_proxy = PUPIQ_PROXY_HOSTNAME;
			}
			if (defined("PUPIQ_IMG_HOSTNAME")) {
				$ppq_img_hostname = PUPIQ_IMG_HOSTNAME;
			}

			if (isset($ppq_proxy) && $ppq_proxy) {
				$ppq_hostname = $ppq_proxy;
			} elseif(isset($ppq_img_hostname) && $ppq_img_hostname) {
				$ppq_hostname = $ppq_img_hostname;
			}

			if (isset($ppq_hostname) && $ppq_hostname!==$this->request->getHttpHost()) {
				$this->head_tags->addLinkTag("preconnect", ["href" => "//$ppq_hostname"]);
			}
		}
		if (class_exists("SystemParameter")) {
			$analytics_tracking_id = SystemParameter::ContentOn("app.trackers.google.analytics.tracking_id");
			$gtm_container_id = SystemParameter::ContentOn("app.trackers.google.tag_manager.container_id");
		} else {
			if (defined("GOOGLE_ANALYTICS_TRACKING_ID")) {
				$analytics_tracking_id = GOOGLE_ANALYTICS_TRACKING_ID;
			}
			if (defined("GOOGLE_TAG_MANAGER_CONTAINER_ID")) {
				$gtm_container_id = GOOGLE_TAG_MANAGER_CONTAINER_ID;
			}
		}
		if (isset($analytics_tracking_id)) {
			$this->head_tags->addPreconnect("https://www.google-analytics.com");
		}
		if (isset($analytics_tracking_id) || isset($gtm_container_id)) {
			$this->head_tags->addPreconnect("https://www.googletagmanager.com");
		}
		$this->_setup_hreflang_for_head_tags();
		return;
		# @note next tags are set in templates for now
		# meta tags
		$this->head_tags->addHttpEquiv("content-language", $this->lang);
		$this->head_tags->setProperty("og:title", ATK14_APPLICATION_NAME);
		$this->head_tags->setProperty("og:type","website");
		$this->head_tags->addProperty("og:url", $this->request->getUrl());
		$this->head_tags->addProperty("og:image", SystemParameter::ContentOn("app.social.default_image"));
		$this->head_tags->setCharsetMeta(DEFAULT_CHARSET);

		# link tags
		# adding preconnect using alternative shortcut method
		$this->head_tags->addPreconnect("https://fonts.gstatic.com/");

		$this->head_tags->addLinkTag("preload", ["href" => "/public/dist/webfonts/fa-solid-900.woff2", "as" => "font", "type" => "font/woff2"]);
		# adding preload using shortcut method
		$this->head_tags->addPreload("/public/dist/webfonts/fa-regular-400.woff2", ["as" => "font", "type" => "font/woff2", "crossorigin"]);
	}

	/**
	 * Prepares a object for the current action
	 *
	 * It's used in generic methods
	 */
	function __prepare_object_for_action(&$object){
		if($object){ return true; }

		$object_name = String4::ToObject(get_class($this))->gsub('/Controller$/','')->singularize()->underscore()->toString(); // "PeopleController" -> "person"
		if(!$object = $this->_find($object_name)){
			$this->_execute_action("error404");
			return false;
		}

		return true;
	}

	/**
	 * Sets the proper template for the current action
	 *
	 * It's used in generic methods
	 */
	function __set_template_name_for_action(){
		$smarty = $this->_get_smarty();
		if(!(
			(!$this->request->xhr() && $smarty->templateExists("$this->namespace/$this->controller/$this->action.tpl")) ||
			($this->request->xhr() && $smarty->templateExists("$this->namespace/$this->controller/$this->action.xhr.tpl"))
		)){
			$this->template_name = "application/$this->action";
		}
	}

	/**
	 *
	 *	$this->tpl_data["canonical_url"] = $this->_build_canonical_url();
	 *	$this->tpl_data["canonical_url"] = $this->_build_canonical_url("index");
	 *	$this->tpl_data["canonical_url"] = $this->_build_canonical_url(["action" => "pages/detail", "id" => $this->page]);
	 */
	function _build_canonical_url($action_or_params = "",$params = []){
		if(!$action_or_params){
			$action_or_params = $this->action;
		}
		if(is_array($action_or_params)){
			$params = $action_or_params;
		}else{
			$params["action"] = $action_or_params;
		}
		return $this->_link_to($params,["with_hostname" => true]);
	}

	protected function _setup_hreflang_for_head_tags() {
		global $ATK14_GLOBAL;
		$params_homepage = array("namespace" => "", "controller" => "main", "action" => "index");
		$params = ($this->request->get() && !preg_match('/^error/',$this->action)) ? $this->params->toArray() : $params_homepage;

		$current_language = null;

		$langs = [];
		$locales = $ATK14_GLOBAL->getConfig("locale");
		# do not setup hreflang when only one locale
		if (!(sizeof($locales)>1)) {
			return;
		}
		foreach($locales as $lang => $locale) {
			$params["lang"] = $lang;
			$_url = $this->_link_to($params,["with_hostname" => true]);
			# first hreflang with just a language code
			$langs[] = [
				"lang" => $lang,
				"url" => $_url,
			];
			list($locale_lang,$encoding) = preg_split("/\./", $locale["LANG"].".");
			# second hreflang for language-country code
			$langs[] = [
				"lang" => strtr($locale_lang, "_", "-"),
				"url" => $_url,
			];
		}

		foreach($langs as $lang) {
			$this->head_tags->addLinkTag("alternate", ["hreflang" => $lang["lang"], "href" => $lang["url"]]);
			if ($ATK14_GLOBAL->getDefaultLang() == $lang["lang"]) {
				$current_language = $lang;
			}
		}
		$this->head_tags->addLinkTag("alternate", ["hreflang" => "x-default", "href" => $current_language["url"]]);
	}
}

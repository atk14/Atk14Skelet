<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/default.tpl
	 */
	function index(){
		$this->page_title = _("Welcome!");
	}

	function about(){
		$this->page_title = sprintf(_("About %s"),ATK14_APPLICATION_NAME);

		$this->breadcrumbs[] = $this->page_title;
	}

	function contact(){
		if($this->logged_user){
			$this->form->set_initial(array(
				"name" => $this->logged_user->getName(),
				"email" => $this->logged_user->getEmail(),
			));
		}

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->mailer->contact_message($d,$this->request->getRemoteAddr(),$this->logged_user);
			$this->session->s("contact_message_sent",1);
			$this->_redirect_to("contact_message_sent");
		}
	}

	function contact_message_sent(){
		$this->page_title = _("Contact");

		if(!$this->session->g("contact_message_sent")){
			return $this->_redirect_to("contact");
		}

		$this->session->clear("contact_message_sent");
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");
	}
}

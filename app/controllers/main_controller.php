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
		$this->page_title = $this->breadcrumbs[] = sprintf(_("About %s"),ATK14_APPLICATION_NAME);
	}

	function contact(){
		$this->page_title = $this->breadcrumbs[] = _("Contact");

		if($this->logged_user){
			$this->form->set_initial(array(
				"name" => $this->logged_user->getName(),
				"email" => $this->logged_user->getEmail(),
			));
		}

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if($d["sign_up_for_newsletter"] && $d["email"]){
				NewsletterSubscriber::SignUp($d["email"],array(
					"name" => $d["name"],
				));
			}

			$this->mailer->contact_message($d,$this->request->getRemoteAddr(),$this->logged_user);
			$this->session->s("contact_message_sent",1);
			$this->_redirect_to("contact_message_sent");
		}
	}

	function contact_message_sent(){
		$this->page_title = $this->breadcrumbs[] = _("Contact");

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

<?php
class ContactMessagesController extends ApplicationController {

	function create_new(){
		if($this->request->get() && !$this->rendering_component && ($page = Page::GetInstanceByCode("contact"))){
			return $this->_redirect_to_form();
		}

		$this->page_title = $this->breadcrumbs[] = _("Contact message");

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
			$this->_redirect_to("sent");
		}
	}

	function sent(){
		$this->page_title = _("Contact message");
		$this->breadcrumbs[] = _("Contact message sent");

		if(!$this->session->g("contact_message_sent")){
			return $this->_redirect_to_form();
		}

		$this->session->clear("contact_message_sent");
	}

	function _before_filter(){
		if($page = Page::GetInstanceByCode("contact")){
			$this->_add_page_to_breadcrumbs($page);
		}
	}

	function _redirect_to_form(){
		if($page = Page::GetInstanceByCode("contact")){
			return $this->_redirect_to([
				"controller" => "pages",
				"action" => "detail",
				"id" => $page,
			]);
		}
		return $this->_redirect_to("create_new");
	}
}

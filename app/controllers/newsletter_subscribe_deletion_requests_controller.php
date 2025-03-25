<?php
class NewsletterSubscribeDeletionRequestsController extends ApplicationController {

	function create_new(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$ns = NewsletterSubscriber::GetMostRecentInstanceByEmail($d["email"]);
			if(!$ns){
				$this->form->set_error("email",_("Na takovou adresu newsletter nezasíláme"));
				return;
			}
			$this->mailer->newsletter_unsubsubscribe_confirmation($ns);
			$this->_redirect_to("created");
		}
	}

	function created(){
		
	}

	function _before_filter(){
		$this->page_title = $this->breadcrumbs[] = _("Odhlášení od newsletteru");
	}
}

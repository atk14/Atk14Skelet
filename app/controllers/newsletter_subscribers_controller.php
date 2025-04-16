<?php
class NewsletterSubscribersController extends ApplicationController {

	function destroy(){
		$ns = NewsletterSubscriber::GetInstanceByToken($this->params->getString("token"));
		if(!$ns){
			$this->_execute_action("error404");
			return;
		}

		$this->tpl_data["newsletter_subscriber"] = $ns;

		if($this->request->post() && !is_null($d = $this->form->validate($this->params))){
			foreach(NewsletterSubscriber::GetInstancesByEmail($ns->getEmail()) as $_ns){
				$_ns->destroy();
			}
			$this->_redirect_to("destroyed");
		}
	}

	function destroyed(){
		$this->breadcrumbs[] = _("Odhlášeno");
	}

	function _before_filter(){
		$this->breadcrumbs[] = [_("Odhlášení od newsletteru"),$this->_link_to("newsletter_subscribe_deletion_requests/create_new")];
		$this->page_title = _("Odhlášení od newsletteru");
	}
}

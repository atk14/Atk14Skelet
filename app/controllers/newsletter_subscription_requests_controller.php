<?php
class NewsletterSubscriptionRequestsController extends ApplicationController {

	function create_new(){
		$this->page_title = _("Přihlášení k odběru novinek");

		if($this->request->post() && ($d = $this->form->validate($this->params))){

			$this->_create_newsletter_subscription_request($d["email"],[],[
				"create_request_if_subscription_exists" => true,
			]);

			$this->_redirect_to("created");
		}
	}

	function created(){
		$this->page_title = _("Přihlášení k odběru novinek");
		$this->breadcrumbs[] = _("Potvrzení odesláno");
	}

	function confirm(){
		$newsletter_subscription_request = $this->newsletter_subscription_request;

		$this->page_title = _("Přihlášení k odběru novinek");

		if($newsletter_subscription_request->isConfirmed()){
			$this->template_name = "already_confirmed";
			return;
		}

		if($newsletter_subscription_request->isCancelled()){
			$this->response->setStatusCode(404);
			$this->template_name = "already_cancelled";
			return;
		}

		if($this->request->post()){
			$newsletter_subscription_request->s([
				"confirmed" => true,
				"confirmed_at" => now(),
			]);

			$this->_sign_up_for_newsletter($newsletter_subscription_request->getEmail(),[
				"vocative" => $newsletter_subscription_request->getVocative(),
				"name" => $newsletter_subscription_request->getName(),
			]);

			$this->flash->success(_("Přihlášení k odběru novinek bylo úspěšně potvrzeno."));

			$this->_redirect_to("main/index");
		}
	}

	function cancel(){
		if(!$this->request->post() || $this->newsletter_subscription_request->isCancelled() || $this->newsletter_subscription_request->isConfirmed()){
			return $this->_redirect_to(["action" => "confirm", "token" => $this->newsletter_subscription_request->getToken()]);
		}

		$this->newsletter_subscription_request->s([
			"cancelled" => true,
			"cancelled_at" => now(),
		]);

		$this->flash->notice(_("Přihlášení k odběru novinek bylo zrušeno."));
		$this->_redirect_to("main/index");
	}

	function _before_filter(){
		if(defined("SIGN_UP_FOR_NEWSLETTER_ENABLED") && !constant("SIGN_UP_FOR_NEWSLETTER_ENABLED")){
			return $this->_execute_action("error404");
		}

		if(in_array($this->action,["confirm","cancel"])){
			$newsletter_subscription_request = NewsletterSubscriptionRequest::GetInstanceByToken($this->params->getString("token"));
			if(!$newsletter_subscription_request){
				return $this->_execute_action("error404");
			}
			$this->newsletter_subscription_request = $this->tpl_data["newsletter_subscription_request"] = $newsletter_subscription_request;
		}

		$this->breadcrumbs[] = [_("Přihlášení k odběru novinek"),$this->_link_to("create_new")];
	}
}

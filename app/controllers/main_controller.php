<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/_default.tpl
	 */
	function index(){
		$this->page_title = _("Welcome!");
	}

	function about(){
		$this->page_title = sprintf(_("About %s"),ATK14_APPLICATION_NAME);
	}

	function contact(){
		$this->page_title = _("Contact");

		if($this->logged_user){
			$this->form->set_initial(array(
				"name" => $this->logged_user->getName(),
				"email" => $this->logged_user->getEmail(),
			));
		}

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->mailer->contact_message($d["email"],$d["name"],$d["body"]);
			$this->flash->success(_("The message has been sent to us. We will reply as soon as we can."));
			$this->_redirect_to("contact");
		}
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");
	}
}

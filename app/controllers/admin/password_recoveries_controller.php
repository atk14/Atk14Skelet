<?php
class PasswordRecoveriesController extends AdminController{
	function index(){
		$this->page_title = _("Password recoveries");
		$this->sorting->add("created_at",array("reverse" => true));

		$this->tpl_data["finder"] = PasswordRecovery::Finder(array(
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}
}

<?php
class PasswordRecoveriesController extends AdminController{

	function index(){
		$this->page_title = _("Password recoveries");

		($d = $this->form->validate($this->params)) || ($d = $this->form->get_initial());
		$conditions = $bind_ar = array();

		if($d["search"]){
			if($ft_cond = FullTextSearchQueryLike::GetQuery("UPPER(id||' '||(SELECT login FROM users WHERE id=password_recoveries.user_id)||' '||COALESCE(email,'')||' '||COALESCE(created_from_addr,'')||' '||COALESCE(recovered_from_addr,''))",Translate::Upper($d["search"]),$bind_ar)){
				$conditions[] = $ft_cond;
			}
		}

		$this->sorting->add("created_at",array("reverse" => true));
		$this->sorting->add("id");

		$this->tpl_data["finder"] = PasswordRecovery::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}
}

<?php
class StaticPagesController extends AdminController {

	function index() {
		$this->page_title = _("Statické stránky");

		$this->sorting->add("created_at");
#		$this->sorting->add("updated_at");
#		$this->sorting->add("title", array("order_by" => Translation::BuildOrderSqlForTranslatableField("static_pages", "title")));

		$conditions = $bind_ar = array();
		$conditions[] = "parent_static_page_id is null";
		$this->tpl_data["finder"] = StaticPage::Finder(array(
			"conditions" => $conditions,
			"bind_ar" => $bind_ar,
			"order_by" => $this->sorting,
			"offset" => $this->params->getInt("offset"),
		));
	}

	function create_new() {
		$this->page_title = _("Vytvoření statické stránky");
		$this->_save_return_uri();
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {
			$static_page = StaticPage::CreateNewRecord($d);
			$this->flash->success(_("Stránka uložena"));
			$this->_redirect_to_action("edit", array("id" => $static_page));
		}
	}

	function edit() {
		$this->page_title = sprintf(_("Editace stránky '%s'"), $this->static_page->getTitle());
		$this->_save_return_uri();
		$this->form->set_initial($this->static_page);
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {
			$this->static_page->s($d);
			$this->flash->success(_("Stránka uložena"));
			$this->_redirect_back();
		}
	}

	function destroy() {
		if (!$this->request->post()) {
			return $this->_execute_action("error404");
		}
		$this->static_page->destroy();
		if (!$this->request->xhr()) {
			$this->flash->success(_("Stránka smazána"));
		}
	}

	function _setup_breadcrumbs_filter() {
		if ($this->action=="index") {
			$this->breadcrumbs->addTextItem(_("Statické stránky"));
		} else {
			$this->breadcrumbs->addItem(_("Statické stránky"), $this->_link_to("static_pages/index"));
		}
		($this->action=="edit") && $this->breadcrumbs->addTextItem($this->static_page->getTitle());
		($this->action=="create_new") && $this->breadcrumbs->addTextItem(_("Nový článek"));
	}

	function _before_filter() {
		if(in_array($this->action,array("edit","destroy"))){
			$this->_find("static_page");
		}
	}
}

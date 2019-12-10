<?php
class PagesForm extends AdminForm {

	function set_up() {
		$this->add_translatable_field("title", new CharField(array(
			"label" => _("Title"),
		)));

		$this->add_field("image_url",new PupiqImageField([
			"label" => _("Image"),
			"required" => false,
			"help_text" => _("Image used on list of pages, social networks, etc.")
		]));

		$this->add_translatable_field("teaser", new MarkdownField(array(
			"label" => _("Teaser"),
			"required" => false,
		)));

		$this->add_translatable_field("body", new MarkdownField(array(
			"label" => _("Body"),
			"required" => false,
		)));

		$this->add_translatable_field("page_title",new CharField(array(
			"label" => _("HTML title"),
			"required" => false,
			"max_length" => 255,
			"help_text" => h(_("Content for <html><head><title>. If left empty, the title is used.")),
		)));

		$this->add_translatable_field("page_description", new CharField(array(
			"label" => _("HTML description"),
			"required" => false,
			"max_length" => 255,
			"help_text" => h(_('Content for <meta name="description">. If left empty, the teaser is used.')),
		)));

		$this->add_field("parent_page_id", new PageField(array(
			"label" => _("Parent page"),
			"required" => false,
			"page_id" => isset($this->controller->page) ? $this->controller->page : null,
		)));

		$this->add_field("indexable", new BooleanField([
			"label" => _("Show in sitemap?"),
			"required" => false,
			"initial" => true,
		]));

		$this->add_field("visible", new BooleanField([
			"label" => _("Visible?"),
			"required" => false,
			"initial" => true,
		]));

		$this->add_code_field();
	}

	function clean() {
		$d = $this->cleaned_data;

		if (isset($d["parent_page_id"]) && isset($this->controller->page)) {
			$page = $d["parent_page_id"];
			$counter = 0;
			while($page){
				if($page->getId()==$this->controller->page->getId()){
					$this->set_error("parent_page_id", _("The page cannot be placed under itself."));
					break;
				}
				$page = $page->getParentPage();
				$counter++;
				if($counter>30){
					$this->set_error("parent_page_id", _("Nesting is too deep."));
					break;
				}
			}
		}

		if(isset($d["parent_page_id"])){
			$d["parent_page_id"] = $d["parent_page_id"]->getId();
		}

		return array(null, $d);
	}
}

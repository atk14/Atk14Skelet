<?php
require_once(__DIR__."/static_page_form.php");
class EditForm extends StaticPageForm {

	function set_up() {
		parent::set_up();
		$this->add_translatable_field("slug",new SlugField());
	}
}

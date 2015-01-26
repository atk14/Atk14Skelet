<?php
class EditForm extends StaticPagesForm {

	function set_up() {
		parent::set_up();
		$this->add_translatable_field("slug",new SlugField());
	}
}

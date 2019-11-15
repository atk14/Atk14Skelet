<?php
require_once(__DIR__."/gallery_items_form.php");

class CreateNewForm extends GalleryItemsForm{
	function set_up(){
		parent::set_up();
		$this->set_button_text(_("Nahrát fotografii"));
	}
}

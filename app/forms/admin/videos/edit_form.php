<?php
class EditForm extends VideosForm {
	function set_up() {
		parent::set_up();
		$this->add_image_url_field();
	}
}

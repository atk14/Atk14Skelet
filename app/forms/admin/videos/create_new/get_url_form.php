<?php
class GetUrlForm extends VideosForm {

	function set_up() {
		$this->add_url_field();
		$this->set_button_text(_("Pokračovat"));
	}
}

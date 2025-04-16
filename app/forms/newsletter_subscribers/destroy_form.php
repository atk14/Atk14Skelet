<?php
class DestroyForm extends ApplicationForm {

	function set_up(){
		$f = $this->add_field("confirmation", new ConfirmationField([
			"label" => _("Odhlásit se z odběru newsletteru"),
		]));
		$f->update_messages([
			"required" => _("Potvrďte svůj záměr odhlásit se z odběru newsletteru zatrhnutím políčka")
		]);

		$this->set_button_text(_("Odhlásit se"));
	}
}

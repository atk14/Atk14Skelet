<?php
class ErrorRedirectionsForm extends AdminForm {

	function set_up(){
		$this->add_field("source_url", new CharField(array(
			"label" => _("Source URL"),
			"help_text" => _("Address which cannot be found on this server. Here are some examples. The first one is usually the most appropriate.").
				"<ul>".
					"<li>/link-to-page/</li>".
					"<li>//www.example.com/link-to-page/</li>".
					"<li>http://www.example.com/link-to-page/</li>".
				"</ul>",
		)));

		$this->add_field("target_url", new CharField(array(
			"label" => _("Target URL"),
			"help_text" => _("Examples:").
				"<ul>".
					"<li>/about-us/</li>".
					"<li>//www.example.com/about-us/</li>".
					"<li>http://www.example.com/about-us/</li>".
				"</ul>",
		)));

		$this->add_field("moved_permanently", new BooleanField(array(
			"label" => _("Moved permanently?"),
			"required" => false,
			"initial" => true,
		)));
	}
}

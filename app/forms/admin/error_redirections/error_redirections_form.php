<?php
class ErrorRedirectionsForm extends AdminForm {

	function set_up(){
		$patterns = array(
			"https?:\/\/[^\/].*", // "https://example.com/file"
			"\/[^\/].*", 					// "/file"
			"\/",									// "/"
			"\/\/[^\/].*"					// "//example.com/file"
		);
		$regex = '/^('.join("|",$patterns).')$/';
		$msg = _("This doesn't look like an URL or URI");

		$f = $this->add_field("source_url", new RegexField($regex,array(
			"label" => _("Source URL"),
			"help_text" => _("Address which cannot be found on this server. Here are some examples. The first one is usually the most appropriate.").
				"<ul>".
					"<li>/link-to-page/</li>".
					"<li>//www.example.com/link-to-page/</li>".
					"<li>http://www.example.com/link-to-page/</li>".
				"</ul>",
		)));
		$f->update_messages(array("invalid" => $msg));

		$f = $this->add_field("target_url", new RegexField($regex,array(
			"label" => _("Target URL"),
			"help_text" => _("Examples:").
				"<ul>".
					"<li>/about-us/</li>".
					"<li>//www.example.com/about-us/</li>".
					"<li>http://www.example.com/about-us/</li>".
				"</ul>",
		)));
		$f->update_messages(array("invalid" => $msg));

		$this->add_field("moved_permanently", new BooleanField(array(
			"label" => _("Moved permanently?"),
			"required" => false,
			"initial" => true,
		)));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(isset($d["source_url"]) && isset($d["target_url"]) && $d["source_url"]==$d["target_url"]){
			$this->set_error(_("The Source URL and Target URL cannot be the same"));
		}

		return array($err,$d);
	}
}

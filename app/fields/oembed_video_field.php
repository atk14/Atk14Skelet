<?php
class_exists("Video");

class OembedVideoField extends UrlField {
	function __construct($options=array()) {
		$options += array(
			"help_text" => _('Zadejte URL videa. Lze vložit video ze služeb, které poskytují API podle specifikace <a href="http://oembed.com/">oEmbed</a>.'),
		);
		parent::__construct($options);
	}

	function clean($value) {
		list($err,$value) = parent::clean($value);

		if($err || !$value){ return array($err,$value); }

		$essence = new \Essence\Essence();
		if (!$media = $essence->extract($value, array("maxwidth" => VIDEO_DEFAULT_WIDTH, "maxheight" => VIDEO_DEFAULT_HEIGHT))) {
			return array(_("Nelze získat informace o videu. Ověřte URL videa a podporu oEmbed u poskytovatele videa."), null);
		}
		if ($media->type!="video") {
			if($media->error && strlen($media->error["message"])){
				trigger_error(sprintf("Essence\Essence: an error occurred: %s (code: %s, video url: %s)",$media->error["message"],$media->error["code"],$value));
			}
			return array(_("Typ souboru na tomto URL není video"), null);
		}

		return array(null,$media);
	}
}

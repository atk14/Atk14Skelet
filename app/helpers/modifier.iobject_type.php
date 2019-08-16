<?php
/**
* Zobrazi typ iobjektu v citelne podobe
*
* Usage in templates:
*
* ```
* {$iobject|iobject_type}
* ```
*
*/
function smarty_modifier_iobject_type($iobject){
	switch ($table = $iobject->getReferredTable()) {
		case "videos":
			$type = _("Video");
			$icon = "glyphicon glyphicon-film";
			break;
		case "galleries":
			$type = _("Galerie");
			$icon = "glyphicon glyphicon-picture";
			break;
		case "pictures":
			$type = _("Obrázek");
			$icon = "glyphicon glyphicon-picture";
			break;
		default:
			$type = sprintf("%s #%d", $table, $iobject->getId());
			$icon = "glyphicon glyphicon-file";
			break;
	}
	return sprintf('<span class="%s" title="%s"></span>', $icon, $type);
}

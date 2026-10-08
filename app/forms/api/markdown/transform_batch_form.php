<?php
class TransformBatchForm extends ApiForm{

	const MAX_SOURCES = 500;
	const MAX_SOURCE_LENGTH = 50000;

	function set_up(){
		$this->add_field("sources", new TextField(array(
			"max_length" => 1000000,
			"help_text" => _('JSON encoded array of Markdown sources, e.g. ["# Hello", "Some *text*"]'),
		)));

		$this->add_field("base_href", new CharField([
			"max_length" => 255,
			"required" => false,
		]));

		$this->add_field("editor_preview", new BooleanField([
			"required" => false,
			"help_text" => _("Render for a preview in an editor (without admin menus)"),
		]));
	}

	function clean(){
		list($err,$d) = parent::clean();

		if(isset($d["sources"])){
			$sources = json_decode($d["sources"],true);
			if(!is_array($sources) || array_values($sources)!==$sources){
				$this->set_error("sources",_("Expecting a JSON encoded array"));
			}elseif(sizeof($sources)>self::MAX_SOURCES){
				$this->set_error("sources",sprintf(_("Too many sources, max. %d is allowed"),self::MAX_SOURCES));
			}else{
				foreach($sources as $source){
					if(!is_string($source)){
						$this->set_error("sources",_("Every source must be a string"));
						break;
					}
					if(mb_strlen($source,"UTF-8")>self::MAX_SOURCE_LENGTH){
						$this->set_error("sources",sprintf(_("A source is too long, max. %d characters is allowed"),self::MAX_SOURCE_LENGTH));
						break;
					}
				}
			}
			$d["sources"] = $sources;
		}

		return array($err,$d);
	}
}

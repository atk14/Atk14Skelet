<?php
class TagsField extends CharField{
	function __construct($options = array()){
		$options += array(
			"separator" => ",",
			"unique" => true,
			"create_tag_if_not_found" => false,
			"max_tags" => null, // 10
		);
		$this->separator = $options["separator"];
		$this->unique = $options["unique"];
		$this->max_tags = $options["max_tags"];
		$this->create_tag_if_not_found = $options["create_tag_if_not_found"];

		parent::__construct($options);

		$this->update_messages(array(
			"max_tags" => _('Ensure that there are max %max% tags (now there are %count%)'),
			"no_such_tag" => _("There is no tag <em>%s</em>"),
			"unique" => _("Tag <em>%s</em> is there more than once"),
		));
	}

	function format_initial_data($tags){
		if(is_array($tags)){
			$out = array();
			foreach($tags as $t){
				$out[] = (string)$t;
			}
			$tags = join(" $this->separator ",$out);
		}

		return $tags;
	}

	function clean($value){
		$sep = $this->separator;

		$value = preg_replace("/^(\\s*$sep\\s*)+/s",'',$value); // ",music,,rock," -> "music,,rock,"
		$value = preg_replace("/(\\s*$sep\\s*)+$/s",'',$value); // "models,,rock," -> "models,,rock"
		$value = preg_replace("/(\\s*$sep\\s*)+/s",$sep,$value); // "models,,rock" -> "models,rock"

		list($err,$value) = parent::clean($value);
		if($err || !$value){ return array($err,array()); }

		$tags = preg_split("/$sep/s",$value);
		$out = array();
		while($tag = array_shift($tags)){
			if(!$t = Tag::FindByTag($tag)){
				if(!$this->create_tag_if_not_found){
					return array(sprintf($this->messages["no_such_tag"],h($tag)),null);
				}
				$t = Tag::CreateNewRecord(array("tag" => $tag));
			}
			if($this->unique && in_array($tag,$tags)){
				return array(sprintf($this->messages["unique"],h($tag)),null);
			}
			$out[] = $t;
		}

		if($this->max_tags && sizeof($out)>$this->max_tags){
			return array(
				strtr($this->messages["max_tags"],array("%max%" => $this->max_tags, "%count%" => sizeof($out))),
				null
			);
		}

		return array(null,$out);
	}
}

<?php
class ArticlesForm extends AdminForm {
	function set_up(){
		$this->add_field("title",new CharField(array(
			"label" => _("Title"),
			"max_length" => 255,
		)));

		$this->add_field("body",new MarkdownField(array(
			"label" => _("Body"),
			"help_text" => _("Markdown format is expected"),
		)));

		$this->add_field("published_at",new DateTimeField(array(
			"label" => _("Published At"),
			"initial" => time(),
		)));

		$this->add_field("tags", new TagsField(array(
			"label" => _("Tags"),
			"required" => false,
			"hint" => "news , webdesign",
			"create_tag_if_not_found" => true,
		)));
	}
}

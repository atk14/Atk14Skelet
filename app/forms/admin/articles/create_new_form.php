<?php
class CreateNewForm extends AdminForm{
	function set_up(){
		$this->add_field("title",new CharField(array(
			"label" => _("Title"),
			"max_length" => 255,
		)));

		$this->add_field("body",new TextField(array(
			"label" => _("Body"),
			"help_text" => _("Mardown format is expected"),
		)));

		$this->add_field("published_at",new DateTimeField(array(
			"label" => _("Published At"),
			"initial" => time(),
		)));

		$this->add_field("tags", new TagsField(array(
			"label" => _("Tags"),
			"required" => false,
			"hint" => "news , webdesign",
			"help_text" => sprintf(_("Mention tag <em>%s</em> when this article should be displayed in the news section"),h(Tag::FindById(Tag::ID_NEWS)))
		)));
	}
}

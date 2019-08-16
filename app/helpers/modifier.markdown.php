<?php
/**
 * Markdown modifier
 *
 *	{$source_text|markdown}
 *
 * In an ATK14 application, you may use nofilter or the exclamation mark
 *
 *	{$source_text|markdown nofilter}
 *	{!$source_text|markdown}
 */
function smarty_modifier_markdown($text){
	$markdown = new DrinkMarkdown(array(
		"table_class" => "table",
		"html_purification_enabled" => false,
		"iobjects_processing_enabled" => true,
		"urlize_text" => true,
	));
	return $markdown->transform($text);
}

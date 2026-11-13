<?php
class MarkdownField extends TextField {

	function __construct($options = array()){
		$options += array(
			"base_href" => "", // e.g. "/admin/en/wiki_pages/"
		);

		$options += array(
			"widget" => new TextArea(array(
				"attrs" => array(
					"data-provide" => "markdown",
					"data-base_href" => $options["base_href"],
					"data-block_shortcodes" => json_encode(self::GetBlockShortcodes()), // needed by the block editor, see public/admin/scripts/utils/md_block_editor.js
					"data-function_shortcodes" => json_encode(self::GetFunctionShortcodes()), // e.g. [spacer]; a paragraph consisting only of one is a shortcode block in the block editor
				),
			)),
		);

		unset($options["base_href"]);

		parent::__construct($options);
	}

	/**
	 * Returns names of block shortcodes known to DrInk Markdown
	 *
	 *	["row", "col", "div", "tabs", "tab", ...]
	 */
	static function GetBlockShortcodes(){
		static $block_shortcodes;
		if(!isset($block_shortcodes)){
			$dm = new DrinkMarkdown();
			$block_shortcodes = $dm->getBlockShortcodes();
		}
		return $block_shortcodes;
	}

	/**
	 * Returns names of function shortcodes (shortcodes without a closing tag) known to DrInk Markdown
	 *
	 *	["spacer"]
	 */
	static function GetFunctionShortcodes(){
		static $function_shortcodes;
		if(!isset($function_shortcodes)){
			$dm = new DrinkMarkdown();
			$function_shortcodes = $dm->getFunctionShortcodes();
		}
		return $function_shortcodes;
	}
}

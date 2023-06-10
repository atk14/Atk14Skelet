<?php
/**
 * {!$source_text|markdown}
 */
function smarty_modifier_markdown($text){
	return Michelf\MarkdownExtra::defaultTransform($text);
}

<?php
function Markdown($raw){
	Atk14Require::Helper("modifier.markdown");
	return smarty_modifier_markdown($raw);
}

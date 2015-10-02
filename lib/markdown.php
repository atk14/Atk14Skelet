<?php
function Markdown($raw){
	$out = Michelf\Markdown::defaultTransform($raw);
	return $out;
}

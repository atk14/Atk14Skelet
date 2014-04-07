<?php
class Tag extends ApplicationModel{
	const ID_NEWS = 1; // see db/migrations/0004_tags.sql

	function toString(){ return $this->getTag(); }
}

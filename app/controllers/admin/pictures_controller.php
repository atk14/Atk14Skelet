<?php
require_once(__DIR__ . "/iobjects_base.php");
class PicturesController extends IobjectsBaseController{

	function detail(){
		parent::detail();
		$this->tpl_data["image"] = new Pupiq($this->picture->getImageUrl());
	}
}

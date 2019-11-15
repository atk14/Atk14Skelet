<?php
require_once(__DIR__ . "/iobjects_base.php");
class PicturesController extends IobjectsBaseController{

	function detail(){
		parent::detail();

		if($this->params->getString("format")=="raw"){
			$pupiq = new Pupiq($this->picture->getUrl());
			
			$content = $pupiq->downloadOriginal($headers);

			$this->render_template = false;
			$this->response->write($content);
			$this->response->setHeaders($headers);
		}
	}
}

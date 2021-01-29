<?php
class TestsController extends ApplicationController {

	function async_file_upload(){
		$this->page_title = _("Testing async file upload");

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$this->tpl_data["cleaned_data"] = $d;
		}
	}
}

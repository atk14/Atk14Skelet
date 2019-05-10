<?php
class MarkdownController extends ApiController {

	/**
	 * ### Transforms a Markdown document into HTML
	 */
	function transform(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			Atk14Require::Helper("modifier.markdown");
			$this->_report_success(array(),array(
				"content_type" => "text/plain",
				"raw_data" => smarty_modifier_markdown($d["source"]),
			));
		}
	}
}

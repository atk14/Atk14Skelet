<?php
class TagsSuggestionsController extends ApiController{

	/**
	 * ### Tags suggestion
	 * 
	 * Suggests tags according to a search term
	 */
	function index(){
		if(!$this->params->isEmpty() && ($d = $this->form->validate($this->params))){
			$this->api_data = array();
			
			$tags = Tag::FindAll(array(
				"conditions" => "LOWER(tag) LIKE LOWER('%'||:q||'%')",
				"bind_ar" => array(":q" => "$d[q]"),
				"order_by" => "LOWER(tag) LIKE LOWER(:q||'%') DESC, LOWER(tag), tag",
				"limit" => 20,
			));
			foreach($tags as $t){
				$this->api_data[] = (string)$t;
			}
		}
	}
}

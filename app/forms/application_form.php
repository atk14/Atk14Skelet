<?php
class ApplicationForm extends Atk14Form{

	function set_up(){

	}

	function pre_set_up(){
		// submit method GET is automatically set in IndexForm or DetailForm
		if(preg_match('/^(Index|Detail)Form$/i',get_class($this))){
			$this->set_method("get");
		}
	}

	function post_set_up(){
		
	}

	/**
	 * Text on submit button 
	 */
	protected $button_text = null;

	/**
	 * Has this form only a few fields and so on a page it appears to be small?
	 * 
	 * This method is used in partial template app/views/shared/form.tpl
	 */
	function is_small(){
		return sizeof($this->fields)<=4;
	}

	/**
	 * Sets text on the form`s submit button
	 * 
	 * See partial template app/views/shared/_form_button.tpl
	 */
	function set_button_text($text){
		$this->button_text = $text;
	}

	/**
	 * Returns text on the form`s submit button
	 * 
	 * See partial template app/views/shared/_form_button.tpl
	 */
	function get_button_text(){
		return isset($this->button_text) ? $this->button_text : _("Save");
	}
}

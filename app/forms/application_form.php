<?php
class ApplicationForm extends Atk14Form{
	/**
	 * Text on submit button 
	 */
	protected $button_text = null;

	/**
	 * 
	 */
	function pre_set_up(){
		// submit method GET is automatically set in IndexForm or DetailForm
		if(preg_match('/^(Index|Detail)Form$/i',get_class($this))){
			$this->set_method("get");
		}
	}

	/**
	 * The main setup method
	 */
	function set_up(){

	}

	/**
	 * 
	 */
	function post_set_up(){
		
	}

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
		if($this->button_text){ return $this->button_text; }
		switch(get_class($this)){
			case "CreateNewForm":
				return _("Create");
			case "DetailForm":
				return _("Show");
			case "EditForm":
				return _("Update");
			case "DestroyForm":
				return _("Delete");
			default:
				return _("Save");
		}
	}
}

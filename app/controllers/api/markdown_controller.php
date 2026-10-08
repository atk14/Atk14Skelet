<?php
class MarkdownController extends ApiController {

	/**
	 * ### Transforms a Markdown document into HTML
	 */
	function transform(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$content = $this->_transform($d["source"],$d);
			$this->_report_success(array(),array(
				"content_type" => "text/plain",
				"raw_data" => $content,
			));
		}
	}

	/**
	 * ### Transforms several Markdown documents into HTML at once
	 *
	 * Returns an array of HTML documents in the same order as the given sources.
	 * It is used by the block editor in the administration to render all blocks in one request.
	 */
	function transform_batch(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$contents = array();
			foreach($d["sources"] as $source){
				$contents[] = $this->_transform($source,$d);
			}
			$this->_report_success($contents);
		}
	}

	function _transform($source,$options){
		global $ATK14_GLOBAL;

		// In previews for an editor, admin menus of the rendered objects (iobjects, link lists...) are just in the way
		$admin_menu_disabled = $ATK14_GLOBAL->getValue("admin_menu_disabled");
		$ATK14_GLOBAL->setValue("admin_menu_disabled",$admin_menu_disabled || $options["editor_preview"]);

		Atk14Require::Helper("modifier.markdown");
		$content = smarty_modifier_markdown($source);

		$ATK14_GLOBAL->setValue("admin_menu_disabled",$admin_menu_disabled);

		if($options["base_href"]){
			$base_href = $options["base_href"];
			if(!preg_match('/\/$/',$base_href)){
				$base_href .= "/";
			}
			$content = preg_replace_callback('/(<a\b[^>]*\bhref="|img\b[^>]*\bsrc=")([^"]*)/',function($matches) use($base_href){
				$url = $matches[2];
				if(!preg_match('/^https?:\/\//',$url) && !preg_match('/^\//',$url)){
					$url = preg_replace('/^\.\/+/','',$url);
					$url = $base_href.$url;
				}
				return $matches[1].$url."";
			},$content);
		}

		return $content;
	}

	function _logged_admin_required(){
		return true;
	}
}

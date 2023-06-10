<?php
class MarkdownController extends ApiController {

	/**
	 * ### Transforms a Markdown document into HTML
	 */
	function transform(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			Atk14Require::Helper("modifier.markdown");
			$content = smarty_modifier_markdown($d["source"]);
			if($d["base_href"]){
				$base_href = $d["base_href"];
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
			$this->_report_success(array(),array(
				"content_type" => "text/plain",
				"raw_data" => $content,
			));
		}
	}
}

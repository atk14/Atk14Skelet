<?php
class MainController extends ApplicationController{

	/**
	 * The front page
	 * 
	 * See corresponding template: app/views/main/index.tpl
	 * See default layout: app/layouts/default.tpl
	 */
	function index(){
		$this->page_title = _("Welcome!");

		$page = $this->tpl_data["page"] = Page::GetInstanceByCode("homepage");
		if($page){
			$this->page_title = $page->getPageTitle();
			$this->page_description = $page->getPageDescription();
		}

		if ($page && !$page->isIndexable()) {
			$this->head_tags->setMetaTag("robots", "noindex,noarchive");
			$this->head_tags->setMetaTag("googlebot", "noindex");
		}
		$this->head_tags->setCanonical(Atk14Url::BuildLink(["action" => "main/index"], ["with_hostname" => true]));

		global $ATK14_GLOBAL;
		$_supported_langs = $ATK14_GLOBAL->getSupportedLangs();
		if (sizeof($_supported_langs)>1) {
			$_supported_langs = array_combine($_supported_langs, $_supported_langs);
			$_supported_langs["x-default"] = $ATK14_GLOBAL->getDefaultLang();
			foreach($_supported_langs as $hreflang => $lang) {
				$this->head_tags->addLinkTag("alternate", ["hreflang" => $hreflang, "href" => Atk14Url::BuildLink(["controller" => $this->controller, "action" => $this->action, "lang" => $lang], ["with_hostname" => true])]);
			}
		}
	}

	function robots_txt(){
		$this->render_layout = false;
		$this->response->setContentType("text/plain");
	}
}

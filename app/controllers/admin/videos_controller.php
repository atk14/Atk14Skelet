<?php
require(__DIR__ . "/iobjects_base.php");
class VideosController extends IobjectsBaseController {

	function create_new() {
		$this->page_title = _("Nové video");
		$this->_walk(array(
			"get_url",
			"get_description",
			"save"
		), array(
			"extra_params" => array("table_name" => $this->table_name, "record_id" => $this->record_id, "_return_uri_" => $this->_get_return_uri()),
		));
	}

	function create_new__get_url() {
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {
			return $d;
		}
	}

	function create_new__get_description() {
		global $ATK14_GLOBAL;

		$get_url = $this->returned_by["get_url"];
		$this->tpl_data["media"] = $media = $get_url["url"];

		$def_lang = $ATK14_GLOBAL->getDefaultLang();

		$this->form->set_initial(array(
			"title_$def_lang" => $media->title,
			"description_$def_lang" => $media->description,
		));
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {
			return $d;
		}
	}

	function create_new__save() {
		$get_url = $this->returned_by["get_url"];
		$d = $this->returned_by["get_description"];
		$d += array(
			"url" => $get_url["url"]->url,
			"html" => $get_url["url"]->html,
			"image_url" => Pupiq::CreateImage($get_url["url"]->thumbnailUrl),
		);
		$video = Video::CreateNewRecord($d);
		IobjectLink::CreateNewRecord(array(
			"iobject_id" => $video,
			"linked_table" => $this->table_name,
			"linked_record_id" => $this->record_id,
		));
		$this->flash->success(_("Vložili jste úspěšně video"));
		$this->_redirect_back();
	}
}

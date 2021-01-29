<?php
class TemporaryFileUploadsController extends ApiController {

	function create_new(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$file = $d["file"];

			if($file->chunkedUpload()){
				$token = $file->getToken();
				if($file->firstChunk()){
					$temporary_file_upload = TemporaryFileUpload::CreateNewRecordByHttpUploadedFile($file);
					$this->session->s("temporary_file_upload_id_$token",$temporary_file_upload->getId());
				}else{
					$id = $this->session->g("temporary_file_upload_id_$token");
					if(is_null($id)){
						$this->_report_fail(_("Error 1"));
						return;
					}
					$temporary_file_upload = TemporaryFileUpload::GetInstanceById($id);
					if(!$temporary_file_upload){
						$this->_report_fail(_("Error 2"));
						return;
					}
					$full_path = $temporary_file_upload->getFullPath();
					if(!file_exists($full_path)){
						$this->_report_fail(_("Error 3 (file $full_path doen't exist)"));
						return;
					}
					$temporary_file_upload->appendChunk($file);
					unlink($file->getTmpFileName());
				}
			}else{

				$temporary_file_upload = TemporaryFileUpload::CreateNewRecordByHttpUploadedFile($file);

			}

			// the mime type is verified at the upload end
			if($temporary_file_upload->fullyUploaded()){
				$mime_type = Files::DetermineFileType($temporary_file_upload->getFullPath(),["original_filename" => $temporary_file_upload->getFilename()]);
				if($mime_type && $temporary_file_upload->getMimeType()!==$mime_type){
					$temporary_file_upload->s("mime_type",$mime_type);
				}
			}

			$this->api_data = $this->_dump_temporary_file_upload($temporary_file_upload);
		}
	}

	function destroy(){
		if($this->request->post() && ($d = $this->form->validate($this->params))){
			$file = TemporaryFileUpload::GetInstanceByToken($d["token"]);
			if($file){
				$file->destroy();
			}
			$this->api_data = [];
		}
	}

	function _dump_temporary_file_upload($temporary_file_upload){
		Atk14Require::Helper("modifier.format_bytes");

		$bytes_uploaded = !is_null($temporary_file_upload->getBytesUploaded()) ? $temporary_file_upload->getBytesUploaded() : $temporary_file_upload->getFilesize();
		$filesize = $temporary_file_upload->getFilesize();

		return array(
			"id" => $temporary_file_upload->getId(),
			"token" => $temporary_file_upload->getToken(),
			"filename" => $temporary_file_upload->getFilename(),
			"filesize" => $temporary_file_upload->getFilesize(),
			"filesize_localized" => smarty_modifier_format_bytes($temporary_file_upload->getFilesize()),
			"mime_type" => $temporary_file_upload->getMimeType(),

			"chunked_upload" => $temporary_file_upload->chunkedUpload(),
			"fully_uploaded" => $temporary_file_upload->fullyUploaded(),
			"percent_uploaded" => $filesize == 0 ? 100 : round($bytes_uploaded / ($filesize / 100.0)),

			"destroy_url" => $this->_link_to(["action" => "destroy", "token" => $temporary_file_upload->getToken(), "format" => "json"],["with_hostname" => true]),
		);
	}
}

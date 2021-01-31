<?php
class AsyncFileInput extends FileInput {

	function render($name, $value, $options=array()){
		$input = parent::render($name, $value, $options); // <input type="file' ...>

		$template_loading = '
			<div class="progress">
				<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
				</div>
			</div>
		';

		$template_done = '
			%filename% %filesize_localized% <button type="button" class="js--remove" data-destroy_url="%destroy_url%">x</button>
			<input type="hidden" name="%name%" value="%token%">
		';

		$template_error = '<span class="text text-danger">%error_message%</span> <button type="button" class="js--confirm">ok</button>';

		$default = $input;

		if(is_string($value) && ($file = TemporaryFileUpload::GetInstanceByToken($value))){
			Atk14Require::Helper("modifier.format_bytes");
			$default = strtr($template_done,array(
				"%filename%" => h($file->getFilename()),
				"%filesize_localized%" => smarty_modifier_format_bytes($file->getFilesize()),
				"%name%" => h($name),
				"%token%" => $file->getToken(),
				"%destroy_url%" => h(Atk14Url::BuildLink([
					"namespace" => "api",
					"controller" => "temporary_file_uploads",
					"action" => "destroy",
					"token" => $file->getToken(),
					"format" => "json",
				],[
					"with_hostname" => true,
				])),
			));
		}

		$out = '
			<div class="js--async-file" data-name="'.h($name).'" data-input="'.h($input).'" data-template_loading="'.h($template_loading).'" data-template_done="'.h($template_done).'" data-template_error="'.h($template_error).'">
				'.$default.'
			</div>
		';

		return $out;
	}

	function value_from_datadict($data, $name){
		if(isset($data[$name]) && is_string($data[$name]) && strlen($data[$name])){
			return $data[$name];
		}
		return parent::value_from_datadict($data, $name);
	}
}

{*
 * {render partial="shared/xhr_upload_image_form" url="{link_to action="images/create_new" _connector="&"}" label="Select new luxury images"}
 *}

{if !$label}{capture assign=label}{t}Add images{/t}{/capture}{/if}
{assign uniqid "id_file_"|uniqid}

<form action="{$url}" class="form-horizontal js--xhr_upload_image_form" method="post" enctype="multipart/form-data">

	<div class="custom-file">
		<input type="file" name="files[]" class="custom-file-input" id="{$uniqid}" required multiple>
		<label class="custom-file-label" for="{$uniqid}">{$label}</label>
	</div>

	<div class="progress progress-image_upload">
		<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
		</div>
	</div>

	<button class="btn btn-primary nojs-only" type="submit">{t}Submit form{/t}</button>

</form>

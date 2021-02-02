<h5>{$field_name}</h5>

{if $file}
	<ul>
		<li>name: {$file->getName()}</li>
		<li>filename: {$file->getFileName()}</li>
		<li>filesize: {$file->getFileSize()}</li>
		<li>mime_type: {$file->getMimeType()}</li>
		<li>is_image: {$file->isImage()|display_bool}</li>
		<li>is_pdf: {$file->isPdf()|display_bool}</li>
		<li>image_width: {$file->getImageWidth()|default:$mdash}</li>
		<li>image_height: {$file->getImageHeight()|default:$mdash}</li>
		<li>chunked_upload: {$file->chunkedUpload()|display_bool}</li>
		<li>tmp_filename: {$file->getTmpFileName()}</li>
	</ul>
{else}
	<p>No file uploaded</p>
{/if}

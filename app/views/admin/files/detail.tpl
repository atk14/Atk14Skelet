<h1>{$page_title}</h1>

<dl class="dl-horizontal">
	<dt>{t}ID{/t}</dt>
	<dd>{$file->getId()}</dd>
	<dt>{t}Title{/t}</dt>
	<dd>{$file->getName()}</dd>
	<dt>{t}Filename{/t}</dt>
	<dd>{$file->getFilename()}</dd>
	<dt>{t}File Type{/t}</dt>
	<dd>{$file->getMimeType()}</dd>
	<dt>{t}Filesize{/t}</dt>
	<dd>{$file->getFilesize()|format_bytes}</dd>
	<dt>{t}URL{/t}</dt>
	<dd><a href="{$file->getUrl()}">{$file->getUrl()}</a></dd>
</dl> 

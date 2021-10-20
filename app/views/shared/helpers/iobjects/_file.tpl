<div class="iobject iobject--file">
	<a href="{$file->getUrl()}">
		<span>
			<span class="fileicon fileicon-{$file->getSuffix()} fileicon-color"></span>
			<span class="file-name">{$file->getTitle()}</span>
		</span>
		<span class="iobject--file__meta">({$file->getSuffix()|upper}, {$file->getFilesize()|format_bytes})</span>
	</a>
</div>


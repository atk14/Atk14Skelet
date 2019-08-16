<h1>{$page_title}</h1>

{$image|pupiq_img:"600x600" nofilter}

<ul>
	<li>{t}The image was uploaded at:{/t} {$image->getCreatedAt()|format_datetime}</li>
	<li>{t}The image was uploaded by:{/t} {$image->getCreatedByUserId()|user_name}</li>
	<li>{t}The size of the original:{/t} {$image->getOriginalWidth()}x{$image->getOriginalHeight()}</li>
</ul>

{render partial="shared/form"}

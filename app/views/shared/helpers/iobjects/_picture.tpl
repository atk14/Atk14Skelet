{assign var=picture value=$iobject->getObject()}
<figure class="io-picture">
	<a class="io-picture__link" href="{$picture->getUrl()|img_url:1200}">
		<img class="io-picture__img" {!$picture->getUrl()|img_attrs:929} class="img-responsive" alt="{$picture->getTitle()}">
	</a>
	{if ($picture->getTitle() && $picture->isTitleVisible()) || $picture->getDescription()}
	<figcaption class="io-picture__caption">
		{if $picture->getTitle() && $picture->isTitleVisible()}
			<div class="io-picture__title">{$picture->getTitle()}</div>
		{/if}
		{if {$picture->getDescription()}}
			<div class="io-picture__desc">{$picture->getDescription()}</div>
		{/if}
	</figcaption>
	{/if}
</figure>

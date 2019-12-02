{assign var=picture value=$iobject->getObject()}
{assign geometry_detail "1600"}
<div class="iobject iobject--picture">
	<figure>
		<a class="iobject--picture__link" href="{!$picture|img_url:$geometry_detail}" title="{$picture->getTitle()}" data-size="{$picture|img_width:$geometry_detail}x{$picture|img_height:$geometry_detail}">
			<img class="iobject--picture__img img-fluid" {!$picture->getUrl()|img_attrs:1500} alt="{$picture->getTitle()}" 
srcset="{!$picture|img_url:600} 600w, {!$picture|img_url:800} 800w, {!$picture|img_url:1500} 1500w" 
sizes="100vw">
		</a>
		{if ($picture->getTitle() && $picture->isTitleVisible()) || $picture->getDescription()}
		<figcaption class="iobject__caption">
			{if $picture->getTitle() && $picture->isTitleVisible()}
			<div class="iobject__title"><a class="iobject--picture__link" href="{!$picture|img_url:$geometry_detail}" title="{if $picture->getTitle()}{$picture->getTitle()}{/if}" data-size="{$picture|img_width:$geometry_detail}x{$picture|img_height:$geometry_detail}">{!"search-plus"|icon}</a> <span class="iobject__title__separator">|</span> {$picture->getTitle()}</div>
			{/if}
			{if {$picture->getDescription()}}
				<div class="iobject__description">{$picture->getDescription()}</div>
			{/if}
		</figcaption>
		{/if}
	</figure>
</div>
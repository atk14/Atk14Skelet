{assign geometry_detail "2000x1800"}
{assign render_link 1}
{if $picture->getUrl()|img_width:"1600"<1600}
	{* the picture is too small to render a link *}
	{assign render_link 0}
{/if}

<div class="iobject iobject--picture">
	<figure>
		{if $render_link}<a class="iobject--picture__link" href="{!$picture|img_url:$geometry_detail}" title="{$picture->getTitle()}" data-pswp-width="{$picture|img_width:$geometry_detail}" data-pswp-height="{$picture|img_height:$geometry_detail}">{/if}
		<picture>
			{* sem prijde source tag pro webp, pak bude potreba k source tagum doplnit atribut type* }
			{* srcset a sizes vzaty z puvodniho img tagu. *}
			<source srcset="{!$picture|img_url:600} 600w, {!$picture|img_url:800} 800w, {!$picture|img_url:1500} 1500w" sizes="(max-width:1400px) 100vw, 1400px">
			{* img tag ponechan jak byl. *}
			<img class="iobject--picture__img img-fluid" {!$picture->getUrl()|img_attrs:1500} alt="{$picture->getAlt()}" 
srcset="{!$picture|img_url:600} 600w, {!$picture|img_url:800} 800w, {!$picture|img_url:1500} 1500w" 
sizes="(max-width:1400px) 100vw, 1400px">
		</picture>
		{if $render_link}</a>{/if}
		{if ($picture->getTitle() && $picture->isTitleVisible()) || $picture->getDescription()}
		<figcaption class="iobject__caption">
			{if $picture->getTitle() && $picture->isTitleVisible()}
			<div class="iobject__title">{if $render_link}<a class="iobject--picture__link js_picture_trigger" href="{!$picture|img_url:$geometry_detail}" title="{if $picture->getTitle()}{$picture->getTitle()}{/if}">{!"search-plus"|icon}</a> <span class="iobject__title__separator">|</span> {/if}{$picture->getTitle()}</div>
			{/if}
			{if {$picture->getDescription()}}
				<div class="iobject__description">{$picture->getDescription()}</div>
			{/if}
		</figcaption>
		{/if}
	</figure>
</div>

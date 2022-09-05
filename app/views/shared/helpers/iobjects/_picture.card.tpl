{assign geometry_detail 1600}
{assign render_link 1}
{if $picture->getUrl()|img_width:"1600"<1600}
	{* the picture is too small to render a link *}
	{assign render_link 0}
{/if}
<div class="iobject iobject--picture iobject--picture-card card">
	<figure class="">
		{if $render_link}<a class="iobject--picture__link image-wrap" href="{!$picture|img_url:$geometry_detail}" title="{$picture->getTitle()}" data-pswp-width="{$picture|img_width:$geometry_detail}" data-pswp-height="{$picture|img_height:$geometry_detail}">{else}<div class="image-wrap">{/if}
			<picture>
				<source  
	srcset="{!$picture|img_url:600} 600w, {!$picture|img_url:800} 800w, {!$picture|img_url:1500} 1500w" 
	sizes="(max-width:1400px) 100vw, 1400px">
				<img class="iobject--picture__img img-fluid card-img-top" {!$picture->getUrl()|img_attrs:1500} alt="{$picture->getAlt()}" 
	srcset="{!$picture|img_url:600} 600w, {!$picture|img_url:800} 800w, {!$picture|img_url:1500} 1500w" 
	sizes="(max-width:1400px) 100vw, 1400px">
			</picture>
		{if $render_link}</a>{else}</div>{/if}
		{if ($picture->getTitle() && $picture->isTitleVisible()) || $picture->getDescription()}
		<figcaption class="card-body iobject__caption">
			{if $picture->getTitle() && $picture->isTitleVisible()}
			<div class="iobject__title card-title">{if $render_link}<a class="iobject--picture__link js_picture_trigger" href="{!$picture|img_url:$geometry_detail}" title="{if $picture->getTitle()}{$picture->getTitle()}{/if}">{!"search-plus"|icon}</a> <span class="iobject__title__separator">|</span> {/if}{$picture->getTitle()}</div>
			{/if}
			{if {$picture->getDescription()}}
				<div class="iobject__description card-text">{$picture->getDescription()}</div>
			{/if}
		</figcaption>
		{/if}
	</figure>
</div>

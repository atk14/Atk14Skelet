{assign format $picture->getUrl()|img_format}
{if in_array($format,["png","svg"])}
	{assign fallback_format $format}
{else}
	{assign fallback_format "jpg"}
{/if}

{if $format=="svg"}

	<picture>
		<img class="iobject--picture__img img-fluid" alt="{$picture->getAlt()}" {!$picture->getUrl()|img_attrs:"1500"}>
	</picture>

{else}

	<picture>
		<source srcset="{!$picture|img_url:"600,format=webp"} 600w, {!$picture|img_url:"800,format=webp"} 800w, {!$picture|img_url:"1500,format=webp"} 1500w" sizes="(max-width:1400px) 100vw, 1400px" type="image/webp">
		<img class="iobject--picture__img img-fluid" alt="{$picture->getAlt()}" 
			{!$picture->getUrl()|img_attrs:"1500,format=$fallback_format"}
			srcset="{!$picture|img_url:"600,format=$fallback_format"} 600w, {!$picture|img_url:"800,format=$fallback_format"} 800w, {!$picture|img_url:"1500,format=$fallback_format"} 1500w" 
			sizes="(max-width:1400px) 100vw, 1400px"
		>
	</picture>

{/if}

{assign var=video value=$iobject->getObject()}
<div style="margin: 1em 0;">
	<div class="embed-responsive embed-responsive-16by9">
	{!$video->getHtml()}
	</div>
	{if $video->getTitle() && $video->isTitleVisible()}<br><strong>{$video->getTitle()}</strong>{/if}
	{if $video->getDescription()}<br>{$video->getDescription()}{/if}
</div>

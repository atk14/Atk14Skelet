{assign var=video value=$iobject->getObject()}
<div class="iobject iobject--video">
	<div class="embed-responsive embed-responsive-16by9">
	{!$video->getHtml()}
	</div>
	<div class="iobject__caption">
		{if $video->getTitle() && $video->isTitleVisible()}<div class="iobject__title">{$video->getTitle()}</div>{/if}
		{if $video->getDescription()}<div class="iobject__description">{$video->getDescription()}</div>{/if}
	</div>
</div>

<div class="iobject iobject--video">
	{!$admin_menu}
	<div class="embed-responsive embed-responsive-16by9">
	{!$video->getHtml()}
	</div>
	{if $video->isTitleVisible() && ( $video->getTitle() || $video->getDescription() )}
	<div class="iobject__caption">
		{if $video->getTitle() && $video->isTitleVisible()}<div class="iobject__title">{$video->getTitle()}</div>{/if}
		{if $video->getDescription()}<div class="iobject__description">{$video->getDescription()}</div>{/if}
	</div>
	{/if}
</div>

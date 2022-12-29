<li class="list-group-item clearfix" data-id="{$gallery_item->getId()}">
	{dropdown_menu clearfix=false}
		{a action="gallery_items/edit" id=$gallery_item}{!"edit"|icon} {t}Edit{/t}{/a}
		{a_destroy controller="gallery_items" id=$gallery_item}{!"remove"|icon} {t}Delete{/t}{/a_destroy}
		{a action="gallery_items/image_download" id=$gallery_item}{!"download"|icon} {t}Download original image{/t}{/a}
	{/dropdown_menu}

	<div class="float-left">
		{render partial="shared/list_thumbnail" image=$gallery_item->getImageUrl()}
	</div>

	{if !$gallery_item->getTitle()|strlen && !$gallery_item->getDescription()|strlen}
		<small><em>{t}bez titulku nebo popisu{/t}</em></small>
	{else}
		{if $gallery_item->getTitle()|strlen}
			<strong>{$gallery_item->getTitle()}</strong><br>
		{/if}
		{$gallery_item->getDescription()}
	{/if}
</li>

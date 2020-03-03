<li class="list-group-item clearfix" data-id="{$gallery_item->getId()}">
	{dropdown_menu clearfix=false}
		{a action="gallery_items/edit" id=$gallery_item}{!"edit"|icon} {t}Edit{/t}{/a}
		{a_destroy controller="gallery_items" id=$gallery_item}{!"remove"|icon} {t}Delete{/t}{/a_destroy}
	{/dropdown_menu}

	<div class="float-left">
		{render partial="shared/list_thumbnail" image=$gallery_item->getImageUrl()}
	</div>

	<strong>{!$gallery_item->getTitle()|h|default:"<em>{t}bez titulku{/t}</em>"}</strong><br>
	{!$gallery_item->getDescription()|h|default:"<em>{t}bez popisu{/t}</em>"}
</li>

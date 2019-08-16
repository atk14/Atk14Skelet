{assign var=gallery_items value=$gallery->getGalleryItems()}

<h1>{$page_title}</h1>

<p>
{$gallery->getDescription()|default:"{t}bez popisu{/t}"}
</p>

{if !$gallery_items}

	<p>{t}Fotogalerie zatím neobsahuje žádný obrázek.{/t}</p>

{else}

	<ul class="list-group list-sortable" data-sortable-url="{link_to action="gallery_items/set_rank"}">
	{foreach $gallery->getGalleryItems() as $item}

		<li class="list-group-item clearfix" data-id="{$item->getId()}">

				<div class="pull-left" style="padding-right: 1em;">
					{render partial="shared/list_thumbnail" image=$item->getImageUrl()}
				</div>

				<div class="pull-right">
					{dropdown_menu}
						{a action="gallery_items/edit" id=$item}<i class="glyphicon glyphicon-edit"></i> {t}Upravit{/t}{/a}
						{a_destroy controller="gallery_items" id=$item}<i class="glyphicon glyphicon-remove"></i> {t}Smazat{/t}{/a_destroy}
					{/dropdown_menu}
				</div>

				<strong>{a action="gallery_items/edit" id=$item _title="{t}editovat{/t}"}{$item->getTitle()|default:"{t}bez titulku{/t}"}{/a}</strong><br>
				{$item->getDescription()|default:"{t}bez popisu{/t}"}
		</li>
	{/foreach}
	</ul>

{/if}

<h3>{t}Přidat fotografii{/t}</h3>

{render partial="shared/form" form=$create_item_form}


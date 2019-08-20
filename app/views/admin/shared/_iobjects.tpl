{*
 * {render partial="shared/iobjects" object=$article}
 *}

{assign var=iobjects value=Iobject::GetIobjects($object)}

<h3>{t}Objekty vkládané do textu{/t}</h3>

{if $iobjects}

	<p>{t}Zkopírujte značku daného objektu ([#...]) do textu.{/t}</p>

	<ul class="list-group list-sortable" data-sortable-url="{link_to action="iobject_links/set_rank"}">
		{foreach $iobjects as $iobject}
			{assign var=preview_image_url value=$iobject->getPreviewImageUrl()}
			{capture assign=confirm}{t}Opravdu odstranit objekt?{/t}{/capture}

			<li class="list-group-item" data-id="{$iobject->getIobjectLinkId($object)}">
				<div class="pull-left" style="padding-right: 0.5em;">
					{if $preview_image_url}
						{!$preview_image_url|pupiq_img:40x40xcrop}
					{else}
						<img src="{$public}/images/question_mark_icon.png" width="40" height="40">
					{/if}
				</div>
				{!$iobject|iobject_type}
				{a controller=$iobject->getReferredTable() action=detail id=$iobject}
					{if $iobject->getTitle()}
					{$iobject->getTitle()}
					{else}
						<em>{t}bez názvu{/t}</em>
					{/if}
				{/a}

				<div class="pull-right">
					{dropdown_menu}
						{a controller=$iobject->getReferredTable() action=edit id=$iobject}<span class="glyphicon glyphicon-edit"></span> {t}Změnit{/t}{/a}
						{a_destroy controller="iobjects" id=$iobject}<i class="glyphicon glyphicon-remove"></i> {t}Smazat{/t}{/a_destroy}
					{/dropdown_menu}
				</div>
				<br><span title="{t}tento řádek zkopírujte do textu{/t}">{$iobject->getInsertMark()}</span>
			</li>

		{/foreach}
	</ul>

{else}

	<p>{t}Momentálně tady nejsou žádné objekty.{/t}</p>

{/if}

<p>
	{a action="pictures/create_new" table_name=$object->getTableName() record_id=$object _class="btn btn-default"}{t}Přidat obrázek{/t}{/a}
	{a action="galleries/create_new" table_name=$object->getTableName() record_id=$object return_uri=$request->getUri() _class="btn btn-default"}{t}Přidat fotogalerii{/t}{/a}
	{a action="files/create_new" table_name=$object->getTableName() record_id=$object _class="btn btn-default"}{t}Přidat přílohu{/t}{/a}
	{a action="videos/create_new" table_name=$object->getTableName() record_id=$object _class="btn btn-default"}{t}Přidat video{/t}{/a}
</p>


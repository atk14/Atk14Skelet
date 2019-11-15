{*
 * Tato sablonka se vyrenderuje do jsonu pri XHR uploadu obrazku do adminu (viz controllers/admin/images_controller.php)
 *}
<li class="list-group-item clearfix" data-id="{$image->getId()}">

	{dropdown_menu clearfix=false}
		{a action="images/edit" id=$image}{icon glyph=edit} {t}Edit{/t}{/a}
		{a_destroy action="images/destroy" id=$image}{icon glyph=remove} {t}Remove image{/t}{/a_destroy}
	{/dropdown_menu}
	
	<div class="float-left">
	{render partial="shared/list_thumbnail" image=$image align="left"}
	</div>

	{if $image->getName()}<strong>{$image->getName()}</strong><br>{/if}
	{$image->getDescription()}

</li>

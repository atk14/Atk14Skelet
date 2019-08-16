{*
 * Renders the given image intended to be used in a list
 *
 * A replacement icon is used instead of missing image.
 *
 * {render partial="shared/list_thumbnail" image=$card->getImage()}
 * {render partial="shared/list_thumbnail" image=$card->getImage() align="left"}
 *}
{if $image}
	<a class="list-thumbnail" href="{$image|img_url:800x800}" title="{t}Show larger image{/t}"><img {!$image|img_attrs:"!80x80"}{if $align} align="{$align}"{/if}></a>
{else}
	<span class="list-thumbnail"><img src="{$public}images/camera.svg" width="80" height="80" title="{t}no image{/t}"{if $align} align="{$align}"{/if}></span>
{/if}

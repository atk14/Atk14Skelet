{*
 * Renders the given image intended to be used in a list
 *
 * A replacement icon is used instead of missing image.
 *
 * {render partial="shared/list_thumbnail" image=$card->getImage()}
 * {render partial="shared/list_thumbnail" image=$card->getImage() align="left"}
 * {render partial="shared/list_thumbnail" image=$card->getImage() thumbnail_size="40"}
 *}
{if !$thumbnail_size}{assign thumbnail_size "80"}{/if}
{capture assign=geometry_thumbnail}!{$thumbnail_size}x{$thumbnail_size}{/capture} {* !80x80 *}
{if $image}
	<a class="list-thumbnail" href="{$image|img_url:800x800}" title="{t}Show larger image{/t}"><img {!$image|img_attrs:$geometry_thumbnail}{if $align} align="{$align}"{/if}></a>
{else}
	<span class="list-thumbnail"><img src="{$public}images/camera.svg" width="{$thumbnail_size}" height="{$thumbnail_size}" title="{t}no image{/t}"{if $align} align="{$align}"{/if}></span>
{/if}

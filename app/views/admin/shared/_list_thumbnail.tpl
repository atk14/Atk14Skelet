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
{capture assign=geometry_thumbnail}{$thumbnail_size}x{$thumbnail_size}x#ffffff{/capture} {* 80x80x#ffffff *}
{if $image}
	<span class="list-thumbnail"><img {!$image|img_attrs:$geometry_thumbnail}{if $align} align="{$align}"{/if}></span>
{else}
	<span class="list-thumbnail"><img src="{$public}images/camera.svg" width="{$thumbnail_size}" height="{$thumbnail_size}" title="{t}no image{/t}"{if $align} align="{$align}"{/if}></span>
{/if}

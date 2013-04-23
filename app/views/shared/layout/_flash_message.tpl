{*
 * Displays flash message if there is any.
 *
 * Beware! There is no html escaping.
 * Thus one can place a link to somewhere within the flash message or something.
 *}
{if $flash->notice()}
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{!$flash->notice()}
	</div>
{/if}
{if $flash->error()}
	<div class="alert alert-error">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{!$flash->error()}
	</div>
{/if}
{if $flash->success()}
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{!$flash->success()}
	</div>
{/if}

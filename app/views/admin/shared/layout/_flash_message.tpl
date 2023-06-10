{*
 * Displays flash message if there is any.
 *
 * Beware! There is no html escaping.
 * Thus one can place a link to somewhere within the flash message or something.
 *}
{if $flash->warning()}
	{message block=1}
		{!$flash->warning()}
	{/message}
{/if}
{if $flash->error()}
	{message type="error" block=1}
		{!$flash->error()}
	{/message}
{/if}
{if $flash->success()}
	{message type="success" block=1}
		{!$flash->success()}
	{/message}
{/if}
{if $flash->info()}
	{message type="info" block=1}
		{!$flash->info()}
	{/message}
{/if}

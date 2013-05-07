<h2>{$page_title}</h2>

<ul>
{foreach from=$controllers key=ctrl item=item}
	<li>
		<h3>{$ctrl}</h3>
		<ul style="padding-bottom: 2em;">
			{foreach from=$item.commands item=cmd}
				<li>
					<a href="{$cmd.url}">{$cmd.action}</a>
					{if $cmd.inline_description}{$cmd.inline_description|strip_tags}{/if}
				</li>
			{/foreach}
		</ul>
	</li>
{/foreach}
</ul>

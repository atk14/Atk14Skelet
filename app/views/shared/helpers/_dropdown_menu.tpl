<div class="btn-group btn-group-sm{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
	{!$first_line}
	{if $lines}
		<button class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">{t}Show menu{/t}</span>
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			{foreach $lines as $line}
				{!$line}
			{/foreach}
		</div>
	{/if}
</div>

{if $clearfix}
<div class="clearfix"></div>
{/if}
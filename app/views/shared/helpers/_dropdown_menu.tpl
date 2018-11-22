<div class="btn-group btn-group-sm{if $pull=="right"} pull-right{/if}{if $pull=="left"} pull-left{/if}{if $class} {$class}{/if}">
	{!$first_line}
	{if $lines}
		<button class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" href="#">
			<span class="caret"></span>
			<span class="sr-only">{t}Show menu{/t}</span>
		</button>
		<ul class="dropdown-menu dropdown-menu-right">
			{foreach $lines as $line}
				<li>{!$line}</li>
			{/foreach}
		</ul>
	{/if}
</div>

{if $clearfix}
<div class="clearfix"></div>
{/if}
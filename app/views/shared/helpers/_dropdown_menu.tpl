<div class="btn-group btn-group-sm">
	{!$first_line}
	{if $lines}
		<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
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

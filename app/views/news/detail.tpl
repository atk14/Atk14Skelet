{render partial="articles/detail" article=$news}

<ul class="pager">
	{if $older_news}
		<li class="previous">
			{a action=detail id=$older_news}&larr; {$older_news->getTitle()|truncate:24}{/a}
		</li>
	{/if}
	<li>{a action=index _class="muted"}<i class="icon-list"></i> {t}News archive{/t}{/a}</li>
	{if $newer_news}
		<li class="next">
			{a action=detail id=$newer_news}{$newer_news->getTitle()|truncate:24} &rarr;{/a}
		</li>
	{/if}
</ul>

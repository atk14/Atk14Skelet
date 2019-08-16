{if $pages}
<ul class="list--tree">

{foreach $pages as $page}

	<li>
		{a action="pages/detail" id=$page _with_hostname=1}{$page->getTitle()}{/a}
		<p>
			{$page->getTeaser()|markdown|strip_tags|truncate:200}
		</p>
		{render partial="pages" pages=$page->getChildPages()}
	</li>

{/foreach}

</ul>
{/if}

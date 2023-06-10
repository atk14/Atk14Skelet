<header>
	<h1>{a action="main/index"}{$namespace}{/a} &rarr; {$page_title}</h1>
</header>

<section>
{if $documentation}
	{!$documentation}
{else}
	<p>{!$page_description|trim|nl2br}</p>
{/if}

{render partial="shared/form"}
</section>
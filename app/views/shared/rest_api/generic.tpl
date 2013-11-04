<h1>{$page_title}</h1>

{if $documentation}
	{!$documentation}
{else}
	<p>{!$page_description|trim|nl2br}</p>
{/if}

{render partial="shared/form"}

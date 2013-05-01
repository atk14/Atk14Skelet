<h2>{$page_title}</h2>

{if $documentation}
	{!$documentation}
{else}
	<p>{!$page_description|trim|nl2br}</p>
{/if}

{render partial="shared/form"}

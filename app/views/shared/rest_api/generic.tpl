{if $documentation}
	{$documentation nofilter}
{else}
	<p>{$page_description|trim|nl2br nofilter}</p>
{/if}

{render partial="shared/generic_form"}

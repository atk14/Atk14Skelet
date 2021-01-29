<h1>{$page_title}</h1>

{render partial="shared/form"}

{if $cleaned_data}
	{dump var=$cleaned_data}
{/if}

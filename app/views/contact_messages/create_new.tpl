{if !$rendering_component}
	{render partial="shared/layout/content_header" title=$page_title}
{else}
	<h2>{$page_title}</h2>
{/if}

<p class="lead mb-5">
	{t}If you have any question, contact us through the following form. We will reply to you as soon as we can.{/t}
</p>

{render partial="shared/form"}

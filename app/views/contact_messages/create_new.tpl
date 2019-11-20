{if !$rendering_component}
	<h1>{$page_title}</h1>
{else}
	<h2>{$page_title}</h2>
{/if}

<p class="lead mb-5">
	{t}If you have any question, contact us through the following form. We will reply to you as soon as we can.{/t}
</p>

{render partial="shared/form"}

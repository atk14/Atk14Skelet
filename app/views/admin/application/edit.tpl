<h1>{$page_title}</h1>

{render partial="shared/form"}

{if $has_iobjects}
	<hr>
	{render partial="shared/iobjects" object=$object}
{/if}

{if $has_image_gallery}
	<hr>
	{render partial="shared/image_gallery" object=$object}
{/if}

{if $has_attachments}
	<hr>
	{render partial="shared/attachments" object=$object}
{/if}
{if $has_iobjects}
	{render partial="shared/editor_modals" iobjects_for=$object}
{else}
	{render partial="shared/editor_modals"}
{/if}
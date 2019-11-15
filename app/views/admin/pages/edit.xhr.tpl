$form.replaceWith({jstring}{render partial="edit_form"}{/jstring});
ADMIN.utils.initializeMarkdonEditors();
UTILS.leaving_unsaved_page_checker.init();

{if $form->has_errors()}
	{js_notify type="error"}{t}Saving failed.{/t}{/js_notify}
{/if}
{render partial="shared/flash_message.xhr"}

{* the Markdown editors are kept as they are (view, cursor, undo history, fullscreen) *}
UTILS.EditFormReplacer.replace( $form[ 0 ], {jstring}{render partial="edit_form"}{/jstring} );
window.dispatchEvent( new Event( "edit_form_replaced" ) );
{if $form->has_errors()}
	{js_notify type="error"}{t}Saving failed.{/t}{/js_notify}
{/if}
{render partial="shared/flash_message.xhr"}

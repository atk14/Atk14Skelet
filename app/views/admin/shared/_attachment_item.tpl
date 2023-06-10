{*
 * Tato sablonka se vyrenderuje do jsonu pri XHR uploadu obrazku do adminu (viz controllers/admin/attachments_controller.php)
 *}
<li class="list-group-item" data-id="{$attachment->getId()}">
			<a href="{$attachment->getUrl()}"><span class="fileicon fileicon-{$attachment->getSuffix()} fileicon-color">&nbsp;&nbsp;{$attachment->getName()} ({if $attachment->getName()!=$attachment->getFilename()}{$attachment->getFilename()}, {/if}{$attachment->getFilesize()|format_bytes})</a>

			{dropdown_menu}
				{a action="attachments/edit" id=$attachment}{!"pencil-alt"|icon} {t}Edit{/t}{/a}

				{capture assign="confirm"}{t 1=$attachment->getName()|h escape=no}You are about to delete the attachment %1
Are you sure about that?{/t}{/capture}
				{a_destroy action="attachments/destroy" id=$attachment _method=post _confirm=$confirm}{!"trash-alt"|icon} {t}Remove{/t}{/a_destroy}
			{/dropdown_menu}

</li>

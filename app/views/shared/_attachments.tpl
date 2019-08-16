{*
 * {render partial="shared/attachments" object=$card_section}
 *}

{assign var=attachments value=Attachment::GetAttachments($object)}
{if $attachments}
	<section class="attachments">
		<h4>{t}Attachments{/t}</h4>
		<ul class="list-unstyled">
			{foreach $attachments as $attachment}
				<li>	
					<a href="{$attachment->getUrl()}"><span class="fileicon fileicon-{$attachment->getSuffix()} fileicon-color">&nbsp;&nbsp;<strong>{$attachment->getName()}</strong> [{$attachment->getMimeType()}, {$attachment->getFilesize()|format_bytes}] </a>
				</li>
			{/foreach}
		</ul>
	</section>
{/if}

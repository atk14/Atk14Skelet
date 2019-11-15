{dropdown_menu}
	{a action="edit" id=$picture}{!"edit"|icon} {t}Edit{/t}{/a}
{/dropdown_menu}

<h1>{$page_title}</h1>

{!$picture|pupiq_img:"800"}

<hr>

<dl class="dl-horizontal">
	<dt>{t}ID{/t}:</dt><dd>{$picture->getId()}</dd>
	<dt>{t}Title{/t}:</dt><dd>{$picture->getTitle()|default:$mdash}</dd>
	<dt>{t}Description{/t}:</dt><dd>{$picture->getDescription()|default:$mdash}</dd>
	<dt>{t}Dimensions of the original{/t}:</dt><dd>{$picture->getOriginalWidth()}&times;{$picture->getOriginalHeight()}</dd>
	<dt>{t}Created at{/t}:</dt><dd>{$picture->getCreatedAt()|format_datetime}</dd>
	<dt>{t}Uploaded by{/t}:</dt><dd>{$picture->getCreatedByUser()}</dd>
	<dt>{t}Updated at{/t}:</dt><dd>{$picture->getUpdatedAt()|format_datetime|default:$mdash}</dd>
	<dt>{t}Updated by{/t}:</dt><dd>{$picture->getUpdatedByUser()|default:$mdash}</dd>
</dl>

{a id=$picture format=raw}{!"download"|icon} {t}Download original{/t}{/a}

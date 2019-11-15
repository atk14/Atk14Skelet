<h1>{$page_title}</h1>

<p>
	{a action="videos/edit" id=$video _class="btn btn-primary"}{t}Změnit video{/t}{/a}
</p>

<p><strong>{t}URL videa{/t}</strong>: {$video->getUrl()}</p>

{!$video->getHtml()}

<h3>{t}Náhledový obrázek{/t}</h3>
{capture assign=dflt_no_img}{t}Není nastaven{/t}{/capture}
<p>{!$video->getImageUrl()|pupiq_img:"200x"|default:$dflt_no_img}</p>

<p>{$video->getDescription()|default:"{t}bez popisu{/t}"}</p>



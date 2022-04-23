{if !$title}
	{capture assign=title}{t}Create a new record{/t}{/capture}
{/if}
<a href="{$create_new_url}"{foreach $attrs as $attr_k => $attr_v} {$attr_k}="{$attr_v}"{/foreach}>{!$icon|icon} <span class="sr-only">{$title}</span></a>

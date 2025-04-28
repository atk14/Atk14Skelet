{if $newsletter_subscriber->getVocative()}
	{t vocative=$newsletter_subscriber->getVocative()}Dobrý den %1,{/t}<br/><br/>
{else}
	{t}Dobrý den,{/t}<br/><br/>
{/if}

{t}děkujeme Vám za registraci k odběru našeho newsletteru.{/t}<br/></br/>

{t}Pokud nadále nechcete dostávat zprávy o našich novinkách, můžete se z odběru odhlásit na adrese:{/t}<br/><br/>

<a href="{$unsubscribe_url}" style="{$link_style}">{$unsubscribe_url}</a>

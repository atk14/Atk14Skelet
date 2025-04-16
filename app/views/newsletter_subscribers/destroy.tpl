<h1>{$page_title}</h1>

<p>
	{t email=$newsletter_subscriber->getEmail()|h escape=no}Na této stránce můžete odhlásit zasílání našeho newsletteru na e-mailovou adresu <em>%1</em>.{/t}
</p>

{render partial="shared/form"}

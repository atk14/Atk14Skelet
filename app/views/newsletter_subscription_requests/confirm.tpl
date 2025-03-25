<h1 class="h3">{$page_title}</h1>

<p>{t}Pro přihlášení se k odběru novinek klikněte na tlačítko.{/t}</p>

{form}
	{render partial="shared/form_error"}

	<div class="form__footer">
		<button type="submit" class="btn btn-primary">{t}Potvrdit přihlášení{/t}</button>
		{a action="cancel" token=$newsletter_subscription_request->getToken() _class="btn btn-danger" _method=post}{t}Zrušit toto potvrzení{/t}{/a}
	</div>
{/form}

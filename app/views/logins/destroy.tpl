{render partial="shared/layout/content_header" title=$page_title}
<section>
	<p>
		{t escape=no name=$logged_user->getName()|h}You are signed in as <em>%1</em>.{/t}<br>
		{t}Click on the button to sign out.{/t}
	</p>

	{render partial="shared/form"}
</section>

<h1>{$page_title}</h1>

<div class="row-fluid">
	<p>
		{t}If you have any question, contact us through the following form. We will reply to you as soon as we can.{/t}<br>
		{t}Below the form you'll find other contact information.{/t}
	</p>
	<div class="span6">
		{render partial="shared/form" form_class="well"}
	</div>

	<address class="span6">
		<strong>{t team_name="ATK14_APPLICATION_NAME"|dump_constant}%1 Team{/t}</strong><br>
		Elm Street 1428<br>
		Springwood<br>
		Ohio<br>
		United States<br>
		<br>
		<strong>{t}E-mail{/t}:</strong><br>
		{"DEFAULT_EMAIL"|dump_constant}<br>
		<strong>{t}Phone{/t}:</strong><br>
		+1-541-754-3010
		<br>
		<strong>{t}Opening hours{/t}</strong><br>
		{t}From dusk till dawn{/t}
	</address>
</div>

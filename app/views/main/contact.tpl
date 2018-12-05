<article>
	<header>
		<h1>{$page_title}</h1>
	</header>
	
	<section>
		<div class="row">
			<div class="col-sm-12">
				<p class="lead mb-5">
					{t}If you have any question, contact us through the following form. We will reply to you as soon as we can. Below the form you'll find other contact information.{/t}
				</p>
			</div>

			<div class="col-md-6">
				<div class="well">
					{render partial="shared/form"}
				</div>
			</div>

			<address class="col-md-6">
				<strong>{t team_name="ATK14_APPLICATION_NAME"|dump_constant}%1 Team{/t}</strong><br>
				Elm Street 1428<br>
				Springwood<br>
				Ohio<br>
				United States<br>
				<br>
				<strong>{t}Email{/t}:</strong><br>
				{"DEFAULT_EMAIL"|dump_constant}<br>
				<strong>{t}Phone{/t}:</strong><br>
				+1-541-754-3010
				<br>
				<strong>{t}Opening hours{/t}</strong><br>
				{t}From dusk till dawn{/t}
			</address>
		</div>
	</section>
</article>
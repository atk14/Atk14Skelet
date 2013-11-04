<h1>{$page_title}</h1>

{render partial="shared/form"}

<p>{a action="password_recoveries/create_new"}{t}Have you forgotten password?{/t}{/a}</p>

<p class="to_be_removed alert alert-info">{t}Please note that default administration access is admin/admin. Be a smart guy and change admin's password on production promptly.{/t}</p>

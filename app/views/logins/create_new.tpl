<h2>{$page_title}</h2>

{capture assign=button_text}{t}Sign in{/t}{/capture}
{render partial="shared/form" button_text=$button_text}

<p>
{a action="password_recoveries/create_new"}{t}Forgot password?{/t}{/a}
</p>

<p class="to_be_removed">{t}Please note that default administration access is admin/admin. Be a smart guy and change admin`s password on production promptly.{/t}</p>

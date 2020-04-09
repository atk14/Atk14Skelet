{dropdown_menu clearfix=false}
	{a action=detail id=$article namespace=""}{!"eye-open"|icon} {t}Visit public link{/t}{/a}
{/dropdown_menu}

<h1>{$page_title}</h1>

{render partial="edit_form"}

<hr>

{render partial="shared/iobjects" object=$article}

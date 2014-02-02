<h1>{$page_title}</h1>

<p>{t user=$user|h escape=no}At the moment no one knows the actual user's password because passwords are not stored in plain text form. In this form you can set a new password to the user <em>%1</em>{/t}</p>

{render partial="shared/form"}

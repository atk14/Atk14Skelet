{t}Name{/t}: {!$name}
{t}Email{/t}: {!$email}
{t}Signing up for newsletter{/t}: {$sign_up_for_newsletter|display_bool}
{t}URL{/t}: {$request->getUrl()}

{t}IP address{/t}: {!$request->getRemoteAddr()} ({!$request->getRemoteHostname()})
{t}Logged user:{/t} {!$logged_user|default:"-"}

{!$body}

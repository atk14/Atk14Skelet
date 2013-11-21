{t user_name=$user->getName() ip_address=$password_recovery->getRecoveredFromAddr() escape=no}Hello %1!

Your password was just updated in recovery mode.

The update has been executed upon a request from IP address %2{/t}

{render partial="shared/mailer/footer"}

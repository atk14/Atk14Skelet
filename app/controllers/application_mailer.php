<?php
/**
 * The application's mailer class.
 *
 * In DEVELOPMENT e-mails are not sent for real,
 * they are logged to the application's log instead.
 *
 * Within a controller one can send an email like this:
 *     $this->mailer->execute("notify_user_registration",$user);
 *
 * Be aware of the fact that $this->mailer is a member of Atk14MailerProxy,
 * so you can use it this way
 *     $this->mailer->notify_user_registration($user);
 */
class ApplicationMailer extends Atk14Mailer {

	/**
	 * A place for common configuration.
	 */
	function _before_filter(){
		$this->content_type = "text/plain";
		$this->content_charset = DEFAULT_CHARSET; // "UTF-8"
		$this->from = DEFAULT_EMAIL;
		// $this->bcc = "";
	}

	function notify_user_registration($user){
		$this->tpl_data["user"] = $user;
		$this->to = $user->getEmail();
		$this->subject = sprintf(_("Welcome to %s"),ATK14_APPLICATION_NAME);
		// body is rendered from app/views/mailer/notify_user_registration.tpl
	}

	function notify_password_recovery($password_recovery){
		$this->tpl_data["user"] = $user = $password_recovery->getUser();
		$this->tpl_data["password_recovery"] = $password_recovery;

		$this->to = $user->getEmail();
		$this->subject = _("Reset Your Password");
		// body is rendered from app/views/mailer/notify_password_recovery.tpl
	}

	function contact_message($email_address,$name,$message){
		$this->to = DEFAULT_EMAIL;
		$this->from = $email_address;
		$this->from_name = $name;
		$this->body = $message; // there is no template for body
		$this->subject = _("Message sent from contact page");
	}
}

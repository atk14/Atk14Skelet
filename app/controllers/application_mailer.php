<?php
/**
 * The application's mailer class.
 *
 * In DEVELOPMENT e-mails are not sent for real,
 * they are logged to the application's log instead.
 */
class ApplicationMailer extends Atk14Mailer {

	/**
	 * This is a test message.
	 * Within a controller run $this->mailer->execute("test_message"); 
	 */
	function test_message($params = array()){
		$this->to = DEFAULT_EMAIL;
		$this->subject = "Test message";
		$this->tpl_data["current_time"] = date("Y-m-d H:i:s");
	}

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
	}

	function notify_password_recovery($params){
		$this->tpl_data["user"] = $user = $params["password_recovery"]->getUser();
		$this->tpl_data["password_recovery"] = $params["password_recovery"];

		$this->to = $user->getEmail();
		$this->subject = _("Reset Your Password");
	}

	function contact_message($email_address,$name,$message){
		$this->to = DEFAULT_EMAIL;
		$this->from = $email_address;
		$this->from_name = $name;
		$this->body = $message;
		$this->subject = _("Message sent from contact page");
	}
}

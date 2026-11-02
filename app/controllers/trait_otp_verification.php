<?php
/**
 * Shared implementation of the "send_otp" and "authorize" steps used by multi-step (_walk)
 * controllers that verify ownership of an e-mail address via a one-time numeric code
 * (see OneTimePassword).
 *
 * Usage:
 *
 *	class FooController extends ApplicationController {
 *		use TraitOtpVerification;
 *
 *		function create_new__send_otp(){
 *			$object = $this->_get_object();
 *
 *			return $this->_send_otp([
 *				"skip" => $this->logged_user && $object->getUserId()===$this->logged_user->getId(),
 *				"purpose" => "foo",
 *				"object_key" => $object->getId(),
 *				"recipient" => $object->getEmail(),
 *				"subject" => $object, // passed as the first argument to notify_foo_otp($subject,$otp,$code)
 *			]);
 *		}
 *
 *		function create_new__authorize(){
 *			$object = $this->_get_object();
 *
 *			return $this->_authorize([
 *				"skip" => $this->logged_user && $object->getUserId()===$this->logged_user->getId(),
 *				"purpose" => "foo",
 *				"object_key" => $object->getId(),
 *				// on_success_closure can be omitted to just get ["one_time_password_id" => ...]
 *			]);
 *		}
 *	}
 */
trait TraitOtpVerification {

	/**
	 * See the option comments below for what can be passed in $options.
	 *
	 * "purpose" defaults to the controller's own class name, e.g. "FooBarRequestsController" -> "foo_bar_request"
	 * (see _defaultOtpPurpose()).
	 */
	function _send_otp($options){
		$options += [
			"skip" => false, // bypasses sending when truthy (e.g. an already logged in owner); the step becomes invisible to the user
			"purpose" => $this->_defaultOtpPurpose(), // scope of the OneTimePassword record, see OneTimePassword::CreateNewRecordFor()
			"object_key" => null, // scope of the OneTimePassword record, see OneTimePassword::CreateNewRecordFor()
			"recipient" => null, // e-mail address the code is being sent to
			"subject" => null, // business object (e.g. an Order or a Product) passed as the first argument to
			                    // notify_{$purpose}_otp($subject,$otp,$code) - not to be confused with the e-mail's
			                    // own subject line, which that mailer method sets on its own
			"resend_delay" => 60, // minimal number of seconds between two code resends
		];

		if($options["skip"]){
			return $this->_next_step([
				"sent" => false,
			]);
		}

		if($this->request->post()){
			$last_otp = OneTimePassword::FindFirst([
				"conditions" => "purpose=:purpose AND object_key=:object_key",
				"bind_ar" => [
					":purpose" => (string)$options["purpose"],
					":object_key" => (string)$options["object_key"],
				],
				"order_by" => "created_at DESC, id DESC",
			]);
			if($last_otp && (time() - strtotime($last_otp->getCreatedAt())) < $options["resend_delay"]){
				$this->form->set_error(_("Kód už byl odeslán, zkuste to prosím za chvíli znovu"));
				return;
			}

			$code = OneTimePassword::NewCode();
			$otp = OneTimePassword::CreateNewRecordFor($options["purpose"],$options["object_key"],$code,$options["recipient"]);

			$mailer_method = "notify_{$options['purpose']}_otp";
			$this->mailer->$mailer_method($options["subject"],$otp,$code);

			return [
				"sent" => true,
			];
		}
	}

	/**
	 * See the option comments below for what can be passed in $options.
	 *
	 * "purpose" defaults to the controller's own class name, same as in _send_otp() (see _defaultOtpPurpose())
	 * - it must match whatever purpose was used in the corresponding _send_otp() call.
	 *
	 * "on_success_closure" is always called as a plain function call, never through _next_step() - even the
	 * $skip branch may be reached through a POST meant for a different, later step, and a plain `return` forces
	 * a fresh request for it instead of risking validating that step's form against the current (unrelated) POST data.
	 * It's called as $closure($otp) - $otp is null when $skip was used, since no code was actually verified then.
	 */
	function _authorize($options){
		$options += [
			"skip" => false, // bypasses the code check when truthy (e.g. an already logged in owner); the step becomes invisible to the user
			"purpose" => $this->_defaultOtpPurpose(), // scope of the OneTimePassword record, see OneTimePassword::GetActiveInstanceFor()
			"object_key" => null, // scope of the OneTimePassword record, see OneTimePassword::GetActiveInstanceFor()
			"mark_as_used" => true, // whether a successfully verified $otp should be consumed via $otp->markAsUsed()
			"on_success_closure" => function($otp){ // function($otp){ return [...]; }, called once authorized (either via $skip or a valid code)
				return [
					"one_time_password_id" => $otp ? $otp->getId() : null,
				];
			},
		];

		if($options["skip"]){
			$fn = $options["on_success_closure"];
			return $fn(null);
		}

		if($this->request->post() && ($d = $this->form->validate($this->params))){
			if(InvalidPasswordAttempt::IsRemoteAddressBlocked($this->request->getRemoteAddr(),$release_time,["purpose" => $options["purpose"]])){
				$this->form->set_error(InvalidPasswordAttempt::BuildNextAttemptDelayMessage($release_time));
				return;
			}

			$code = $d["code"];
			if(!($otp = OneTimePassword::GetActiveInstanceFor($options["purpose"],$options["object_key"],$code))){
				InvalidPasswordAttempt::CreateNewRecord([
					"purpose" => $options["purpose"],
					"object_key" => (string)$options["object_key"],
				]);
				$this->form->set_error("code",_("Toto není platný kód"));
				return;
			}
			$options["mark_as_used"] && $otp->markAsUsed();

			$fn = $options["on_success_closure"];
			return $fn($otp);
		}
	}

	/**
	 *	"OrderWithdrawalRequestsController" -> "order_withdrawal_request"
	 */
	private function _defaultOtpPurpose(){
		return String4::ToObject(get_class($this))->gsub('/Controller$/','')->singularize()->underscore()->toString();
	}
}

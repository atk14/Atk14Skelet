<?php
class NewsletterSubscriptionsRouter extends Atk14Router {

	function setUp(){
		// Newsletter subscription
		$this->addRoute("/newsletter/sign-in/","en/newsletter_subscription_requests/create_new");
		$this->addRoute("/newsletter/prihlaseni/","cs/newsletter_subscription_requests/create_new");
		$this->addRoute("/newsletter/prihlasenie/","sk/newsletter_subscription_requests/create_new");

		$this->addRoute("/newsletter/sign-in/sent/","en/newsletter_subscription_requests/created");
		$this->addRoute("/newsletter/prihlaseni/odeslano/","cs/newsletter_subscription_requests/created");
		$this->addRoute("/newsletter/prihlasenie/odeslane/","sk/newsletter_subscription_requests/created");

		$this->addRoute("/newsletter/sign-in/confirm/<token>/","en/newsletter_subscription_requests/confirm");
		$this->addRoute("/newsletter/prihlaseni/potvrzeni/<token>/","cs/newsletter_subscription_requests/confirm");
		$this->addRoute("/newsletter/prihlasenie/potvrzenie/<token>/","sk/newsletter_subscription_requests/confirm");

		// Newsletter unsubscription
		$this->addRoute("/newsletter/sign-out/","en/newsletter_subscribe_deletion_requests/create_new");
		$this->addRoute("/newsletter/odhlaseni/","cs/newsletter_subscribe_deletion_requests/create_new");
		$this->addRoute("/newsletter/odhlasenie/","sk/newsletter_subscribe_deletion_requests/create_new");

		$this->addRoute("/newsletter/sign-out/sent/","en/newsletter_subscribe_deletion_requests/created");
		$this->addRoute("/newsletter/odhlaseni/odeslano/","cs/newsletter_subscribe_deletion_requests/created");
		$this->addRoute("/newsletter/odhlasenie/doslane/","sk/newsletter_subscribe_deletion_requests/created");

		$this->addRoute("/newsletter/sign-out/confirm/<token>/","en/newsletter_subscribers/destroy");
		$this->addRoute("/newsletter/odhlaseni/potvrzeni/<token>/","cs/newsletter_subscribers/destroy");
		$this->addRoute("/newsletter/odhlasenie/potvrzenie/<token>/","sk/newsletter_subscribers/destroy");

		$this->addRoute("/newsletter/sign-out/finished/","en/newsletter_subscribers/destroyed");
		$this->addRoute("/newsletter/odhlaseni/dokonceno/","cs/newsletter_subscribers/destroyed");
		$this->addRoute("/newsletter/odhlasenie/dokoncene/","sk/newsletter_subscribers/destroyed");
	}
}

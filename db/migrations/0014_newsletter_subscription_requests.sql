CREATE SEQUENCE seq_newsletter_subscription_requests;
CREATE TABLE newsletter_subscription_requests (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_newsletter_subscription_requests'),
	--
	vocative VARCHAR(255), -- Mr., Mrs., Miss.
	name VARCHAR(255),
	email VARCHAR(255) NOT NULL,
	language CHAR(2) NOT NULL,
	--
	confirmed BOOLEAN NOT NULL DEFAULT FALSE,
	confirmed_at TIMESTAMP,
	--
	cancelled BOOLEAN NOT NULL DEFAULT FALSE,
	cancelled_at TIMESTAMP,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_by_user_id INT,
	created_from_addr VARCHAR(255),
	created_from_hostname VARCHAR(255),
	created_from_user_agent VARCHAR(1000),
	--
	updated_at TIMESTAMP,
	updated_by_user_id INT,
	updated_from_addr VARCHAR(255),
	updated_from_hostname VARCHAR(255),
	updated_from_user_agent VARCHAR(1000),
	--
	CONSTRAINT chk_newslettersubscriptionrequests CHECK (NOT (confirmed AND cancelled)),
	CONSTRAINT chk_newslettersubscriptionrequests_confirmed CHECK ((NOT confirmed AND confirmed_at IS NULL) OR (confirmed AND confirmed_at IS NOT NULL)),
	CONSTRAINT chk_newslettersubscriptionrequests_cancelled CHECK ((NOT cancelled AND cancelled_at IS NULL) OR (cancelled AND cancelled_at IS NOT NULL)),
	CONSTRAINT fk_newslettersubscriptionrequests_cr_users FOREIGN KEY (created_by_user_id) REFERENCES users(id),
	CONSTRAINT fk_newslettersubscriptionrequests_upd_users FOREIGN KEY (updated_by_user_id) REFERENCES users(id)
);

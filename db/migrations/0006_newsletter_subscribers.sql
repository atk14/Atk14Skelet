CREATE SEQUENCE seq_newsletter_subscribers;
CREATE TABLE newsletter_subscribers (
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_newsletter_subscribers'),
	--
	vocative VARCHAR(255), -- Mr., Mrs., Miss.
	name VARCHAR(255),
	email VARCHAR(255) NOT NULL,
	--
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_from_addr VARCHAR(255),
	updated_at TIMESTAMP,
	updated_from_addr VARCHAR(255),
	--
	CONSTRAINT unq_newslettersubscribers UNIQUE (email)
);

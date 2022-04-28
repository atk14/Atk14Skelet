ALTER TABLE newsletter_subscribers ADD subscribed_at_url VARCHAR(1000);
ALTER TABLE newsletter_subscribers ADD created_from_hostname VARCHAR(255);
ALTER TABLE newsletter_subscribers ADD created_from_user_agent VARCHAR(1000);

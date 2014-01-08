CREATE SEQUENCE seq_news;
CREATE TABLE news(
	id INT PRIMARY KEY DEFAULT NEXTVAL('seq_news'),
	title VARCHAR(255),
	body TEXT,
	author_id INT NOT NULL,
	published_at TIMESTAMP NOT NULL DEFAULT NOW(),
	created_at TIMESTAMP NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMP,
	CONSTRAINT fk_news_users FOREIGN KEY (author_id) REFERENCES users ON DELETE SET NULL
);


INSERT INTO news (title,published_at,author_id,body) VALUES('Happy Millenium','2000-01-01',1,TRIM('
We wish you Happy Millenium!

May all ATK14 developers are doing their job in peace.

ATK14 Development Team
'));

INSERT INTO news (title,published_at,author_id,body) VALUES('Welcome to ATK14 Skelet','2013-04-12',1,TRIM('

We are happy to announce the availability of ATK14 Skelet. This is a carefully selected & minimalistic set of functionality you may require at the start of your next web project.

You can find more informations on the following addresses

  * [AKT14 Skelet on Github](https://github.com/yarri/Atk14Skelet)
  * [Atk14 Site](http://www.atk14.net/)
  * [Atk14 Book](http://book.atk14.net/)

Remember! Your projects shall run on [ATK14 Framework](http://www.atk14.net/), for now and ever after!

'));

ATK14 Skelet
============

[![Build Status](https://travis-ci.org/atk14/Atk14Skelet.svg?branch=develop)](https://travis-ci.com/atk14/Atk14Skelet)

Meaningful skeleton for just kicked up ATK14 application.

Check out <http://skelet.atk14.net/> to see the skelet running.

What the Skeleton contains
--------------------------

* User registrations
* Password recoveries
* Articles (news)
* Tags
* A simple administration
* Front-end tooling [Gulp](https://github.com/gulpjs/gulp) and [BrowserSync](https://github.com/BrowserSync/browser-sync)

Installation
------------

```bash
git clone https://github.com/atk14/Atk14Skelet.git
cd Atk14Skelet
git submodule init
git submodule update
composer install
./scripts/create_database
./scripts/migrate
```
If you are experiencing a trouble make sure that all requirements are met: <http://book.atk14.net/czech/installation%3Arequirements/>

Starting the skeleton
---------------------

Start the development server

```bash
./scripts/server
```

and you may find the running skeleton on http://localhost:8000/

Installing the skeleton as a virtual host on Apache web server
--------------------------------------------------------------

This is optional step. If you have Apache installed, you may want to install the application to a virtual server.

```bash
./scripts/virtual_host_configuration -f
sudo service apache2 reload
chmod 777 tmp log
```

Visit <http://atk14skelet.localhost/>. Is it running? Great!

If you have a trouble run the following command and follow instructions.

```bash
./scripts/virtual_host_configuration
```

Front-end Assets Installation
-----------------------------
#### Install dependencies.
With [Node.js](http://nodejs.org) and npm installed, run the following one liner from the root of your Skelet application:
```bash
$ npm install -g gulp && npm install
```

This will install all the tools you will need to serve and build your front-end assets.

### Run initial build
Run initial Gulp build process for presentation and admininstration.
```bash
$ gulp && gulp admin
```

### Serve / watch
```bash
$ gulp serve
```

This outputs an IP address you can use to locally test and another that can be used on devices connected to your network.

### You're done! Happy skeleting!

Don't forget to list your new project on http://www.atk14sites.net/

[//]: # ( vim: set ts=2 et: )

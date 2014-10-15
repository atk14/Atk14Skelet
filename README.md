ATK14 Skelet
============

Meaningful skeleton for a new installed ATK14 application.

Check out <http://atk14skelet.atk14.net/> to see the skelet running.

Installation
------------

```bash
git clone https://github.com/yarri/Atk14Skelet.git
cd Atk14Skelet
git submodule init
git submodule update
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

and you may found the running skeleton on http://localhost:8000/

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
```bash
# Node Version manager
wget -q -O - https://raw.github.com/creationix/nvm/master/install.sh | sh
echo -e "\n. ~/.nvm/nvm.sh" >> ~/.bashrc && . ~/.nvm/nvm.sh
# Node.js
nvm install 0.10
# Bower
npm install -g bower
# Grunt
npm install -g grunt-cli
```
#### Install skelet front-end dependencies via Bower.
```bash
bower install
```
#### Install build dependencies.
```bash
npm install
```
#### Build.
```bash
grunt dist
```
### You're done! Happy skeleting!
From now on you only need to run `grunt dist` when you need to concatenate and minify JS and CSS for production.
You can also run lint and other tasks via Grunt. To see available tasks run `grunt --help`.

Don't forget to list your new project on http://www.atk14sites.net/

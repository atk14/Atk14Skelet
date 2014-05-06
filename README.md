ATK14 Skelet
============

Meaningful skelet for a new installed ATK14 application.

Check out <http://atk14skelet.atk14.net/> to see the skelet running.

Installation
------------

First make sure that all requirements are met: <http://book.atk14.net/czech/installation%3Arequirements/>

```bash
# go to your projects directory
cd ~/projects/
git clone https://github.com/yarri/Atk14Skelet.git atk14skelet
cd atk14skelet
chmod 777 log tmp
git submodule init
git submodule update
./scripts/create_database
./scripts/migrate

./scripts/server
```

You should find running application on http://localhost:8000/

If you have Apache installed, you may want to install the application to a virtual server.
Follow instructions from the following command.

```bash
./scripts/virtual_host_configuration
```

Front-end Assets Installation
-----------------------------
#### Install dependencies.
```bash
# Node Version manager
curl https://raw.github.com/creationix/nvm/master/install.sh | sh
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

<!-- vim: set et: -->

Atk14Skelet
===========

Meaningful skelet for a new installed ATK14 application.

Check out <http://atk14skelet.atk14.net/> to see the skelet running.

Installation
------------

First make sure that all requirements are met: <http://book.atk14.net/czech/installation%3Arequirements/>

    # go to your projects directory
    cd ~/projects/

    mkdir atk14skelet
    cd atk14skelet
    git clone https://github.com/yarri/Atk14Skelet.git ./
    chmod 777 log tmp
    git submodule init
    git submodule update
    ./scripts/virtual_host_configuration -f
    ./scripts/create_database
    ./scripts/initialize_database
    ./scripts/migrate

You shall found running ATK14 Skelet on http://atk14skelet.localhost/

Front-end Assets Installation
-----------------------------

    curl https://raw.github.com/creationix/nvm/master/install.sh | sh
    . ~/.nvm/nvm.sh
    nvm install 0.10
    npm install -g bower
    bower install
    cd public/assets/vendor/bootstrap
    npm install
    make test
    make bootstrap

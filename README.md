Atk14Skelet
===========

Meaningful skelet for a new installed ATK14 application.

Check out <http://atk14skelet.atk14.net/> to see the skelet running.

Installation
------------

    mkdir atk14skelet
    cd atk14skelet
    git clone git@github.com:yarri/Atk14Skelet.git ./
    chmod 777 log tmp
    git submodule init
    git submodule update
    ./scripts/virtual_host_configuration -f
    ./scripts/create_database
    ./scripts/initialize_database
    ./scripts/migrate

You shall found running ATK14 Skelet on http://atk14skelet.localhost/

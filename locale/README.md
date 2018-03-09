Localization
============

Localization files are held in this directory. Localization is provided by Gettext.

The output file messages.mo is compiled from concatenation of 3 files:

- #### application.po
  messages from your application are extracted from files located in folders ./app/, ./lib/

- #### application_admin.po
  messages from your admin are extracted from files located in folders ./app/controllers/admin/, ./app/forms/admin/, ./app/views/admin/

- #### atk14.po
  messages from ATK14 Framework

- #### vendor.po
  messages from 3rd party components installed using Composer into ./vendor/ directory

Localization files maintenance
------------------------------

Usually you may want to run all the commands in the given order.

Synchronizes atk14.po files with the currently installed version of ATK14 Framework.

    make sync-atk14

Creates files files_php and files_smarty_templates with a list of files to be searched for possible gettext phrases.

    make files-list

Creates the messages.po file with all phrases in the current folder.

    make pot

Merges just created messages.po with en_US/LC_MESSAGES/messages.po, cs_CZ/LC_MESSAGES/messages.po and so on.

    make merge

Now it is time to translate all new messages in every language, e.g.:

    poedit cs_CZ/LC_MESSAGES/application.po
    poedit cs_CZ/LC_MESSAGES/application_admin.po
    poedit cs_CZ/LC_MESSAGES/atk14.po
    poedit cs_CZ/LC_MESSAGES/vendor.po

Compiles application.po, atk14.po and vendor.po files into the single messages.mo file for every language.

    make compile

Wipes out all the working files.

    make clear



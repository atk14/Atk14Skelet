# SUBDIRS is a merge of folders and supported locales
SUBDIRS = $(shell { ../scripts/_get_supported_locales && ls | cat; } | egrep '^[a-z]{2}_[A-Z]{2,4}' | sort | uniq | tr '\n' ' ' )

sync-atk14: $(addprefix sync-atk14-,$(SUBDIRS))

files-list:
	> files_php
	> files_vendor
	> files_smarty_templates
	find ../ -type f -name '*.php' -o -name '*.inc' | egrep -v '^\.\.\/(tmp|atk14|vendor)\/' | grep -v -F ".svn" | sort >> files_php
	find ../vendor/ -type f -name '*.php' -o -name '*.inc' | grep -v -F ".svn" | sort >> files_vendor
	find ../app/ -type f -name "*.tpl" | grep -v -F ".svn" | sort >> files_smarty_templates

gettext: files-list
	# Creating application.pot
	@cat files_smarty_templates|xargs ../scripts/tsmarty2c.php >> smarty.c
	@-xgettext --no-location -L c --from-code=UTF-8 smarty.c --output=- > application.pot
	@-xgettext --join-existing -L PHP --from-code=UTF-8 -f files_php --output=application.pot
	@sed --in-place application.pot --expression='s/Content-Type: text\/plain; charset=CHARSET/Content-Type: text\/plain; charset=UTF-8/'
	# Creating vendor.pot
	@-xgettext -L PHP --from-code=UTF-8 -f files_vendor --output=vendor.pot
	@sed --in-place vendor.pot --expression='s/Content-Type: text\/plain; charset=CHARSET/Content-Type: text\/plain; charset=UTF-8/'

merge: $(addprefix merge-,$(SUBDIRS))

compile: $(addprefix compile-,$(SUBDIRS))

clear:
	@rm -f files_php files_vendor files_smarty_templates
	@rm -f smarty.c
	@rm -f application.pot vendor.pot
	@rm -f */*/messages.po

define lang-def
sync-atk14-$(1):
	@echo "----> Syncing ATK14 messages in $(1)"
	mkdir -p $(1)/LC_MESSAGES
	if [ -f ../atk14/locale/$(1)/LC_MESSAGES/atk14.po ]; then cp ../atk14/locale/$(1)/LC_MESSAGES/atk14.po $(1)/LC_MESSAGES/atk14.po.dist; else cp ../atk14/locale/atk14.pot $(1)/LC_MESSAGES/atk14.po.dist; fi
	if [ ! -f $(1)/LC_MESSAGES/atk14.po ]; then cp $(1)/LC_MESSAGES/atk14.po.dist $(1)/LC_MESSAGES/atk14.po; fi
	mv $(1)/LC_MESSAGES/atk14.po $(1)/LC_MESSAGES/atk14.po.orig
	msgmerge $(1)/LC_MESSAGES/atk14.po.orig $(1)/LC_MESSAGES/atk14.po.dist --no-fuzzy-matching --output-file=$(1)/LC_MESSAGES/atk14.po
	rm $(1)/LC_MESSAGES/atk14.po.orig $(1)/LC_MESSAGES/atk14.po.dist

merge-$(1):
	@echo "----> Merging in $(1)"
	# Merging application.pot
	mkdir -p $(1)/LC_MESSAGES
	if [ ! -f  $(1)/LC_MESSAGES/application.po ]; then cp application.pot $(1)/LC_MESSAGES/application.po; fi
	-mv $(1)/LC_MESSAGES/application.po $(1)/LC_MESSAGES/application.po.orig;
	-msgmerge $(1)/LC_MESSAGES/application.po.orig application.pot --no-fuzzy-matching --output-file=$(1)/LC_MESSAGES/application.po
	-rm $(1)/LC_MESSAGES/application.po.orig
	# Merging vendor.po
	if [ ! -f $(1)/LC_MESSAGES/vendor.po ]; then cp vendor.pot $(1)/LC_MESSAGES/vendor.po; fi
	mv $(1)/LC_MESSAGES/vendor.po $(1)/LC_MESSAGES/vendor.po.orig
	msgmerge $(1)/LC_MESSAGES/vendor.po.orig vendor.pot --no-fuzzy-matching --output-file=$(1)/LC_MESSAGES/vendor.po
	rm $(1)/LC_MESSAGES/vendor.po.orig

compile-$(1):
	@echo "----> Compiling in $(1)"
	cat $(1)/LC_MESSAGES/application.po | egrep -v '^#~' > $(1)/LC_MESSAGES/join_a.po
	cat $(1)/LC_MESSAGES/atk14.po | egrep -v '^#~' > $(1)/LC_MESSAGES/join_b.po
	cat $(1)/LC_MESSAGES/vendor.po | egrep -v '^#~' > $(1)/LC_MESSAGES/join_c.po
	msgcat --use-first $(1)/LC_MESSAGES/join_*.po > $(1)/LC_MESSAGES/messages.po
	msgfmt $(1)/LC_MESSAGES/messages.po --output-file=$(1)/LC_MESSAGES/messages.mo
	rm $(1)/LC_MESSAGES/join_*

endef

$(foreach lang, $(SUBDIRS),$(eval $(call lang-def,$(lang))))


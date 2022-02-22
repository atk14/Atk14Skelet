ALTER TABLE link_list_items ADD code VARCHAR(255);

ALTER TABLE link_list_items ADD CONSTRAINT unq_linklistitems_code UNIQUE (code);

ALTER TABLE link_lists ADD rank INTEGER NOT NULL DEFAULT 999;

ALTER TABLE link_list_items ADD visible BOOLEAN NOT NULL DEFAULT TRUE;

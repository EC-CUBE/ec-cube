CREATE TABLE dtb_session (
    sess_id text NOT NULL,
    sess_data text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL
) TYPE=InnoDB;
CREATE TABLE dtb_session (
    sess_id varchar(50) NOT NULL,
    sess_data text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (sess_id)
) TYPE=InnoDB;
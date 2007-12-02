CREATE TABLE dtb_module (
    module_id int NOT NULL UNIQUE,
    module_code text NOT NULL,
    module_name text NOT NULL,
    sub_data text,
    auto_update_flg smallint NOT NULL DEFAULT 0,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date datetime NOT NULL ,
    update_date datetime NOT NULL
) TYPE=InnoDB;

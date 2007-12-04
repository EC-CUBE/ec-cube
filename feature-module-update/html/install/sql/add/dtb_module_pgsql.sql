CREATE TABLE dtb_module (
    module_id int NOT NULL UNIQUE,
    module_code text NOT NULL,
    module_name text NOT NULL,
    sub_data text,
    auto_update_flg int2 NOT NULL DEFAULT 0,
    del_flg int2 NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL DEFAULT NOW(),
    update_date timestamp NOT NULL
);


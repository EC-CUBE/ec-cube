CREATE TABLE dtb_site_control (
    control_id int auto_increment NOT NULL,
    control_title varchar(255) NOT NULL DEFAULT '',
    control_text text NOT NULL DEFAULT '',
    control_flg int NOT NULL DEFAULT 2,
    del_flg int NOT NULL DEFAULT 0,
    memo text NOT NULL DEFAULT '',
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY  (control_id)
) TYPE=InnoDB;

INSERT INTO dtb_site_control (control_title, control_text, create_date, update_date) VALUES('トラックバック機能', 'トラックバック機能を使用するかどうかを決定します。', NOW(), NOW());
INSERT INTO dtb_site_control (control_title, control_text, create_date, update_date) VALUES('アフィリエイト機能', 'アフィリエイト機能を使用するかどうかを決定します。', NOW(), NOW());

CREATE TABLE dtb_mobile_kara_mail (
    kara_mail_id serial PRIMARY KEY,
    session_id text NOT NULL,
    token text NOT NULL,
    next_url text NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    email text,
    receive_date timestamp
);

CREATE INDEX dtb_mobile_kara_mail_token_key ON dtb_mobile_kara_mail (token(64));
CREATE INDEX dtb_mobile_kara_mail_create_date_key ON dtb_mobile_kara_mail (create_date);
CREATE INDEX dtb_mobile_kara_mail_receive_date_key ON dtb_mobile_kara_mail (receive_date);

INSERT INTO dtb_table_comment (table_name, description) VALUES ('dtb_mobile_kara_mail', '空メール管理');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'kara_mail_id', '空メール管理ID');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'session_id', 'セッションID');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'token', 'トークン');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'next_url', '次ページURL');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'email', 'メールアドレス');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'receive_date', '受信日時');

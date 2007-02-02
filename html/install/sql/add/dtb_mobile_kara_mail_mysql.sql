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

INSERT INTO dtb_table_comment (table_name, description) VALUES ('dtb_mobile_kara_mail', '���᡼�����');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'kara_mail_id', '���᡼�����ID');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'session_id', '���å����ID');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'token', '�ȡ�����');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'next_url', '���ڡ���URL');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'create_date', '��������');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'email', '�᡼�륢�ɥ쥹');
INSERT INTO dtb_table_comment (table_name, column_name, description) VALUES ('dtb_mobile_kara_mail', 'receive_date', '��������');

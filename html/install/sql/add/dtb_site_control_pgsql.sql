CREATE TABLE dtb_site_control (
    control_id serial primary key NOT NULL,
    control_title text ,
    control_text text ,
    control_flg int2 NOT NULL DEFAULT 2,
    del_flg int2 NOT NULL DEFAULT 0,
    memo text ,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now()
);

INSERT INTO dtb_site_control (control_title, control_text) VALUES('�ȥ�å��Хå���ǽ', '�ȥ�å��Хå���ǽ����Ѥ��뤫�ɤ�������ꤷ�ޤ���');

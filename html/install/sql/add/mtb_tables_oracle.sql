CREATE TABLE mtb_permission (
    id varchar2(128),
    name varchar2(4000),
    disp_order number(4) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_disable_logout (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_authority (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_work (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_disp (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_class (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_sdisp_order (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_status (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_status_image (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_allowed_tag (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_page_max (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_magazine_type (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_mail_magazine_type (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_recommend (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_taxrule (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_mail_template (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_mail_tpl_path (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_job (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_reminder (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_sex (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_page_rows (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_mail_type (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_order_status (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_product_status_color (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_order_status_color (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_wday (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_delivery_date (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_product_list_max (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_convenience (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_conveni_message (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_db (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_target (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_review_deny_url (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_track_back_status (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_site_control_track_back (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_site_control_affiliate (
    id number(4),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    PRIMARY KEY (id)
);

CREATE TABLE mtb_constants (
    id varchar(64),
    name varchar2(4000),
    disp_order number(4) NOT NULL ,
    remarks varchar2(4000),
    PRIMARY KEY (id)
);

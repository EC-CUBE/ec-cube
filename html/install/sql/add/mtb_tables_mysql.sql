CREATE TABLE mtb_permission (
    id text,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_disable_logout (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_authority (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_work (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_disp (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_class (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_srank (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_status (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_status_image (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_allowed_tag (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_page_max (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_magazine_type (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_magazine_type (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_recommend (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_taxrule (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_template (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_tpl_path (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_job (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_reminder (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_sex (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_page_rows (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_type (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_order_status (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_product_status_color (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_order_status_color (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_wday (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_delivery_date (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_product_list_max (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_convenience (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_conveni_message (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_db (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_target (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_review_deny_url (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_track_back_status (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_site_control_track_back (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_site_control_affiliate (
    id int2,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_constants (
    id text,
    name text,
    rank int2 NOT NULL DEFAULT 0,
    remarks text,
    PRIMARY KEY (id)
) TYPE=InnoDB;

create table dtb_module_update_logs(
    log_id int  NOT NULL,
    module_id int NOT NULL,
    buckup_path text,
    error_flg smallint DEFAULT 0,
    error text,
    ok text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (log_id)
) TYPE=InnoDB;

CREATE TABLE dtb_ownersstore_settings (
    public_key text
) TYPE=InnoDB;

CREATE TABLE dtb_kiyaku (
    kiyaku_id int  NOT NULL,
    kiyaku_title text NOT NULL,
    kiyaku_text text NOT NULL,
    rank int NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (kiyaku_id)
) TYPE=InnoDB;

CREATE TABLE dtb_holiday (
    holiday_id int  NOT NULL,
    title text NOT NULL,
    month smallint NOT NULL,
    day smallint NOT NULL,
    rank int NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (holiday_id)
) TYPE=InnoDB;

CREATE TABLE mtb_zip (
    code text,
    old_zipcode text,
    zipcode text,
    state_kana text,
    city_kana text,
    town_kana text,
    state text,
    city text,
    town text,
    flg1 text,
    flg2 text,
    flg3 text,
    flg4 text,
    flg5 text,
    flg6 text
) TYPE=InnoDB;

CREATE TABLE dtb_bat_order_daily_age (
    order_count numeric NOT NULL DEFAULT 0,
    total numeric NOT NULL DEFAULT 0,
    total_average numeric NOT NULL DEFAULT 0,
    start_age smallint,
    end_age smallint,
    member smallint,
    order_date datetime,
    create_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_update (
    module_id int NOT NULL,
    module_name text NOT NULL,
    now_version text,
    latest_version text NOT NULL,
    module_explain text,
    main_php text NOT NULL,
    extern_php text NOT NULL,
    install_sql text,
    uninstall_sql text,
    other_files text,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date datetime NOT NULL,
    update_date datetime,
    release_date datetime NOT NULL,
    PRIMARY KEY (module_id)
) TYPE=InnoDB;

CREATE TABLE dtb_baseinfo (
    company_name text,
    company_kana text,
    zip01 text,
    zip02 text,
    pref smallint,
    addr01 text,
    addr02 text,
    tel01 text,
    tel02 text,
    tel03 text,
    fax01 text,
    fax02 text,
    fax03 text,
    business_hour text,
    law_company text,
    law_manager text,
    law_zip01 text,
    law_zip02 text,
    law_pref smallint,
    law_addr01 text,
    law_addr02 text,
    law_tel01 text,
    law_tel02 text,
    law_tel03 text,
    law_fax01 text,
    law_fax02 text,
    law_fax03 text,
    law_email text,
    law_url text,
    law_term01 text,
    law_term02 text,
    law_term03 text,
    law_term04 text,
    law_term05 text,
    law_term06 text,
    law_term07 text,
    law_term08 text,
    law_term09 text,
    law_term10 text,
    tax numeric NOT NULL DEFAULT 5,
    tax_rule smallint NOT NULL DEFAULT 1,
    email01 text,
    email02 text,
    email03 text,
    email04 text,
    email05 text,
    free_rule numeric,
    shop_name text,
    shop_kana text,
    shop_name_eng text,
    point_rate numeric NOT NULL DEFAULT 0,
    welcome_point numeric NOT NULL DEFAULT 0,
    update_date datetime,
    top_tpl text,
    product_tpl text,
    detail_tpl text,
    mypage_tpl text,
    good_traded text,
    message text,
    regular_holiday_ids text,
    latitude text,
    longitude text,
    downloadable_days numeric DEFAULT 30,
    downloadable_days_unlimited smallint
) TYPE=InnoDB;

CREATE TABLE dtb_deliv (
    deliv_id int  NOT NULL,
    name text,
    service_name text,
    confirm_url text,
    rank int,
    status smallint NOT NULL DEFAULT 1,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    PRIMARY KEY (deliv_id)
) TYPE=InnoDB;

CREATE TABLE dtb_delivtime (
    deliv_id int NOT NULL,
    time_id int NOT NULL,
    deliv_time text NOT NULL,
    PRIMARY KEY (deliv_id, time_id)
) TYPE=InnoDB;

CREATE TABLE dtb_delivfee (
    deliv_id int NOT NULL,
    fee_id int  NOT NULL,
    fee text NOT NULL,
    pref smallint,
    PRIMARY KEY (fee_id)
) TYPE=InnoDB;

CREATE TABLE dtb_payment (
    payment_id int  NOT NULL,
    payment_method text,
    charge numeric,
    rule numeric,
    deliv_id int DEFAULT 0,
    rank int,
    note text,
    fix smallint,
    status smallint NOT NULL DEFAULT 1,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    payment_image text,
    upper_rule numeric,
    charge_flg smallint DEFAULT 1,
    rule_min numeric,
    upper_rule_max numeric,
    module_id int,
    module_path text,
    memo01 text,
    memo02 text,
    memo03 text,
    memo04 text,
    memo05 text,
    memo06 text,
    memo07 text,
    memo08 text,
    memo09 text,
    memo10 text,
    PRIMARY KEY (payment_id)
) TYPE=InnoDB;

CREATE TABLE dtb_mailtemplate (
    template_id int NOT NULL,
    subject text,
    header text,
    footer text,
    creator_id int NOT NULL,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_send_history (
    send_id int  NOT NULL,
    mail_method smallint,
    subject text,
    body text,
    send_count int,
    complete_count int NOT NULL DEFAULT 0,
    start_date datetime,
    end_date datetime,
    search_data text,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (send_id)
) TYPE=InnoDB;

CREATE TABLE dtb_send_customer (
    customer_id int,
    send_id int NOT NULL,
    email text,
    name text,
    send_flag smallint,
    PRIMARY KEY (send_id, customer_id)
) TYPE=InnoDB;

CREATE TABLE dtb_products (
    product_id int  NOT NULL,
    name text NOT NULL,
    maker_id int,
    rank int,
    status smallint NOT NULL DEFAULT 2,
    comment1 text,
    comment2 text,
    comment3 mediumtext,
    comment4 text,
    comment5 text,
    comment6 text,
    note text,
    main_list_comment text,
    main_list_image text,
    main_comment mediumtext,
    main_image text,
    main_large_image text,
    sub_title1 text,
    sub_comment1 mediumtext,
    sub_image1 text,
    sub_large_image1 text,
    sub_title2 text,
    sub_comment2 mediumtext,
    sub_image2 text,
    sub_large_image2 text,
    sub_title3 text,
    sub_comment3 mediumtext,
    sub_image3 text,
    sub_large_image3 text,
    sub_title4 text,
    sub_comment4 mediumtext,
    sub_image4 text,
    sub_large_image4 text,
    sub_title5 text,
    sub_comment5 mediumtext,
    sub_image5 text,
    sub_large_image5 text,
    sub_title6 text,
    sub_comment6 mediumtext,
    sub_image6 text,
    sub_large_image6 text,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    deliv_date_id int,
    PRIMARY KEY (product_id)
) TYPE=InnoDB;

CREATE TABLE dtb_products_class (
    product_class_id int  NOT NULL,
    product_id int NOT NULL,
    class_combination_id int,
    product_type_id smallint NOT NULL,
    product_code text,
    stock numeric,
    stock_unlimited smallint NOT NULL DEFAULT 0,
    sale_limit numeric,
    price01 numeric,
    price02 numeric,
    deliv_fee numeric,
    point_rate numeric,
    status smallint,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    down_filename text,
    down_realfilename text,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (product_class_id)
) TYPE=InnoDB;

CREATE TABLE dtb_class (
    class_id int  NOT NULL,
    name text,
    status smallint,
    rank int,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (class_id)
) TYPE=InnoDB;

CREATE TABLE dtb_classcategory (
    classcategory_id int  NOT NULL,
    name text,
    class_id int NOT NULL,
    status smallint,
    rank int,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (classcategory_id)
) TYPE=InnoDB;

CREATE TABLE dtb_class_combination (
    class_combination_id int NOT NULL,
    parent_class_combination_id int,
    classcategory_id int NOT NULL,
    level int,
    PRIMARY KEY(class_combination_id)
) TYPE=InnoDB;

CREATE TABLE dtb_category (
    category_id int  NOT NULL,
    category_name text,
    parent_category_id int NOT NULL DEFAULT 0,
    level int NOT NULL,
    rank int,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (category_id)
) TYPE=InnoDB;

CREATE TABLE dtb_product_categories (
    product_id int NOT NULL,
    category_id int NOT NULL,
    rank int NOT NULL,
    PRIMARY KEY(product_id, category_id)
) TYPE=InnoDB;

CREATE TABLE dtb_product_status (
    product_status_id smallint NOT NULL,
    product_id int NOT NULL,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY(product_status_id, product_id)
) TYPE=InnoDB;

CREATE TABLE dtb_bat_order_daily (
    total_order numeric NOT NULL DEFAULT 0,
    member numeric NOT NULL DEFAULT 0,
    nonmember numeric NOT NULL DEFAULT 0,
    men numeric NOT NULL DEFAULT 0,
    women numeric NOT NULL DEFAULT 0,
    men_member numeric NOT NULL DEFAULT 0,
    men_nonmember numeric NOT NULL DEFAULT 0,
    women_member numeric NOT NULL DEFAULT 0,
    women_nonmember numeric NOT NULL DEFAULT 0,
    total numeric NOT NULL DEFAULT 0,
    total_average numeric NOT NULL DEFAULT 0,
    order_date datetime NOT NULL,
    create_date datetime NOT NULL,
    year smallint NOT NULL,
    month smallint NOT NULL,
    day smallint NOT NULL,
    wday smallint NOT NULL,
    key_day text NOT NULL,
    key_month text NOT NULL,
    key_year text NOT NULL,
    key_wday text NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_bat_order_daily_hour (
    total_order numeric NOT NULL DEFAULT 0,
    member numeric NOT NULL DEFAULT 0,
    nonmember numeric NOT NULL DEFAULT 0,
    men numeric NOT NULL DEFAULT 0,
    women numeric NOT NULL DEFAULT 0,
    men_member numeric NOT NULL DEFAULT 0,
    men_nonmember numeric NOT NULL DEFAULT 0,
    women_member numeric NOT NULL DEFAULT 0,
    women_nonmember numeric NOT NULL DEFAULT 0,
    total numeric NOT NULL DEFAULT 0,
    total_average numeric NOT NULL DEFAULT 0,
    hour smallint NOT NULL DEFAULT 0,
    order_date datetime,
    create_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_recommend_products (
    product_id int NOT NULL,
    recommend_product_id int NOT NULL,
    rank int NOT NULL,
    comment text,
    status smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_review (
    review_id int  NOT NULL,
    product_id int NOT NULL,
    reviewer_name text NOT NULL,
    reviewer_url text,
    sex smallint,
    customer_id int,
    recommend_level smallint NOT NULL,
    title text NOT NULL,
    comment text NOT NULL,
    status smallint DEFAULT 2,
    creator_id int NOT NULL,
    create_date datetime,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (review_id)
) TYPE=InnoDB;

CREATE TABLE dtb_customer_reading (
    reading_product_id int NOT NULL,
    customer_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_customer_favorite_products (
    customer_id int NOT NULL,
    product_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_category_count (
    category_id int NOT NULL,
    product_count int NOT NULL,
    create_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_category_total_count (
    category_id int NOT NULL,
    product_count int,
    create_date datetime NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_news (
    news_id int  NOT NULL,
    news_date datetime,
    rank int,
    news_title text NOT NULL,
    news_comment text,
    news_url text,
    news_select smallint NOT NULL DEFAULT 0,
    link_method text,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (news_id)
) TYPE=InnoDB;

CREATE TABLE dtb_best_products (
    best_id int  NOT NULL,
    category_id int NOT NULL,
    rank int NOT NULL DEFAULT 0,
    product_id int NOT NULL,
    title text,
    comment text,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (best_id)
) TYPE=InnoDB;

CREATE TABLE dtb_mail_history (
    send_id int  NOT NULL,
    order_id int NOT NULL,
    send_date datetime,
    template_id int,
    creator_id int NOT NULL,
    subject text,
    mail_body text,
    PRIMARY KEY (send_id)
) TYPE=InnoDB;

CREATE TABLE dtb_customer (
    customer_id int  NOT NULL,
    name01 text NOT NULL,
    name02 text NOT NULL,
    kana01 text NOT NULL,
    kana02 text NOT NULL,
    zip01 text,
    zip02 text,
    pref smallint,
    addr01 text,
    addr02 text,
    email text NOT NULL,
    email_mobile text,
    tel01 text,
    tel02 text,
    tel03 text,
    fax01 text,
    fax02 text,
    fax03 text,
    sex smallint,
    job smallint,
    birth datetime,
    password text,
    reminder smallint,
    reminder_answer text,
    secret_key varchar(50) NOT NULL UNIQUE,
    first_buy_date datetime,
    last_buy_date datetime,
    buy_times numeric DEFAULT 0,
    buy_total numeric DEFAULT 0,
    point numeric DEFAULT 0,
    note text,
    status smallint NOT NULL DEFAULT 1,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    cell01 text,
    cell02 text,
    cell03 text,
    mobile_phone_id text,
    mailmaga_flg smallint,
    PRIMARY KEY (customer_id)
) TYPE=InnoDB;

CREATE TABLE dtb_order (
    order_id int  NOT NULL,
    order_temp_id text,
    customer_id int NOT NULL,
    message text,
    order_name01 text,
    order_name02 text,
    order_kana01 text,
    order_kana02 text,
    order_email text,
    order_tel01 text,
    order_tel02 text,
    order_tel03 text,
    order_fax01 text,
    order_fax02 text,
    order_fax03 text,
    order_zip01 text,
    order_zip02 text,
    order_pref smallint,
    order_addr01 text,
    order_addr02 text,
    order_sex smallint,
    order_birth datetime,
    order_job int,
    deliv_name01 text,
    deliv_name02 text,
    deliv_kana01 text,
    deliv_kana02 text,
    deliv_tel01 text,
    deliv_tel02 text,
    deliv_tel03 text,
    deliv_fax01 text,
    deliv_fax02 text,
    deliv_fax03 text,
    deliv_zip01 text,
    deliv_zip02 text,
    deliv_pref smallint,
    deliv_addr01 text,
    deliv_addr02 text,
    subtotal numeric,
    discount numeric,
    deliv_fee numeric,
    charge numeric,
    use_point numeric,
    add_point numeric,
    birth_point numeric DEFAULT 0,
    tax numeric,
    total numeric,
    payment_total numeric,
    payment_id int,
    payment_method text,
    deliv_id int,
    deliv_time_id int,
    deliv_time text,
    deliv_no text,
    note text,
    status smallint,
    create_date datetime NOT NULL,
    loan_result text,
    credit_result text,
    credit_msg text,
    update_date datetime,
    commit_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text,
    memo01 text,
    memo02 text,
    memo03 text,
    memo04 text,
    memo05 text,
    memo06 text,
    memo07 text,
    memo08 text,
    memo09 text,
    memo10 text,
    payment_date timestamp,
    PRIMARY KEY (order_id)
) TYPE=InnoDB;

CREATE TABLE dtb_order_temp (
    order_temp_id text NOT NULL,
    customer_id int NOT NULL,
    message text,
    order_name01 text,
    order_name02 text,
    order_kana01 text,
    order_kana02 text,
    order_email text,
    order_tel01 text,
    order_tel02 text,
    order_tel03 text,
    order_fax01 text,
    order_fax02 text,
    order_fax03 text,
    order_zip01 text,
    order_zip02 text,
    order_pref smallint,
    order_addr01 text,
    order_addr02 text,
    order_sex smallint,
    order_birth datetime,
    order_job int,
    deliv_name01 text,
    deliv_name02 text,
    deliv_kana01 text,
    deliv_kana02 text,
    deliv_tel01 text,
    deliv_tel02 text,
    deliv_tel03 text,
    deliv_fax01 text,
    deliv_fax02 text,
    deliv_fax03 text,
    deliv_zip01 text,
    deliv_zip02 text,
    deliv_pref smallint,
    deliv_addr01 text,
    deliv_addr02 text,
    subtotal numeric,
    discount numeric,
    deliv_fee numeric,
    charge numeric,
    use_point numeric,
    add_point numeric,
    birth_point numeric DEFAULT 0,
    tax numeric,
    total numeric,
    payment_total numeric,
    payment_id int,
    payment_method text,
    deliv_id int,
    deliv_time_id int,
    deliv_time text,
    deliv_no text,
    note text,
    mail_flag smallint,
    status smallint,
    deliv_check smallint,
    point_check smallint,
    loan_result text,
    credit_result text,
    credit_msg text,
    create_date datetime NOT NULL,
    update_date datetime,
    del_flg smallint NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text,
    order_id int,
    memo01 text,
    memo02 text,
    memo03 text,
    memo04 text,
    memo05 text,
    memo06 text,
    memo07 text,
    memo08 text,
    memo09 text,
    memo10 text,
    session text
) TYPE=InnoDB;

CREATE TABLE dtb_other_deliv (
    other_deliv_id int  NOT NULL,
    customer_id int NOT NULL,
    name01 text,
    name02 text,
    kana01 text,
    kana02 text,
    zip01 text,
    zip02 text,
    pref smallint,
    addr01 text,
    addr02 text,
    tel01 text,
    tel02 text,
    tel03 text,
    PRIMARY KEY (other_deliv_id)
) TYPE=InnoDB;

CREATE TABLE dtb_order_detail (
    order_id int NOT NULL,
    product_id int NOT NULL,
    product_class_id int NOT NULL,
    product_name text NOT NULL,
    product_code text,
    classcategory_name1 text,
    classcategory_name2 text,
    price numeric,
    quantity numeric,
    point_rate numeric
) TYPE=InnoDB;

CREATE TABLE mtb_pref (
    pref_id smallint NOT NULL,
    pref_name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (pref_id)
) TYPE=InnoDB;

CREATE TABLE dtb_member (
    member_id int  NOT NULL,
    name text,
    department text,
    login_id text NOT NULL,
    password text NOT NULL,
    authority smallint NOT NULL,
    rank int NOT NULL DEFAULT 0,
    work smallint NOT NULL DEFAULT 1,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    update_date datetime,
    create_date datetime NOT NULL,
    login_date datetime,
    PRIMARY KEY (member_id)
) TYPE=InnoDB;

CREATE TABLE dtb_bat_relate_products (
    product_id int,
    relate_product_id int,
    customer_id int,
    create_date datetime
) TYPE=InnoDB;

CREATE TABLE dtb_pagelayout (
    page_id int  NOT NULL,
    page_name text,
    url text NOT NULL,
    php_dir text,
    tpl_dir text,
    filename text,
    header_chk smallint DEFAULT 1,
    footer_chk smallint DEFAULT 1,
    edit_flg smallint DEFAULT 1,
    author text,
    description text,
    keyword text,
    update_url text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (page_id)
) TYPE=InnoDB;

CREATE TABLE dtb_bloc (
    bloc_id int  NOT NULL,
    bloc_name text,
    tpl_path text,
    filename varchar(50) NOT NULL UNIQUE,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    php_path text,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (bloc_id)
) TYPE=InnoDB;

CREATE TABLE dtb_blocposition (
    page_id int NOT NULL,
    target_id int,
    bloc_id int,
    bloc_row int,
    filename text,
    anywhere int DEFAULT 0 NOT NULL
) TYPE=InnoDB;

CREATE TABLE dtb_csv (
    no int ,
    csv_id int NOT NULL,
    col text,
    disp_name text,
    rank int,
    status smallint NOT NULL DEFAULT 1,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    mb_convert_kana_option text,
    PRIMARY KEY (no)
) TYPE=InnoDB;

CREATE TABLE dtb_csv_sql (
    sql_id int ,
    sql_name text NOT NULL,
    csv_sql text,
    update_date datetime NOT NULL,
    create_date datetime NOT NULL,
    PRIMARY KEY (sql_id)
) TYPE=InnoDB;

CREATE TABLE dtb_templates
(
    template_code varchar(50) NOT NULL,
    template_name text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (template_code)
) TYPE=InnoDB;

CREATE TABLE dtb_table_comment
(
    id int ,
    table_name text,
    column_name text,
    description text,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE dtb_maker (
    maker_id int ,
    name text NOT NULL,
    rank int NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    del_flg smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (maker_id)
) TYPE=InnoDB;

CREATE TABLE dtb_maker_count (
    maker_id int NOT NULL,
    product_count int NOT NULL,
    create_date datetime NOT NULL
) TYPE=InnoDB;


CREATE TABLE mtb_permission (
    id text,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id(32))
) TYPE=InnoDB;

CREATE TABLE mtb_disable_logout (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_authority (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_work (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_disp (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_class (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_status (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_status_image (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_allowed_tag (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_page_max (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_magazine_type (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_magazine_type (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_recommend (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_taxrule (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_template (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_tpl_path (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_job (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_reminder (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_sex (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_page_rows (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mail_type (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_order_status (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_product_status_color (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_order_status_color (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_wday (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_delivery_date (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_product_list_max (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_convenience (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_conveni_message (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_db (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_target (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_review_deny_url (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_track_back_status (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_site_control_track_back (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_site_control_affiliate (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_mobile_domain (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_ownersstore_err (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_ownersstore_ips (
    id smallint,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE mtb_constants (
    id text,
    name text,
    rank smallint NOT NULL DEFAULT 0,
    remarks text,
    PRIMARY KEY (id(64))
) TYPE=InnoDB;

CREATE TABLE mtb_product_type (
    id int2,
    name text,
    rank int2 NOT NULL,
    PRIMARY KEY (id)
) TYPE=InnoDB;

CREATE TABLE dtb_mobile_ext_session_id (
    session_id text NOT NULL,
    param_key text,
    param_value text,
    url text,
    create_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_mobile_kara_mail (
    kara_mail_id serial PRIMARY KEY,
    session_id text NOT NULL,
    token text NOT NULL,
    next_url text NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    email text,
    receive_date timestamp
);

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

CREATE TABLE dtb_session (
    sess_id varchar(50) NOT NULL,
    sess_data text,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (sess_id)
) TYPE=InnoDB;

CREATE TABLE dtb_site_control (
    control_id int  NOT NULL,
    control_title varchar(255) ,
    control_text text ,
    control_flg int NOT NULL DEFAULT 2,
    del_flg int NOT NULL DEFAULT 0,
    memo text ,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY  (control_id)
) TYPE=InnoDB;

CREATE TABLE dtb_trackback (
    trackback_id int  NOT NULL,
    product_id int NOT NULL,
    blog_name varchar(255) NOT NULL DEFAULT '',
    title varchar(255) NOT NULL DEFAULT '',
    excerpt text NOT NULL ,
    url text NOT NULL ,
    status int NOT NULL DEFAULT 2,
    del_flg int NOT NULL DEFAULT 0,
    create_date datetime NOT NULL,
    update_date datetime NOT NULL,
    PRIMARY KEY (trackback_id)
) TYPE=InnoDB;

CREATE TABLE dtb_bkup (
    bkup_name varchar(50),
    bkup_memo text,
    create_date datetime,
    PRIMARY KEY (bkup_name)
) TYPE=InnoDB;

CREATE INDEX dtb_customer_mobile_phone_id_key ON dtb_customer (mobile_phone_id(64));
CREATE INDEX dtb_products_class_product_id_key ON dtb_products_class(product_id);
CREATE INDEX dtb_order_detail_product_id_key ON dtb_order_detail(product_id);
CREATE INDEX dtb_send_customer_customer_id_key ON dtb_send_customer(customer_id);
CREATE INDEX dtb_mobile_ext_session_id_param_key_key ON dtb_mobile_ext_session_id (param_key(64));
CREATE INDEX dtb_mobile_ext_session_id_param_value_key ON dtb_mobile_ext_session_id (param_value(64));
CREATE INDEX dtb_mobile_ext_session_id_url_key ON dtb_mobile_ext_session_id (url(64));
CREATE INDEX dtb_mobile_ext_session_id_create_date_key ON dtb_mobile_ext_session_id (create_date);
CREATE INDEX dtb_mobile_kara_mail_token_key ON dtb_mobile_kara_mail (token(64));
CREATE INDEX dtb_mobile_kara_mail_create_date_key ON dtb_mobile_kara_mail (create_date);
CREATE INDEX dtb_mobile_kara_mail_receive_date_key ON dtb_mobile_kara_mail (receive_date);

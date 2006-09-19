CREATE TABLE dtb_kiyaku (
    kiyaku_id int auto_increment primary key NOT NULL,
    kiyaku_title text NOT NULL,
    kiyaku_text text NOT NULL,
    rank int NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL,
    del_flg  smallint NOT NULL DEFAULT 0
);

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
);

CREATE TABLE dtb_bat_order_daily_age (
    order_count numeric NOT NULL DEFAULT 0,
    total numeric NOT NULL DEFAULT 0,
    total_average numeric NOT NULL DEFAULT 0,
    start_age smallint,
    end_age smallint,
    member smallint,
    order_date timestamp ,
    create_date timestamp NOT NULL 
);

CREATE TABLE dtb_update (
    module_id int NOT NULL UNIQUE,
    module_name text NOT NULL,
    now_version text,
    latest_version text NOT NULL,
    module_explain text,
    main_php text NOT NULL,
    extern_php text NOT NULL,
    sql text,
    uninstall_sql text,
    other_files text,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    release_date timestamp NOT NULL
);

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
    tax numeric DEFAULT 5,
    tax_rule smallint DEFAULT 1,
    email01 text,
    email02 text,
    email03 text,
    email04 text,
    email05 text,
    free_rule numeric,
    shop_name text,
    shop_kana text,
    point_rate numeric,
    welcome_point numeric,
    update_date timestamp,
    top_tpl text,
    product_tpl text,
    detail_tpl text,
    mypage_tpl text,
    good_traded text,
    message text
);

CREATE TABLE dtb_deliv (
    deliv_id int auto_increment primary key NOT NULL,
    name text,
    service_name text,
    confirm_url text,
    rank int,
    status smallint NOT NULL DEFAULT 1,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_delivtime (
    deliv_id int NOT NULL,
    time_id int auto_increment primary key NOT NULL,
    deliv_time text NOT NULL
);

CREATE TABLE dtb_delivfee (
    deliv_id int NOT NULL,
    fee_id int auto_increment primary key NOT NULL,
    fee text NOT NULL,
    pref smallint
);

CREATE TABLE dtb_payment (
    payment_id int auto_increment primary key NOT NULL,
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
    create_date timestamp NOT NULL ,
    update_date timestamp,
    payment_image text,
    upper_rule numeric
);

CREATE TABLE dtb_mailtemplate (
    template_id int NOT NULL,
    subject text,
    header text,
    footer text,
    creator_id int NOT NULL,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_mailmaga_template (
    template_id int auto_increment primary key NOT NULL UNIQUE,
    subject text,
    charge_image text,
    mail_method int,
    header text,
    body text,
    main_title text,
    main_comment text,
    main_product_id int,
    sub_title text,
    sub_comment text,
    sub_product_id01 int,
    sub_product_id02 int,
    sub_product_id03 int,
    sub_product_id04 int,
    sub_product_id05 int,
    sub_product_id06 int,
    sub_product_id07 int,
    sub_product_id08 int,
    sub_product_id09 int,
    sub_product_id10 int,
    sub_product_id11 int,
    sub_product_id12 int,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_send_history (
    send_id int auto_increment primary key NOT NULL,
    mail_method smallint,
    subject text,
    body text,
    send_count int,
    complete_count int NOT NULL DEFAULT 0,
    start_date timestamp,
    end_date timestamp,
    search_data text,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_send_customer (
    customer_id int,
    send_id int auto_increment primary key NOT NULL,
    email text,
    name text,
    send_flag smallint
);

CREATE TABLE dtb_products (
    product_id int auto_increment primary key NOT NULL UNIQUE,
    name text,
    deliv_fee numeric,
    sale_limit numeric,
    sale_unlimited smallint DEFAULT 0,
    category_id int,
    rank int,
    status smallint NOT NULL DEFAULT 2,
    product_flag text,
    point_rate numeric,
    comment1 text,
    comment2 text,
    comment3 mediumtext,
    comment4 text,
    comment5 text,
    comment6 text,
    file1 text,
    file2 text,
    file3 text,
    file4 text,
    file5 text,
    file6 text,
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
    create_date timestamp NOT NULL ,
    update_date timestamp,
    deliv_date_id int
);

CREATE TABLE dtb_products_class (
    product_class_id int auto_increment primary key NOT NULL UNIQUE,
    product_id int NOT NULL,
    classcategory_id1 int NOT NULL DEFAULT 0,
    classcategory_id2 int NOT NULL DEFAULT 0,
    product_code text,
    stock numeric,
    stock_unlimited smallint DEFAULT 0,
    sale_limit numeric,
    price01 numeric,
    price02 numeric,
    status smallint,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_class (
    class_id int auto_increment primary key NOT NULL,
    name text,
    status smallint,
    rank int,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0,
    product_id int
);

CREATE TABLE dtb_classcategory (
    classcategory_id int auto_increment primary key NOT NULL,
    name text,
    class_id int NOT NULL,
    status smallint,
    rank int,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_category (
    category_id int auto_increment primary key NOT NULL,
    category_name text,
    parent_category_id int NOT NULL DEFAULT 0,
    level int NOT NULL,
    rank int,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0
);

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
    order_date timestamp NOT NULL ,
    create_date timestamp NOT NULL ,
    year smallint NOT NULL,
    month smallint NOT NULL,
    day smallint NOT NULL,
    wday smallint NOT NULL,
    key_day text NOT NULL,
    key_month text NOT NULL,
    key_year text NOT NULL,
    key_wday text NOT NULL
);

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
    order_date timestamp ,
    create_date timestamp NOT NULL 
);

CREATE TABLE dtb_recommend_products (
    product_id int NOT NULL,
    recommend_product_id int auto_increment primary key NOT NULL,
    rank int NOT NULL,
    comment text,
    status smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_review (
    review_id int auto_increment primary key NOT NULL,
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
    update_date timestamp,
    create_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_customer_reading (
    reading_product_id int NOT NULL,
    customer_id int NOT NULL,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_category_count (
    category_id int NOT NULL,
    product_count int NOT NULL,
    create_date timestamp NOT NULL 
);

CREATE TABLE dtb_category_total_count (
    category_id int NOT NULL,
    product_count int,
    create_date timestamp NOT NULL 
);

CREATE TABLE dtb_news (
    news_id int auto_increment primary key NOT NULL UNIQUE,
    news_date timestamp,
    rank int,
    news_title text NOT NULL,
    news_comment text,
    news_url text,
    news_select smallint NOT NULL DEFAULT 0,
    link_method text,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_best_products (
    best_id int auto_increment primary key NOT NULL,
    category_id int NOT NULL,
    rank int NOT NULL DEFAULT 0,
    product_id int NOT NULL,
    title text,
    comment text,
    creator_id int NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_mail_history (
    send_id int auto_increment primary key  NOT NULL,
    order_id int NOT NULL,
    send_date timestamp,
    template_id int,
    creator_id int NOT NULL,
    subject text,
    mail_body text
);

CREATE TABLE dtb_customer (
    customer_id int auto_increment primary key  NOT NULL,
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
    birth text,
    password text,
    reminder smallint,
    reminder_answer text,
    secret_key varchar(50) NOT NULL UNIQUE,
    first_buy_date timestamp,
    last_buy_date timestamp,
    buy_times numeric DEFAULT 0,
    buy_total numeric DEFAULT 0,
    point numeric DEFAULT 0,
    note text,
    status smallint NOT NULL DEFAULT 1,
    create_date timestamp NOT NULL ,
    update_date timestamp ,
    del_flg  smallint NOT NULL DEFAULT 0,
    cell01 text,
    cell02 text,
    cell03 text
);

CREATE TABLE dtb_customer_mail (
    email varchar(50) NOT NULL UNIQUE,
    mail_flag smallint,
    create_date timestamp NOT NULL ,
    update_date timestamp 
);

CREATE TABLE dtb_customer_mail_temp (
    email varchar(50) NOT NULL UNIQUE,
    mail_flag smallint,
    temp_id varchar(50) NOT NULL UNIQUE,
    end_flag smallint,
    update_date timestamp NOT NULL ,
    create_data timestamp NOT NULL 
);

CREATE TABLE dtb_order (
    order_id int auto_increment primary key NOT NULL,
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
    order_pref text,
    order_addr01 text,
    order_addr02 text,
    order_sex smallint,
    order_birth text,
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
    deliv_pref text,
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
    create_date timestamp NOT NULL ,
    loan_result text,
    credit_result text,
    credit_msg text,
    update_date timestamp,
    commit_date text,
    del_flg  smallint NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text
);

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
    order_pref text,
    order_addr01 text,
    order_addr02 text,
    order_sex smallint,
    order_birth text,
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
    deliv_pref text,
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
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  smallint NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text
);

CREATE TABLE dtb_other_deliv (
    other_deliv_id int auto_increment primary key NOT NULL,
    customer_id int NOT NULL,
    name01 text,
    name02 text,
    kana01 text,
    kana02 text,
    zip01 text,
    zip02 text,
    pref text,
    addr01 text,
    addr02 text,
    tel01 text,
    tel02 text,
    tel03 text
);

CREATE TABLE dtb_order_detail (
    order_id int NOT NULL,
    product_id int NOT NULL,
    classcategory_id1 int NOT NULL,
    classcategory_id2 int NOT NULL,
    product_name text NOT NULL,
    product_code text,
    classcategory_name1 text,
    classcategory_name2 text,
    price numeric,
    quantity numeric,
    point_rate numeric
);

CREATE TABLE mtb_pref (
    pref_id smallint NOT NULL,
    pref_name text,
    rank smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_member (
    member_id int auto_increment primary key NOT NULL,
    name text,
    department text,
    login_id text NOT NULL,
    password text NOT NULL,
    authority smallint NOT NULL,
    rank int NOT NULL DEFAULT 0,
    work smallint NOT NULL DEFAULT 1,
    del_flg smallint NOT NULL DEFAULT 0,
    creator_id int NOT NULL,
    update_date timestamp,
    create_date timestamp NOT NULL ,
    login_date timestamp
);

CREATE TABLE dtb_question (
    question_id int auto_increment primary key NOT NULL,
    question_name text,
    question text,
    create_date timestamp NOT NULL ,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_question_result (
    result_id int auto_increment primary key NOT NULL,
    question_id int NOT NULL,
    question_date timestamp,
    question_name text,
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
    mail01 text,
    question01 text,
    question02 text,
    question03 text,
    question04 text,
    question05 text,
    question06 text,
    create_date timestamp NOT NULL ,
    del_flg  smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_bat_relate_products (
    product_id int,
    relate_product_id int,
    customer_id int,
    create_date timestamp 
);

CREATE TABLE dtb_campaign (
    campaign_id int auto_increment primary key NOT NULL,
    campaign_name text,
    campaign_point_rate numeric NOT NULL,
    campaign_point_type smallint,
    start_date timestamp NOT NULL,
    end_date timestamp NOT NULL,
    search_condition text,
    del_flg smallint NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_campaign_detail (
    campaign_id int NOT NULL,
    product_id int NOT NULL,
    campaign_point_rate numeric NOT NULL
);

CREATE TABLE dtb_pagelayout (
    page_id int auto_increment primary key NOT NULL,
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
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_bloc (
    bloc_id int auto_increment primary key NOT NULL,
    bloc_name text,
    tpl_path text,
    filename varchar(50) NOT NULL UNIQUE,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL ,
    php_path text,
    del_flg smallint NOT NULL DEFAULT 0
);

CREATE TABLE dtb_blocposition (
    page_id int NOT NULL,
    target_id int,
    bloc_id int,
    bloc_row int,
    filename text
);

CREATE TABLE dtb_csv (
    no int auto_increment primary key,
    csv_id int NOT NULL,
    col text,
    disp_name text,
    rank int,
    status smallint NOT NULL DEFAULT 1,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL 
);

CREATE TABLE dtb_csv_sql (
    sql_id int auto_increment primary key,
    name text NOT NULL,
    sql text,
    update_date timestamp NOT NULL ,
    create_date timestamp NOT NULL 
);

CREATE TABLE dtb_user_regist (
    user_id int auto_increment primary key NOT NULL,
    org_name text,
    post_name text,
    name01 text,
    name02 text,
    kana01 text,
    kana02 text,
    email text NOT NULL,
    url text,
    note text,
    secret_key varchar(50) NOT NULL UNIQUE,
    status smallint NOT NULL,
    del_flg smallint DEFAULT 0,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL 
);

create table dtb_templates 
(
template_code		varchar(50) NOT NULL UNIQUE	,
template_name		text			,
create_date		timestamp		NOT NULL	,
update_date		timestamp		NOT NULL	
);

create table dtb_table_comment
(
id	int auto_increment primary key,
table_name	text,
column_name	text,
description	text
);
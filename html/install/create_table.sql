CREATE TABLE dtb_kiyaku (
    kiyaku_id serial NOT NULL,
    kiyaku_title text NOT NULL,
    kiyaku_text text NOT NULL,
    rank int4 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL,
    delete  int2 NOT NULL DEFAULT 0
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
    start_age int2,
    end_age int2,
    member int2,
    order_date timestamp DEFAULT now(),
    create_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_update (
    module_id int4 NOT NULL UNIQUE,
    module_name text NOT NULL,
    now_version text,
    latest_version text NOT NULL,
    module_explain text,
    main_php text NOT NULL,
    extern_php text NOT NULL,
    sql text,
    other_files text,
    delete int2 NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    release_date timestamp NOT NULL
);

CREATE TABLE dtb_baseinfo (
    company_name text,
    company_kana text,
    zip01 text,
    zip02 text,
    pref int2,
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
    law_pref int2,
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
    tax_rule int2 DEFAULT 1,
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
    top_tpl int4,
    product_tpl int4,
    detail_tpl int4,
    mypage_tpl int4,
    good_traded text,
    message text
);

CREATE TABLE dtb_deliv (
    deliv_id serial NOT NULL,
    name text,
    service_name text,
    confirm_url text,
    rank int4,
    status int2 NOT NULL DEFAULT 1,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp
);

CREATE TABLE dtb_delivtime (
    deliv_id int4 NOT NULL,
    time_id serial NOT NULL,
    time text NOT NULL
);

CREATE TABLE dtb_delivfee (
    deliv_id int4 NOT NULL,
    fee_id serial NOT NULL,
    fee text NOT NULL,
    pref int2
);

CREATE TABLE dtb_payment (
    payment_id serial NOT NULL,
    payment_method text,
    charge numeric,
    rule numeric,
    deliv_id int4 DEFAULT 0,
    rank int4,
    note text,
    fix int2,
    status int2 NOT NULL DEFAULT 1,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    payment_image text,
    upper_rule numeric
);

CREATE TABLE dtb_mailtemplate (
    template_id int4 NOT NULL,
    subject text,
    header text,
    footer text,
    creator_id int4 NOT NULL,
    delete int2 NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_mailmaga_template (
    template_id serial NOT NULL UNIQUE,
    subject text,
    charge_image text,
    mail_method int4,
    header text,
    body text,
    main_title text,
    main_comment text,
    main_product_id int4,
    sub_title text,
    sub_comment text,
    sub_product_id01 int4,
    sub_product_id02 int4,
    sub_product_id03 int4,
    sub_product_id04 int4,
    sub_product_id05 int4,
    sub_product_id06 int4,
    sub_product_id07 int4,
    sub_product_id08 int4,
    sub_product_id09 int4,
    sub_product_id10 int4,
    sub_product_id11 int4,
    sub_product_id12 int4,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp
);

CREATE TABLE dtb_send_history (
    send_id serial NOT NULL,
    mail_method int2,
    subject text,
    body text,
    send_count int4,
    complete_count int4 NOT NULL DEFAULT 0,
    start_date timestamp,
    end_date timestamp,
    search_data text,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_send_customer (
    customer_id int4,
    send_id serial NOT NULL,
    email text,
    name text,
    send_flag int2
);

CREATE TABLE dtb_products (
    product_id serial NOT NULL UNIQUE,
    name text,
    deliv_fee numeric,
    sale_limit numeric,
    sale_unlimited int2 DEFAULT 0,
    category_id int4,
    rank int4,
    status int2 NOT NULL DEFAULT 2,
    product_flag text,
    point_rate numeric,
    comment1 text,
    comment2 text,
    comment3 text,
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
    main_comment text,
    main_image text,
    main_large_image text,
    sub_title1 text,
    sub_comment1 text,
    sub_image1 text,
    sub_large_image1 text,
    sub_title2 text,
    sub_comment2 text,
    sub_image2 text,
    sub_large_image2 text,
    sub_title3 text,
    sub_comment3 text,
    sub_image3 text,
    sub_large_image3 text,
    sub_title4 text,
    sub_comment4 text,
    sub_image4 text,
    sub_large_image4 text,
    sub_title5 text,
    sub_comment5 text,
    sub_image5 text,
    sub_large_image5 text,
    sub_title6 text,
    sub_comment6 text,
    sub_image6 text,
    sub_large_image6 text,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    deliv_date_id int4
);

CREATE TABLE dtb_products_class (
    product_class_id serial NOT NULL UNIQUE,
    product_id int4 NOT NULL,
    classcategory_id1 int4 NOT NULL DEFAULT 0,
    classcategory_id2 int4 NOT NULL DEFAULT 0,
    product_code text,
    stock numeric,
    stock_unlimited int2 DEFAULT 0,
    sale_limit numeric,
    price01 numeric,
    price02 numeric,
    status int2,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp
);

CREATE TABLE dtb_class (
    class_id serial NOT NULL,
    name text,
    status int2,
    rank int4,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0,
    product_id int4
);

CREATE TABLE dtb_classcategory (
    classcategory_id serial NOT NULL,
    name text,
    class_id int4 NOT NULL,
    status int2,
    rank int4,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_category (
    category_id serial NOT NULL,
    category_name text,
    parent_category_id int4 NOT NULL DEFAULT 0,
    level int4 NOT NULL,
    rank int4,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0
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
    order_date timestamp NOT NULL DEFAULT now(),
    create_date timestamp NOT NULL DEFAULT now(),
    year int2 NOT NULL,
    month int2 NOT NULL,
    day int2 NOT NULL,
    wday int2 NOT NULL,
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
    hour int2 NOT NULL DEFAULT 0,
    order_date timestamp DEFAULT now(),
    create_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_recommend_products (
    product_id int4 NOT NULL,
    recommend_product_id serial NOT NULL,
    rank int4 NOT NULL,
    comment text,
    status int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_review (
    review_id serial NOT NULL,
    product_id int4 NOT NULL,
    reviewer_name text NOT NULL,
    reviewer_url text,
    sex int2,
    customer_id int4,
    recommend_level int2 NOT NULL,
    title text NOT NULL,
    comment text NOT NULL,
    status int2 DEFAULT 2,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_customer_reading (
    reading_product_id int4 NOT NULL,
    customer_id int4 NOT NULL,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL DEFAULT NOW()
);

CREATE TABLE dtb_category_count (
    category_id int4 NOT NULL,
    product_count int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT Now()
);

CREATE TABLE dtb_category_total_count (
    category_id int4 NOT NULL,
    product_count int4,
    create_date timestamp NOT NULL DEFAULT Now()
);

CREATE TABLE dtb_news (
    news_id serial NOT NULL UNIQUE,
    news_date timestamp,
    rank int4,
    news_title text NOT NULL,
    news_comment text,
    news_url text,
    news_select int2 NOT NULL DEFAULT 0,
    link_method text,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_best_products (
    best_id serial NOT NULL,
    category_id int4 NOT NULL,
    rank int4 NOT NULL DEFAULT 0,
    product_id int4 NOT NULL,
    title text,
    comment text,
    creator_id int4 NOT NULL,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_mail_history (
    send_id serial  NOT NULL,
    order_id int4 NOT NULL,
    send_date timestamp,
    template_id int4,
    creator_id int4 NOT NULL,
    subject text,
    mail_body text
);

CREATE TABLE dtb_customer (
    customer_id serial  NOT NULL,
    name01 text NOT NULL,
    name02 text NOT NULL,
    kana01 text NOT NULL,
    kana02 text NOT NULL,
    zip01 text,
    zip02 text,
    pref int2,
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
    sex int2,
    job int2,
    birth timestamp,
    password text,
    reminder int2,
    reminder_answer text,
    secret_key text NOT NULL UNIQUE,
    first_buy_date timestamp,
    last_buy_date timestamp,
    buy_times numeric DEFAULT 0,
    buy_total numeric DEFAULT 0,
    point numeric DEFAULT 0,
    note text,
    status int2 NOT NULL DEFAULT 1,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp DEFAULT now(),
    delete  int2 NOT NULL DEFAULT 0,
    cell01 text,
    cell02 text,
    cell03 text
);

CREATE TABLE dtb_customer_mail (
    email text NOT NULL UNIQUE,
    mail_flag int2,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp DEFAULT now()
);

CREATE TABLE dtb_customer_mail_temp (
    email text NOT NULL UNIQUE,
    mail_flag int2,
    temp_id text NOT NULL UNIQUE,
    end_flag int2,
    update_date timestamp NOT NULL DEFAULT Now(),
    create_data timestamp NOT NULL DEFAULT Now()
);

CREATE TABLE dtb_order (
    order_id serial NOT NULL,
    order_temp_id text,
    customer_id int4 NOT NULL,
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
    order_sex int2,
    order_birth timestamp,
    order_job int4,
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
    payment_id int4,
    payment_method text,
    deliv_id int4,
    deliv_time_id int4,
    deliv_time text,
    deliv_no text,
    note text,
    status int2,
    create_date timestamp NOT NULL DEFAULT now(),
    loan_result text,
    credit_result text,
    credit_msg text,
    update_date timestamp,
    commit_date timestamp,
    delete  int2 NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text
);

CREATE TABLE dtb_order_temp (
    order_temp_id text NOT NULL,
    customer_id int4 NOT NULL,
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
    order_sex int2,
    order_birth timestamp,
    order_job int4,
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
    payment_id int4,
    payment_method text,
    deliv_id int4,
    deliv_time_id int4,
    deliv_time text,
    deliv_no text,
    note text,
    mail_flag int2,
    status int2,
    deliv_check int2,
    point_check int2,
    loan_result text,
    credit_result text,
    credit_msg text,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp,
    delete  int2 NOT NULL DEFAULT 0,
    deliv_date text,
    conveni_data text,
    cell01 text,
    cell02 text,
    cell03 text
);

CREATE TABLE dtb_other_deliv (
    other_deliv_id serial NOT NULL,
    customer_id int4 NOT NULL,
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
    order_id int4 NOT NULL,
    product_id int4 NOT NULL,
    classcategory_id1 int4 NOT NULL,
    classcategory_id2 int4 NOT NULL,
    product_name text NOT NULL,
    product_code text,
    classcategory_name1 text,
    classcategory_name2 text,
    price numeric,
    quantity numeric,
    point_rate numeric
);

CREATE TABLE mtb_pref (
    pref_id int2 NOT NULL,
    pref_name text,
    rank int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_member (
    member_id serial NOT NULL,
    name text,
    department text,
    login_id text NOT NULL,
    password text NOT NULL,
    authority int2 NOT NULL,
    rank int4 NOT NULL DEFAULT 0,
    work int2 NOT NULL DEFAULT 1,
    delete int2 NOT NULL DEFAULT 0,
    creator_id int4 NOT NULL,
    update_date timestamp,
    create_date timestamp NOT NULL DEFAULT now(),
    login_date timestamp
);

CREATE TABLE dtb_question (
    question_id serial NOT NULL,
    question_name text,
    question text,
    create_date timestamp NOT NULL DEFAULT now(),
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_question_result (
    result_id serial NOT NULL,
    question_id int4 NOT NULL,
    question_date timestamp,
    question_name text,
    name01 text,
    name02 text,
    kana01 text,
    kana02 text,
    zip01 text,
    zip02 text,
    pref int2,
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
    create_date timestamp NOT NULL DEFAULT now(),
    delete  int2 NOT NULL DEFAULT 0
);

CREATE TABLE dtb_bat_relate_products (
    product_id int4,
    relate_product_id int4,
    customer_id int4,
    create_date timestamp DEFAULT now()
);

CREATE TABLE dtb_campaign (
    campaign_id serial NOT NULL,
    campaign_name text,
    campaign_point_rate numeric NOT NULL,
    campaign_point_type int2,
    start_date timestamp NOT NULL,
    end_date timestamp NOT NULL,
    search_condition text,
    delete int2 NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_campaign_detail (
    campaign_id int4 NOT NULL,
    product_id int4 NOT NULL,
    campaign_point_rate numeric NOT NULL
);

CREATE TABLE dtb_pagelayout (
    page_id serial NOT NULL,
    page_name text,
    url text NOT NULL,
    php_dir text,
    tpl_dir text,
    filename text,
    header_chk int2 DEFAULT 1,
    footer_chk int2 DEFAULT 1,
    edit_flg int2 DEFAULT 1,
    author text,
    description text,
    keyword text,
    update_url text,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_bloc (
    bloc_id serial NOT NULL,
    bloc_name text,
    tpl_path text,
    filename text,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now(),
    php_path text
);

CREATE TABLE dtb_blocposition (
    page_id int4 NOT NULL,
    target_id int4,
    bloc_id int4,
    bloc_row int4
);

CREATE TABLE dtb_csv (
    no serial,
    csv_id int4 NOT NULL,
    col text,
    disp_name text,
    rank int4,
    status int2 NOT NULL DEFAULT 1,
    create_date timestamp NOT NULL DEFAULT now(),
    update_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_csv_sql (
    sql_id serial,
    name text NOT NULL,
    sql text,
    update_date timestamp NOT NULL DEFAULT now(),
    create_date timestamp NOT NULL DEFAULT now()
);

CREATE TABLE dtb_user_regist (
    user_id serial NOT NULL,
    org_name text,
    post_name text,
    name01 text,
    name02 text,
    kana01 text,
    kana02 text,
    email text NOT NULL,
    url text,
    note text,
    secret_key text NOT NULL UNIQUE,
    status int2 NOT NULL,
    delete int2 DEFAULT 0,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL DEFAULT now()
);


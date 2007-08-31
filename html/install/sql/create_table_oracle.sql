--
-- FIXME 要修正
--

CREATE TABLE dtb_kiyaku (
    kiyaku_id number(9) NOT NULL,
    kiyaku_title varchar2(4000) NOT NULL,
    kiyaku_text varchar2(4000) NOT NULL,
    rank number(9) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL,
    del_flg  number(4) NOT NULL
);

CREATE TABLE mtb_zip (
    code varchar2(4000),
    old_zipcode varchar2(4000),
    zipcode varchar2(4000),
    state_kana varchar2(4000),
    city_kana varchar2(4000),
    town_kana varchar2(4000),
    state varchar2(4000),
    city varchar2(4000),
    town varchar2(4000),
    flg1 varchar2(4000),
    flg2 varchar2(4000),
    flg3 varchar2(4000),
    flg4 varchar2(4000),
    flg5 varchar2(4000),
    flg6 varchar2(4000)
);

CREATE TABLE dtb_bat_order_daily_age (
    order_count number(9) NOT NULL ,
    total number(9) NOT NULL ,
    total_average number(9) NOT NULL ,
    start_age number(4),
    end_age number(4),
    member number(4),
    order_date timestamp ,
    create_date timestamp NOT NULL
);

CREATE TABLE dtb_update (
    module_id number(9) NOT NULL UNIQUE,
    module_name varchar2(4000) NOT NULL,
    now_version varchar2(4000),
    latest_version varchar2(4000) NOT NULL,
    module_explain varchar2(4000),
    main_php varchar2(4000) NOT NULL,
    extern_php varchar2(4000) NOT NULL,
    install_sql varchar2(4000),
    uninstall_sql varchar2(4000),
    other_files varchar2(4000),
    del_flg number(4) NOT NULL ,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    release_date timestamp NOT NULL
);

CREATE TABLE dtb_baseinfo (
    company_name varchar2(4000),
    company_kana varchar2(4000),
    zip01 varchar2(4000),
    zip02 varchar2(4000),
    pref number(4),
    addr01 varchar2(4000),
    addr02 varchar2(4000),
    tel01 varchar2(4000),
    tel02 varchar2(4000),
    tel03 varchar2(4000),
    fax01 varchar2(4000),
    fax02 varchar2(4000),
    fax03 varchar2(4000),
    business_hour varchar2(4000),
    law_company varchar2(4000),
    law_manager varchar2(4000),
    law_zip01 varchar2(4000),
    law_zip02 varchar2(4000),
    law_pref number(4),
    law_addr01 varchar2(4000),
    law_addr02 varchar2(4000),
    law_tel01 varchar2(4000),
    law_tel02 varchar2(4000),
    law_tel03 varchar2(4000),
    law_fax01 varchar2(4000),
    law_fax02 varchar2(4000),
    law_fax03 varchar2(4000),
    law_email varchar2(4000),
    law_url varchar2(4000),
    law_term01 varchar2(4000),
    law_term02 varchar2(4000),
    law_term03 varchar2(4000),
    law_term04 varchar2(4000),
    law_term05 varchar2(4000),
    law_term06 varchar2(4000),
    law_term07 varchar2(4000),
    law_term08 varchar2(4000),
    law_term09 varchar2(4000),
    law_term10 varchar2(4000),
    tax number(9) ,
    tax_rule number(4) ,
    email01 varchar2(4000),
    email02 varchar2(4000),
    email03 varchar2(4000),
    email04 varchar2(4000),
    email05 varchar2(4000),
    free_rule number(9),
    shop_name varchar2(4000),
    shop_kana varchar2(4000),
    point_rate number(9),
    welcome_point number(9),
    update_date timestamp,
    top_tpl varchar2(4000),
    product_tpl varchar2(4000),
    detail_tpl varchar2(4000),
    mypage_tpl varchar2(4000),
    good_traded varchar2(4000),
    message varchar2(4000)
);

CREATE TABLE dtb_deliv (
    deliv_id number(9) NOT NULL,
    name varchar2(4000),
    service_name varchar2(4000),
    confirm_url varchar2(4000),
    rank number(9),
    status number(4) NOT NULL ,
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_delivtime (
    deliv_id number(9) NOT NULL,
    time_id number(9) NOT NULL,
    deliv_time varchar2(4000) NOT NULL
);

CREATE TABLE dtb_delivfee (
    deliv_id number(9) NOT NULL,
    fee_id number(9) NOT NULL,
    fee varchar2(4000) NOT NULL,
    pref number(4)
);

CREATE TABLE dtb_payment (
    payment_id number(9) NOT NULL,
    payment_method varchar2(4000),
    charge number(9),
    rule number(9),
    deliv_id number(9) ,
    rank number(9),
    note varchar2(4000),
    fix number(4),
    status number(4) NOT NULL ,
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    payment_image varchar2(4000),
    upper_rule number(9),
    charge_flg number(4) ,
    rule_min number(9),
    upper_rule_max number(9),
    module_id number(9),
    module_path varchar2(4000),
    memo01 varchar2(4000),
    memo02 varchar2(4000),
    memo03 varchar2(4000),
    memo04 varchar2(4000),
    memo05 varchar2(4000),
    memo06 varchar2(4000),
    memo07 varchar2(4000),
    memo08 varchar2(4000),
    memo09 varchar2(4000),
    memo10 varchar2(4000)
);

CREATE TABLE dtb_mailtemplate (
    template_id number(9) NOT NULL,
    subject varchar2(4000),
    header varchar2(4000),
    footer varchar2(4000),
    creator_id number(9) NOT NULL,
    del_flg number(4) NOT NULL ,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_mailmaga_template (
    template_id number(9) NOT NULL UNIQUE,
    subject varchar2(4000),
    charge_image varchar2(4000),
    mail_method number(9),
    header varchar2(4000),
    body varchar2(4000),
    main_title varchar2(4000),
    main_comment varchar2(4000),
    main_product_id number(9),
    sub_title varchar2(4000),
    sub_comment varchar2(4000),
    sub_product_id01 number(9),
    sub_product_id02 number(9),
    sub_product_id03 number(9),
    sub_product_id04 number(9),
    sub_product_id05 number(9),
    sub_product_id06 number(9),
    sub_product_id07 number(9),
    sub_product_id08 number(9),
    sub_product_id09 number(9),
    sub_product_id10 number(9),
    sub_product_id11 number(9),
    sub_product_id12 number(9),
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_send_history (
    send_id number(9) NOT NULL,
    mail_method number(4),
    subject varchar2(4000),
    body varchar2(4000),
    send_count number(9),
    complete_count number(9) NOT NULL ,
    start_date timestamp,
    end_date timestamp,
    search_data varchar2(4000),
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_send_customer (
    customer_id number(9),
    send_id number(9) NOT NULL,
    email varchar2(4000),
    name varchar2(4000),
    send_flag number(4)
);

CREATE TABLE dtb_products (
    product_id number(9) NOT NULL UNIQUE,
    name varchar2(4000),
    deliv_fee number(9),
    sale_limit number(9),
    sale_unlimited number(4) ,
    category_id number(9),
    rank number(9),
    status number(4) NOT NULL ,
    product_flag varchar2(4000),
    point_rate number(9),
    comment1 varchar2(4000),
    comment2 varchar2(4000),
    comment3 varchar2(4000),
    comment4 varchar2(4000),
    comment5 varchar2(4000),
    comment6 varchar2(4000),
    file1 varchar2(4000),
    file2 varchar2(4000),
    file3 varchar2(4000),
    file4 varchar2(4000),
    file5 varchar2(4000),
    file6 varchar2(4000),
    main_list_comment varchar2(4000),
    main_list_image varchar2(4000),
    main_comment varchar2(4000),
    main_image varchar2(4000),
    main_large_image varchar2(4000),
    sub_title1 varchar2(4000),
    sub_comment1 varchar2(4000),
    sub_image1 varchar2(4000),
    sub_large_image1 varchar2(4000),
    sub_title2 varchar2(4000),
    sub_comment2 varchar2(4000),
    sub_image2 varchar2(4000),
    sub_large_image2 varchar2(4000),
    sub_title3 varchar2(4000),
    sub_comment3 varchar2(4000),
    sub_image3 varchar2(4000),
    sub_large_image3 varchar2(4000),
    sub_title4 varchar2(4000),
    sub_comment4 varchar2(4000),
    sub_image4 varchar2(4000),
    sub_large_image4 varchar2(4000),
    sub_title5 varchar2(4000),
    sub_comment5 varchar2(4000),
    sub_image5 varchar2(4000),
    sub_large_image5 varchar2(4000),
    sub_title6 varchar2(4000),
    sub_comment6 varchar2(4000),
    sub_image6 varchar2(4000),
    sub_large_image6 varchar2(4000),
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    deliv_date_id number(9)
);

CREATE TABLE dtb_products_class (
    product_class_id number(9) NOT NULL UNIQUE,
    product_id number(9) NOT NULL,
    classcategory_id1 number(9) NOT NULL ,
    classcategory_id2 number(9) NOT NULL ,
    product_code varchar2(4000),
    stock number(9),
    stock_unlimited number(4) ,
    sale_limit number(9),
    price01 number(9),
    price02 number(9),
    status number(4),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp
);

CREATE TABLE dtb_class (
    class_id number(9) NOT NULL,
    name varchar2(4000),
    status number(4),
    rank number(9),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL ,
    product_id number(9)
);

CREATE TABLE dtb_classcategory (
    classcategory_id number(9) NOT NULL,
    name varchar2(4000),
    class_id number(9) NOT NULL,
    status number(4),
    rank number(9),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_category (
    category_id number(9) NOT NULL,
    category_name varchar2(4000),
    parent_category_id number(9) NOT NULL ,
    category_level number(9) NOT NULL,
    rank number(9),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL
);


CREATE TABLE dtb_bat_order_daily (
    total_order number(9) NOT NULL ,
    member number(9) NOT NULL ,
    nonmember number(9) NOT NULL ,
    men number(9) NOT NULL ,
    women number(9) NOT NULL ,
    men_member number(9) NOT NULL ,
    men_nonmember number(9) NOT NULL ,
    women_member number(9) NOT NULL ,
    women_nonmember number(9) NOT NULL ,
    total number(9) NOT NULL ,
    total_average number(9) NOT NULL ,
    order_date timestamp NOT NULL ,
    create_date timestamp NOT NULL ,
    year number(4) NOT NULL,
    month number(4) NOT NULL,
    day number(4) NOT NULL,
    wday number(4) NOT NULL,
    key_day varchar2(4000) NOT NULL,
    key_month varchar2(4000) NOT NULL,
    key_year varchar2(4000) NOT NULL,
    key_wday varchar2(4000) NOT NULL
);

CREATE TABLE dtb_bat_order_daily_hour (
    total_order number(9) NOT NULL ,
    member number(9) NOT NULL ,
    nonmember number(9) NOT NULL ,
    men number(9) NOT NULL ,
    women number(9) NOT NULL ,
    men_member number(9) NOT NULL ,
    men_nonmember number(9) NOT NULL ,
    women_member number(9) NOT NULL ,
    women_nonmember number(9) NOT NULL ,
    total number(9) NOT NULL ,
    total_average number(9) NOT NULL ,
    hour number(4) NOT NULL ,
    order_date timestamp ,
    create_date timestamp NOT NULL
);

CREATE TABLE dtb_recommend_products (
    product_id number(9) NOT NULL,
    recommend_product_id number(9) NOT NULL,
    rank number(9) NOT NULL,
    recommend_product_comment varchar2(4000),
    status number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_review (
    review_id number(9) NOT NULL,
    product_id number(9) NOT NULL,
    reviewer_name varchar2(4000) NOT NULL,
    reviewer_url varchar2(4000),
    sex number(4),
    customer_id number(9),
    recommend_level number(4) NOT NULL,
    title varchar2(4000) NOT NULL,
    review_comment varchar2(4000) NOT NULL,
    status number(4) ,
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_customer_reading (
    reading_product_id number(9) NOT NULL,
    customer_id number(9) NOT NULL,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_category_count (
    category_id number(9) NOT NULL,
    product_count number(9) NOT NULL,
    create_date timestamp NOT NULL
);

CREATE TABLE dtb_category_total_count (
    category_id number(9) NOT NULL,
    product_count number(9),
    create_date timestamp NOT NULL
);

CREATE TABLE dtb_news (
    news_id number(9) NOT NULL UNIQUE,
    news_date timestamp,
    rank number(9),
    news_title varchar2(4000) NOT NULL,
    news_comment varchar2(4000),
    news_url varchar2(4000),
    news_select number(4) NOT NULL ,
    link_method varchar2(4000),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_best_products (
    best_id number(9) NOT NULL,
    category_id number(9) NOT NULL,
    rank number(9) NOT NULL ,
    product_id number(9) NOT NULL,
    title varchar2(4000),
    product_comment varchar2(4000),
    creator_id number(9) NOT NULL,
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_mail_history (
    send_id number(9)  NOT NULL,
    order_id number(9) NOT NULL,
    send_date timestamp,
    template_id number(9),
    creator_id number(9) NOT NULL,
    subject varchar2(4000),
    mail_body varchar2(4000)
);

CREATE TABLE dtb_customer (
    customer_id number(9)  NOT NULL,
    name01 varchar2(4000) NOT NULL,
    name02 varchar2(4000) NOT NULL,
    kana01 varchar2(4000) NOT NULL,
    kana02 varchar2(4000) NOT NULL,
    zip01 varchar2(4000),
    zip02 varchar2(4000),
    pref number(4),
    addr01 varchar2(4000),
    addr02 varchar2(4000),
    email varchar2(4000) NOT NULL,
    email_mobile varchar2(4000),
    tel01 varchar2(4000),
    tel02 varchar2(4000),
    tel03 varchar2(4000),
    fax01 varchar2(4000),
    fax02 varchar2(4000),
    fax03 varchar2(4000),
    sex number(4),
    job number(4),
    birth timestamp,
    password varchar2(4000),
    reminder number(4),
    reminder_answer varchar2(4000),
    secret_key varchar2(4000) NOT NULL UNIQUE,
    first_buy_date timestamp,
    last_buy_date timestamp,
    buy_times number(9) ,
    buy_total number(9) ,
    point number(9) ,
    note varchar2(4000),
    status number(4) NOT NULL ,
    create_date timestamp NOT NULL ,
    update_date timestamp ,
    del_flg  number(4) NOT NULL ,
    cell01 varchar2(4000),
    cell02 varchar2(4000),
    cell03 varchar2(4000),
    mobile_phone_id varchar2(4000),
    mailmaga_flg number(4)
);

-- CREATE INDEX dtb_customer_mobile_phone_id_key ON dtb_customer (mobile_phone_id);

CREATE TABLE dtb_customer_mail_temp (
    email varchar2(4000) NOT NULL UNIQUE,
    mail_flag number(4),
    temp_id varchar2(4000) NOT NULL UNIQUE,
    end_flag number(4),
    update_date timestamp NOT NULL ,
    create_data timestamp NOT NULL
);

CREATE TABLE dtb_order (
    order_id number(9) NOT NULL,
    order_temp_id varchar2(4000),
    customer_id number(9) NOT NULL,
    message varchar2(4000),
    order_name01 varchar2(4000),
    order_name02 varchar2(4000),
    order_kana01 varchar2(4000),
    order_kana02 varchar2(4000),
    order_email varchar2(4000),
    order_tel01 varchar2(4000),
    order_tel02 varchar2(4000),
    order_tel03 varchar2(4000),
    order_fax01 varchar2(4000),
    order_fax02 varchar2(4000),
    order_fax03 varchar2(4000),
    order_zip01 varchar2(4000),
    order_zip02 varchar2(4000),
    order_pref varchar2(4000),
    order_addr01 varchar2(4000),
    order_addr02 varchar2(4000),
    order_sex number(4),
    order_birth timestamp,
    order_job number(9),
    deliv_name01 varchar2(4000),
    deliv_name02 varchar2(4000),
    deliv_kana01 varchar2(4000),
    deliv_kana02 varchar2(4000),
    deliv_tel01 varchar2(4000),
    deliv_tel02 varchar2(4000),
    deliv_tel03 varchar2(4000),
    deliv_fax01 varchar2(4000),
    deliv_fax02 varchar2(4000),
    deliv_fax03 varchar2(4000),
    deliv_zip01 varchar2(4000),
    deliv_zip02 varchar2(4000),
    deliv_pref varchar2(4000),
    deliv_addr01 varchar2(4000),
    deliv_addr02 varchar2(4000),
    subtotal number(9),
    discount number(9),
    deliv_fee number(9),
    charge number(9),
    use_point number(9),
    add_point number(9),
    birth_point number(9) ,
    tax number(9),
    total number(9),
    payment_total number(9),
    payment_id number(9),
    payment_method varchar2(4000),
    deliv_id number(9),
    deliv_time_id number(9),
    deliv_time varchar2(4000),
    deliv_no varchar2(4000),
    note varchar2(4000),
    status number(4),
    create_date timestamp NOT NULL ,
    loan_result varchar2(4000),
    credit_result varchar2(4000),
    credit_msg varchar2(4000),
    update_date timestamp,
    commit_date timestamp,
    del_flg  number(4) NOT NULL ,
    deliv_date varchar2(4000),
    conveni_data varchar2(4000),
    cell01 varchar2(4000),
    cell02 varchar2(4000),
    cell03 varchar2(4000),
    memo01 varchar2(4000),
    memo02 varchar2(4000),
    memo03 varchar2(4000),
    memo04 varchar2(4000),
    memo05 varchar2(4000),
    memo06 varchar2(4000),
    memo07 varchar2(4000),
    memo08 varchar2(4000),
    memo09 varchar2(4000),
    memo10 varchar2(4000),
    campaign_id number(9)
);

CREATE TABLE dtb_order_temp (
    order_temp_id varchar2(4000) NOT NULL,
    customer_id number(9) NOT NULL,
    message varchar2(4000),
    order_name01 varchar2(4000),
    order_name02 varchar2(4000),
    order_kana01 varchar2(4000),
    order_kana02 varchar2(4000),
    order_email varchar2(4000),
    order_tel01 varchar2(4000),
    order_tel02 varchar2(4000),
    order_tel03 varchar2(4000),
    order_fax01 varchar2(4000),
    order_fax02 varchar2(4000),
    order_fax03 varchar2(4000),
    order_zip01 varchar2(4000),
    order_zip02 varchar2(4000),
    order_pref varchar2(4000),
    order_addr01 varchar2(4000),
    order_addr02 varchar2(4000),
    order_sex number(4),
    order_birth timestamp,
    order_job number(9),
    deliv_name01 varchar2(4000),
    deliv_name02 varchar2(4000),
    deliv_kana01 varchar2(4000),
    deliv_kana02 varchar2(4000),
    deliv_tel01 varchar2(4000),
    deliv_tel02 varchar2(4000),
    deliv_tel03 varchar2(4000),
    deliv_fax01 varchar2(4000),
    deliv_fax02 varchar2(4000),
    deliv_fax03 varchar2(4000),
    deliv_zip01 varchar2(4000),
    deliv_zip02 varchar2(4000),
    deliv_pref varchar2(4000),
    deliv_addr01 varchar2(4000),
    deliv_addr02 varchar2(4000),
    subtotal number(9),
    discount number(9),
    deliv_fee number(9),
    charge number(9),
    use_point number(9),
    add_point number(9),
    birth_point number(9) ,
    tax number(9),
    total number(9),
    payment_total number(9),
    payment_id number(9),
    payment_method varchar2(4000),
    deliv_id number(9),
    deliv_time_id number(9),
    deliv_time varchar2(4000),
    deliv_no varchar2(4000),
    note varchar2(4000),
    mail_flag number(4),
    status number(4),
    deliv_check number(4),
    point_check number(4),
    loan_result varchar2(4000),
    credit_result varchar2(4000),
    credit_msg varchar2(4000),
    create_date timestamp NOT NULL ,
    update_date timestamp,
    del_flg  number(4) NOT NULL ,
    deliv_date varchar2(4000),
    conveni_data varchar2(4000),
    cell01 varchar2(4000),
    cell02 varchar2(4000),
    cell03 varchar2(4000),
    order_id number(9),
    memo01 varchar2(4000),
    memo02 varchar2(4000),
    memo03 varchar2(4000),
    memo04 varchar2(4000),
    memo05 varchar2(4000),
    memo06 varchar2(4000),
    memo07 varchar2(4000),
    memo08 varchar2(4000),
    memo09 varchar2(4000),
    memo10 varchar2(4000),
    order_session varchar2(4000)
);

CREATE TABLE dtb_other_deliv (
    other_deliv_id number(9) NOT NULL,
    customer_id number(9) NOT NULL,
    name01 varchar2(4000),
    name02 varchar2(4000),
    kana01 varchar2(4000),
    kana02 varchar2(4000),
    zip01 varchar2(4000),
    zip02 varchar2(4000),
    pref varchar2(4000),
    addr01 varchar2(4000),
    addr02 varchar2(4000),
    tel01 varchar2(4000),
    tel02 varchar2(4000),
    tel03 varchar2(4000)
);

CREATE TABLE dtb_order_detail (
    order_id number(9) NOT NULL,
    product_id number(9) NOT NULL,
    classcategory_id1 number(9) NOT NULL,
    classcategory_id2 number(9) NOT NULL,
    product_name varchar2(4000) NOT NULL,
    product_code varchar2(4000),
    classcategory_name1 varchar2(4000),
    classcategory_name2 varchar2(4000),
    price number(9),
    quantity number(9),
    point_rate number(9)
);

CREATE TABLE mtb_pref (
    pref_id number(4) NOT NULL,
    pref_name varchar2(4000),
    rank number(4) NOT NULL ,
    PRIMARY KEY (pref_id)
);

CREATE TABLE dtb_member (
    member_id number(9) NOT NULL,
    name varchar2(4000),
    department varchar2(4000),
    login_id varchar2(4000) NOT NULL,
    password varchar2(4000) NOT NULL,
    authority number(4) NOT NULL,
    rank number(9) NOT NULL ,
    work number(4) NOT NULL ,
    del_flg number(4) NOT NULL ,
    creator_id number(9) NOT NULL,
    update_date timestamp,
    create_date timestamp NOT NULL ,
    login_date timestamp
);

CREATE TABLE dtb_question (
    question_id number(9) NOT NULL,
    question_name varchar2(4000),
    question varchar2(4000),
    create_date timestamp NOT NULL ,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_question_result (
    result_id number(9) NOT NULL,
    question_id number(9) NOT NULL,
    question_date timestamp,
    question_name varchar2(4000),
    name01 varchar2(4000),
    name02 varchar2(4000),
    kana01 varchar2(4000),
    kana02 varchar2(4000),
    zip01 varchar2(4000),
    zip02 varchar2(4000),
    pref number(4),
    addr01 varchar2(4000),
    addr02 varchar2(4000),
    tel01 varchar2(4000),
    tel02 varchar2(4000),
    tel03 varchar2(4000),
    mail01 varchar2(4000),
    question01 varchar2(4000),
    question02 varchar2(4000),
    question03 varchar2(4000),
    question04 varchar2(4000),
    question05 varchar2(4000),
    question06 varchar2(4000),
    create_date timestamp NOT NULL ,
    del_flg  number(4) NOT NULL
);

CREATE TABLE dtb_bat_relate_products (
    product_id number(9),
    relate_product_id number(9),
    customer_id number(9),
    create_date timestamp
);

CREATE TABLE dtb_campaign (
    campaign_id number(9) NOT NULL,
    campaign_name varchar2(4000),
    campaign_point_rate number(9) NOT NULL,
    campaign_point_type number(4),
    start_date timestamp NOT NULL,
    end_date timestamp NOT NULL,
    directory_name varchar2(4000) NOT NULL,
    limit_count number(9) NOT NULL ,
    total_count number(9) NOT NULL ,
    orverlapping_flg number(4) NOT NULL ,
    cart_flg number(4) NOT NULL ,
    deliv_free_flg number(4) NOT NULL ,
    search_condition varchar2(4000),
    del_flg number(4) NOT NULL ,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_campaign_detail (
    campaign_id number(9) NOT NULL,
    product_id number(9) NOT NULL,
    campaign_point_rate number(9) NOT NULL
);

CREATE TABLE dtb_pagelayout (
    page_id number(9) NOT NULL,
    page_name varchar2(4000),
    url varchar2(4000) NOT NULL,
    php_dir varchar2(4000),
    tpl_dir varchar2(4000),
    filename varchar2(4000),
    header_chk number(4) ,
    footer_chk number(4) ,
    edit_flg number(4) ,
    author varchar2(4000),
    description varchar2(4000),
    keyword varchar2(4000),
    update_url varchar2(4000),
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_bloc (
    bloc_id number(9) NOT NULL,
    bloc_name varchar2(4000),
    tpl_path varchar2(4000),
    filename varchar2(4000) NOT NULL UNIQUE,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL ,
    php_path varchar2(4000),
    del_flg number(4) NOT NULL
);

CREATE TABLE dtb_blocposition (
    page_id number(9) NOT NULL,
    target_id number(9),
    bloc_id number(9),
    bloc_row number(9),
    filename varchar2(4000)
);

CREATE TABLE dtb_csv (
    no number(9),
    csv_id number(9) NOT NULL,
    col varchar2(4000),
    disp_name varchar2(4000),
    rank number(9),
    status number(4) NOT NULL ,
    create_date timestamp NOT NULL ,
    update_date timestamp NOT NULL
);

CREATE TABLE dtb_csv_sql (
    sql_id number(9),
    sql_name varchar2(4000) NOT NULL,
    csv_sql varchar2(4000),
    update_date timestamp NOT NULL ,
    create_date timestamp NOT NULL
);

CREATE TABLE dtb_user_regist (
    user_id number(9) NOT NULL,
    org_name varchar2(4000),
    post_name varchar2(4000),
    name01 varchar2(4000),
    name02 varchar2(4000),
    kana01 varchar2(4000),
    kana02 varchar2(4000),
    email varchar2(4000) NOT NULL,
    url varchar2(4000),
    note varchar2(4000),
    secret_key varchar2(4000) NOT NULL UNIQUE,
    status number(4) NOT NULL,
    del_flg number(4) ,
    create_date timestamp NOT NULL,
    update_date timestamp NOT NULL
);

create table dtb_templates
(
template_code        varchar2(4000)        NOT NULL UNIQUE    ,
template_name        varchar2(4000)            ,
create_date        timestamp        NOT NULL,
update_date        timestamp        NOT NULL
);

create table dtb_table_comment
(
id    number(9),
table_name    varchar2(4000),
column_name    varchar2(4000),
description    varchar2(4000)
);


#!/bin/bash

MAX_MEMBER_ID=$(psql -t -c 'select max(id) from dtb_member;')
MAX_TAG_ID=$(psql -t -c 'select max(id) from dtb_tag;')

while true
do
    psql -c "
delete from dtb_page_layout where page_id in (select id from dtb_page where create_date between now() - interval '24:00' and now() - interval '00:00:05');
delete from dtb_page where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_delivery_fee where delivery_id in (select id from dtb_delivery where create_date between now() - interval '24:00' and now() - interval '00:00:05');
delete from dtb_payment_option where delivery_id in (select id from dtb_delivery where create_date between now() - interval '24:00' and now() - interval '00:00:05');
delete from dtb_delivery where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_payment where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_block_position where layout_id in (select id from dtb_layout where create_date between now() - interval '24:00' and now() - interval '00:00:05');
delete from dtb_block where create_date between now() - interval '24:00' and now() - interval '00:00:06' and id not in (select distinct block_id from dtb_block_position);
delete from dtb_layout where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_category where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_class_category where class_name_id in (select id from dtb_class_name where create_date between now() - interval '24:00' and now() - interval '00:00:05');
delete from dtb_class_name where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_member where create_date between now() - interval '24:00' and now() - interval '00:00:06' and id > ${MAX_MEMBER_ID};
delete from dtb_template where create_date between now() - interval '24:00' and now() - interval '00:00:06';
delete from dtb_tag where id > ${MAX_TAG_ID};";
    sleep 5
done
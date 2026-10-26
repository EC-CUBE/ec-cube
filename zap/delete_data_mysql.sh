#!/bin/bash
#
# zap/delete_data.sh の MySQL 版。接続先は環境変数 (MYSQL_PWD など) と引数で渡す (zap/bin/start_cleanup.sh)。

MAX_MEMBER_ID=$(mysql -u dbuser eccubedb -N -B -e 'select max(id) from dtb_member;')
MAX_TAG_ID=$(mysql -u dbuser eccubedb -N -B -e 'select max(id) from dtb_tag;')
# dtb_tag には create_date が無いため、2 周前 (10 秒以上前) に既にあった ID までを消す。
# 作成直後に消すと、シナリオが作ったタグを編集・削除する前に消してしまう
SEEN_TAG_ID=${MAX_TAG_ID}
OLD_TAG_ID=${MAX_TAG_ID}

while true
do
    mysql -u dbuser eccubedb -e "
delete from dtb_page_layout where page_id in (select id from dtb_page where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE);
delete from dtb_page where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE;
delete from dtb_delivery_fee where delivery_id in (select id from dtb_delivery where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 2 HOUR);
delete from dtb_payment_option where delivery_id in (select id from dtb_delivery where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 2 HOUR);
delete from dtb_delivery where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 2 HOUR;
delete from dtb_payment where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE;
delete from dtb_block_position where layout_id in (select id from dtb_layout where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE);
delete from dtb_block where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE and id not in (select distinct block_id from dtb_block_position);
delete from dtb_layout where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 2 HOUR;
delete from dtb_category where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 1 HOUR;
delete from dtb_class_category where class_name_id in (select id from dtb_class_name where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 1 HOUR);
delete from dtb_class_name where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 1 HOUR;
delete from dtb_member where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 20 MINUTE and id > ${MAX_MEMBER_ID};
delete from dtb_template where create_date between now() - INTERVAL 24 HOUR and now() - INTERVAL 30 MINUTE;
delete from dtb_tag where id > ${MAX_TAG_ID} and id <= ${OLD_TAG_ID};";
    OLD_TAG_ID=${SEEN_TAG_ID}
    SEEN_TAG_ID=$(mysql -u dbuser eccubedb -N -B -e 'select coalesce(max(id), 0) from dtb_tag;')
    sleep 5
done
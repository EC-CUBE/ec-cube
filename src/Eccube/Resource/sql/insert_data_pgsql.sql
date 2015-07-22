BEGIN;

CREATE TABLE IF NOT EXISTS doctrine_migration_versions(
  version varchar(255) NOT NULL
);

INSERT INTO doctrine_migration_versions (version) VALUES('20150602223925');
INSERT INTO doctrine_migration_versions (version) VALUES('20150612152108');
INSERT INTO doctrine_migration_versions (version) VALUES('20150716105942');
INSERT INTO doctrine_migration_versions (version) VALUES('20150716110252');
INSERT INTO doctrine_migration_versions (version) VALUES('20150716110827');
INSERT INTO doctrine_migration_versions (version) VALUES('20150716110834');

INSERT INTO mtb_authority (id, name, rank) VALUES (0, 'システム管理者', 0);
INSERT INTO mtb_authority (id, name, rank) VALUES (1, '店舗オーナー', 1);

INSERT INTO mtb_db (id, name, rank) VALUES (1, 'PostgreSQL', 0);
INSERT INTO mtb_db (id, name, rank) VALUES (2, 'MySQL', 1);

INSERT INTO mtb_disp (id, name, rank) VALUES (1, '公開', 0);
INSERT INTO mtb_disp (id, name, rank) VALUES (2, '非公開', 1);

INSERT INTO mtb_product_type (id, name, rank) VALUES (1, '商品種別A', 0);
INSERT INTO mtb_product_type (id, name, rank) VALUES (2, '商品種別B', 1);

INSERT INTO mtb_device_type (id, name, rank) VALUES (1, 'モバイル', 0);
INSERT INTO mtb_device_type (id, name, rank) VALUES (2, 'スマートフォン', 1);
INSERT INTO mtb_device_type (id, name, rank) VALUES (10, 'PC', 2);
INSERT INTO mtb_device_type (id, name, rank) VALUES (99, '管理画面', 3);

INSERT INTO mtb_job (id, name, rank) VALUES (1, '公務員', 0);
INSERT INTO mtb_job (id, name, rank) VALUES (2, 'コンサルタント', 1);
INSERT INTO mtb_job (id, name, rank) VALUES (3, 'コンピューター関連技術職', 2);
INSERT INTO mtb_job (id, name, rank) VALUES (4, 'コンピューター関連以外の技術職', 3);
INSERT INTO mtb_job (id, name, rank) VALUES (5, '金融関係', 4);
INSERT INTO mtb_job (id, name, rank) VALUES (6, '医師', 5);
INSERT INTO mtb_job (id, name, rank) VALUES (7, '弁護士', 6);
INSERT INTO mtb_job (id, name, rank) VALUES (8, '総務・人事・事務', 7);
INSERT INTO mtb_job (id, name, rank) VALUES (9, '営業・販売', 8);
INSERT INTO mtb_job (id, name, rank) VALUES (10, '研究・開発', 9);
INSERT INTO mtb_job (id, name, rank) VALUES (11, '広報・宣伝', 10);
INSERT INTO mtb_job (id, name, rank) VALUES (12, '企画・マーケティング', 11);
INSERT INTO mtb_job (id, name, rank) VALUES (13, 'デザイン関係', 12);
INSERT INTO mtb_job (id, name, rank) VALUES (14, '会社経営・役員', 13);
INSERT INTO mtb_job (id, name, rank) VALUES (15, '出版・マスコミ関係', 14);
INSERT INTO mtb_job (id, name, rank) VALUES (16, '学生・フリーター', 15);
INSERT INTO mtb_job (id, name, rank) VALUES (17, '主婦', 16);
INSERT INTO mtb_job (id, name, rank) VALUES (18, 'その他', 17);

INSERT INTO mtb_order_status (id, name, rank) VALUES (7, '決済処理中', 0);
INSERT INTO mtb_order_status (id, name, rank) VALUES (1, '新規受付', 1);
INSERT INTO mtb_order_status (id, name, rank) VALUES (2, '入金待ち', 2);
INSERT INTO mtb_order_status (id, name, rank) VALUES (6, '入金済み', 3);
INSERT INTO mtb_order_status (id, name, rank) VALUES (3, 'キャンセル', 4);
INSERT INTO mtb_order_status (id, name, rank) VALUES (4, '取り寄せ中', 5);
INSERT INTO mtb_order_status (id, name, rank) VALUES (5, '発送済み', 6);
INSERT INTO mtb_order_status (id, name, rank) VALUES (8, '購入処理中', 7);


INSERT INTO mtb_order_status_color (id, name, rank) VALUES (1, '#FFFFFF', 0);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (2, '#FFDE9B', 1);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (3, '#C9C9C9', 2);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (4, '#FFD9D9', 3);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (5, '#BFDFFF', 4);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (6, '#FFFFAB', 5);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (7, '#FFCCCC', 6);

INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (7, '注文未完了', 0);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (1, '注文受付', 1);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (2, '入金待ち', 2);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (6, '注文受付', 3);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (3, 'キャンセル', 4);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (4, '注文受付', 5);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (5, '発送済み', 6);
INSERT INTO mtb_customer_order_status (id, name, rank) VALUES (8, '注文未完了', 7);

INSERT INTO mtb_page_max (id, name, rank) VALUES (10, '10', 0);
INSERT INTO mtb_page_max (id, name, rank) VALUES (20, '20', 1);
INSERT INTO mtb_page_max (id, name, rank) VALUES (30, '30', 2);
INSERT INTO mtb_page_max (id, name, rank) VALUES (40, '40', 3);
INSERT INTO mtb_page_max (id, name, rank) VALUES (50, '50', 4);
INSERT INTO mtb_page_max (id, name, rank) VALUES (60, '60', 5);
INSERT INTO mtb_page_max (id, name, rank) VALUES (70, '70', 6);
INSERT INTO mtb_page_max (id, name, rank) VALUES (80, '80', 7);
INSERT INTO mtb_page_max (id, name, rank) VALUES (90, '90', 8);
INSERT INTO mtb_page_max (id, name, rank) VALUES (100, '100', 9);

INSERT INTO mtb_pref (id, name, rank) VALUES (1, '北海道', 1);
INSERT INTO mtb_pref (id, name, rank) VALUES (2, '青森県', 2);
INSERT INTO mtb_pref (id, name, rank) VALUES (3, '岩手県', 3);
INSERT INTO mtb_pref (id, name, rank) VALUES (4, '宮城県', 4);
INSERT INTO mtb_pref (id, name, rank) VALUES (5, '秋田県', 5);
INSERT INTO mtb_pref (id, name, rank) VALUES (6, '山形県', 6);
INSERT INTO mtb_pref (id, name, rank) VALUES (7, '福島県', 7);
INSERT INTO mtb_pref (id, name, rank) VALUES (8, '茨城県', 8);
INSERT INTO mtb_pref (id, name, rank) VALUES (9, '栃木県', 9);
INSERT INTO mtb_pref (id, name, rank) VALUES (10, '群馬県', 10);
INSERT INTO mtb_pref (id, name, rank) VALUES (11, '埼玉県', 11);
INSERT INTO mtb_pref (id, name, rank) VALUES (12, '千葉県', 12);
INSERT INTO mtb_pref (id, name, rank) VALUES (13, '東京都', 13);
INSERT INTO mtb_pref (id, name, rank) VALUES (14, '神奈川県', 14);
INSERT INTO mtb_pref (id, name, rank) VALUES (15, '新潟県', 15);
INSERT INTO mtb_pref (id, name, rank) VALUES (16, '富山県', 16);
INSERT INTO mtb_pref (id, name, rank) VALUES (17, '石川県', 17);
INSERT INTO mtb_pref (id, name, rank) VALUES (18, '福井県', 18);
INSERT INTO mtb_pref (id, name, rank) VALUES (19, '山梨県', 19);
INSERT INTO mtb_pref (id, name, rank) VALUES (20, '長野県', 20);
INSERT INTO mtb_pref (id, name, rank) VALUES (21, '岐阜県', 21);
INSERT INTO mtb_pref (id, name, rank) VALUES (22, '静岡県', 22);
INSERT INTO mtb_pref (id, name, rank) VALUES (23, '愛知県', 23);
INSERT INTO mtb_pref (id, name, rank) VALUES (24, '三重県', 24);
INSERT INTO mtb_pref (id, name, rank) VALUES (25, '滋賀県', 25);
INSERT INTO mtb_pref (id, name, rank) VALUES (26, '京都府', 26);
INSERT INTO mtb_pref (id, name, rank) VALUES (27, '大阪府', 27);
INSERT INTO mtb_pref (id, name, rank) VALUES (28, '兵庫県', 28);
INSERT INTO mtb_pref (id, name, rank) VALUES (29, '奈良県', 29);
INSERT INTO mtb_pref (id, name, rank) VALUES (30, '和歌山県', 30);
INSERT INTO mtb_pref (id, name, rank) VALUES (31, '鳥取県', 31);
INSERT INTO mtb_pref (id, name, rank) VALUES (32, '島根県', 32);
INSERT INTO mtb_pref (id, name, rank) VALUES (33, '岡山県', 33);
INSERT INTO mtb_pref (id, name, rank) VALUES (34, '広島県', 34);
INSERT INTO mtb_pref (id, name, rank) VALUES (35, '山口県', 35);
INSERT INTO mtb_pref (id, name, rank) VALUES (36, '徳島県', 36);
INSERT INTO mtb_pref (id, name, rank) VALUES (37, '香川県', 37);
INSERT INTO mtb_pref (id, name, rank) VALUES (38, '愛媛県', 38);
INSERT INTO mtb_pref (id, name, rank) VALUES (39, '高知県', 39);
INSERT INTO mtb_pref (id, name, rank) VALUES (40, '福岡県', 40);
INSERT INTO mtb_pref (id, name, rank) VALUES (41, '佐賀県', 41);
INSERT INTO mtb_pref (id, name, rank) VALUES (42, '長崎県', 42);
INSERT INTO mtb_pref (id, name, rank) VALUES (43, '熊本県', 43);
INSERT INTO mtb_pref (id, name, rank) VALUES (44, '大分県', 44);
INSERT INTO mtb_pref (id, name, rank) VALUES (45, '宮崎県', 45);
INSERT INTO mtb_pref (id, name, rank) VALUES (46, '鹿児島県', 46);
INSERT INTO mtb_pref (id, name, rank) VALUES (47, '沖縄県', 47);

INSERT INTO mtb_product_list_max (id, name, rank) VALUES (15, '15件', 0);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (30, '30件', 1);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (50, '50件', 2);

INSERT INTO mtb_product_list_order_by (id, name, rank) VALUES (1, '価格順', 0);
INSERT INTO mtb_product_list_order_by (id, name, rank) VALUES (2, '新着順', 1);

INSERT INTO mtb_sex (id, name, rank) VALUES (1, '男性', 0);
INSERT INTO mtb_sex (id, name, rank) VALUES (2, '女性', 1);

INSERT INTO mtb_customer_status (id, name, rank) VALUES (1, '仮会員', 0);
INSERT INTO mtb_customer_status (id, name, rank) VALUES (2, '本会員', 1);

INSERT INTO mtb_taxrule (id, name, rank) VALUES (1, '四捨五入', 0);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (2, '切り捨て', 1);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (3, '切り上げ', 2);

INSERT INTO mtb_work (id, name, rank) VALUES (0, '非稼働', 0);
INSERT INTO mtb_work (id, name, rank) VALUES (1, '稼働', 1);

INSERT INTO dtb_member (member_id, name, department, login_id, password, salt, authority, rank, work, del_flg, creator_id, update_date, create_date, login_date) VALUES (1, 'dummy', NULL, 'dummy', 'dummy', 'dummy', 0, 0, 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
SELECT setval('dtb_member_member_id_seq', 2);

INSERT INTO dtb_tax_rule (tax_rule_id, apply_date, calc_rule, tax_rate, tax_adjust, creator_id, del_flg, create_date, update_date) VALUES (1, CURRENT_TIMESTAMP, 1, 8, 0, 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_tax_rule_tax_rule_id_seq', 1);

INSERT INTO mtb_country (id, name, rank) VALUES (352,'アイスランド',1);
INSERT INTO mtb_country (id, name, rank) VALUES (372,'アイルランド',2);
INSERT INTO mtb_country (id, name, rank) VALUES (31,'アゼルバイジャン',3);
INSERT INTO mtb_country (id, name, rank) VALUES (4,'アフガニスタン',4);
INSERT INTO mtb_country (id, name, rank) VALUES (840,'アメリカ合衆国',5);
INSERT INTO mtb_country (id, name, rank) VALUES (850,'アメリカ領ヴァージン諸島',6);
INSERT INTO mtb_country (id, name, rank) VALUES (16,'アメリカ領サモア',7);
INSERT INTO mtb_country (id, name, rank) VALUES (784,'アラブ首長国連邦',8);
INSERT INTO mtb_country (id, name, rank) VALUES (12,'アルジェリア',9);
INSERT INTO mtb_country (id, name, rank) VALUES (32,'アルゼンチン',10);
INSERT INTO mtb_country (id, name, rank) VALUES (533,'アルバ',11);
INSERT INTO mtb_country (id, name, rank) VALUES (8,'アルバニア',12);
INSERT INTO mtb_country (id, name, rank) VALUES (51,'アルメニア',13);
INSERT INTO mtb_country (id, name, rank) VALUES (660,'アンギラ',14);
INSERT INTO mtb_country (id, name, rank) VALUES (24,'アンゴラ',15);
INSERT INTO mtb_country (id, name, rank) VALUES (28,'アンティグア・バーブーダ',16);
INSERT INTO mtb_country (id, name, rank) VALUES (20,'アンドラ',17);
INSERT INTO mtb_country (id, name, rank) VALUES (887,'イエメン',18);
INSERT INTO mtb_country (id, name, rank) VALUES (826,'イギリス',19);
INSERT INTO mtb_country (id, name, rank) VALUES (86,'イギリス領インド洋地域',20);
INSERT INTO mtb_country (id, name, rank) VALUES (92,'イギリス領ヴァージン諸島',21);
INSERT INTO mtb_country (id, name, rank) VALUES (376,'イスラエル',22);
INSERT INTO mtb_country (id, name, rank) VALUES (380,'イタリア',23);
INSERT INTO mtb_country (id, name, rank) VALUES (368,'イラク',24);
INSERT INTO mtb_country (id, name, rank) VALUES (364,'イラン|イラン・イスラム共和国',25);
INSERT INTO mtb_country (id, name, rank) VALUES (356,'インド',26);
INSERT INTO mtb_country (id, name, rank) VALUES (360,'インドネシア',27);
INSERT INTO mtb_country (id, name, rank) VALUES (876,'ウォリス・フツナ',28);
INSERT INTO mtb_country (id, name, rank) VALUES (800,'ウガンダ',29);
INSERT INTO mtb_country (id, name, rank) VALUES (804,'ウクライナ',30);
INSERT INTO mtb_country (id, name, rank) VALUES (860,'ウズベキスタン',31);
INSERT INTO mtb_country (id, name, rank) VALUES (858,'ウルグアイ',32);
INSERT INTO mtb_country (id, name, rank) VALUES (218,'エクアドル',33);
INSERT INTO mtb_country (id, name, rank) VALUES (818,'エジプト',34);
INSERT INTO mtb_country (id, name, rank) VALUES (233,'エストニア',35);
INSERT INTO mtb_country (id, name, rank) VALUES (231,'エチオピア',36);
INSERT INTO mtb_country (id, name, rank) VALUES (232,'エリトリア',37);
INSERT INTO mtb_country (id, name, rank) VALUES (222,'エルサルバドル',38);
INSERT INTO mtb_country (id, name, rank) VALUES (36,'オーストラリア',39);
INSERT INTO mtb_country (id, name, rank) VALUES (40,'オーストリア',40);
INSERT INTO mtb_country (id, name, rank) VALUES (248,'オーランド諸島',41);
INSERT INTO mtb_country (id, name, rank) VALUES (512,'オマーン',42);
INSERT INTO mtb_country (id, name, rank) VALUES (528,'オランダ',43);
INSERT INTO mtb_country (id, name, rank) VALUES (288,'ガーナ',44);
INSERT INTO mtb_country (id, name, rank) VALUES (132,'カーボベルデ',45);
INSERT INTO mtb_country (id, name, rank) VALUES (831,'ガーンジー',46);
INSERT INTO mtb_country (id, name, rank) VALUES (328,'ガイアナ',47);
INSERT INTO mtb_country (id, name, rank) VALUES (398,'カザフスタン',48);
INSERT INTO mtb_country (id, name, rank) VALUES (634,'カタール',49);
INSERT INTO mtb_country (id, name, rank) VALUES (581,'合衆国領有小離島',50);
INSERT INTO mtb_country (id, name, rank) VALUES (124,'カナダ',51);
INSERT INTO mtb_country (id, name, rank) VALUES (266,'ガボン',52);
INSERT INTO mtb_country (id, name, rank) VALUES (120,'カメルーン',53);
INSERT INTO mtb_country (id, name, rank) VALUES (270,'ガンビア',54);
INSERT INTO mtb_country (id, name, rank) VALUES (116,'カンボジア',55);
INSERT INTO mtb_country (id, name, rank) VALUES (580,'北マリアナ諸島',56);
INSERT INTO mtb_country (id, name, rank) VALUES (324,'ギニア',57);
INSERT INTO mtb_country (id, name, rank) VALUES (624,'ギニアビサウ',58);
INSERT INTO mtb_country (id, name, rank) VALUES (196,'キプロス',59);
INSERT INTO mtb_country (id, name, rank) VALUES (192,'キューバ',60);
INSERT INTO mtb_country (id, name, rank) VALUES (531,'キュラソー島|キュラソー',61);
INSERT INTO mtb_country (id, name, rank) VALUES (300,'ギリシャ',62);
INSERT INTO mtb_country (id, name, rank) VALUES (296,'キリバス',63);
INSERT INTO mtb_country (id, name, rank) VALUES (417,'キルギス',64);
INSERT INTO mtb_country (id, name, rank) VALUES (320,'グアテマラ',65);
INSERT INTO mtb_country (id, name, rank) VALUES (312,'グアドループ',66);
INSERT INTO mtb_country (id, name, rank) VALUES (316,'グアム',67);
INSERT INTO mtb_country (id, name, rank) VALUES (414,'クウェート',68);
INSERT INTO mtb_country (id, name, rank) VALUES (184,'クック諸島',69);
INSERT INTO mtb_country (id, name, rank) VALUES (304,'グリーンランド',70);
INSERT INTO mtb_country (id, name, rank) VALUES (162,'クリスマス島 (オーストラリア)|クリスマス島',71);
INSERT INTO mtb_country (id, name, rank) VALUES (268,'グルジア',72);
INSERT INTO mtb_country (id, name, rank) VALUES (308,'グレナダ',73);
INSERT INTO mtb_country (id, name, rank) VALUES (191,'クロアチア',74);
INSERT INTO mtb_country (id, name, rank) VALUES (136,'ケイマン諸島',75);
INSERT INTO mtb_country (id, name, rank) VALUES (404,'ケニア',76);
INSERT INTO mtb_country (id, name, rank) VALUES (384,'コートジボワール',77);
INSERT INTO mtb_country (id, name, rank) VALUES (166,'ココス諸島|ココス（キーリング）諸島',78);
INSERT INTO mtb_country (id, name, rank) VALUES (188,'コスタリカ',79);
INSERT INTO mtb_country (id, name, rank) VALUES (174,'コモロ',80);
INSERT INTO mtb_country (id, name, rank) VALUES (170,'コロンビア',81);
INSERT INTO mtb_country (id, name, rank) VALUES (178,'コンゴ共和国',82);
INSERT INTO mtb_country (id, name, rank) VALUES (180,'コンゴ民主共和国',83);
INSERT INTO mtb_country (id, name, rank) VALUES (682,'サウジアラビア',84);
INSERT INTO mtb_country (id, name, rank) VALUES (239,'サウスジョージア・サウスサンドウィッチ諸島',85);
INSERT INTO mtb_country (id, name, rank) VALUES (882,'サモア',86);
INSERT INTO mtb_country (id, name, rank) VALUES (678,'サントメ・プリンシペ',87);
INSERT INTO mtb_country (id, name, rank) VALUES (652,'サン・バルテルミー島|サン・バルテルミー',88);
INSERT INTO mtb_country (id, name, rank) VALUES (894,'ザンビア',89);
INSERT INTO mtb_country (id, name, rank) VALUES (666,'サンピエール島・ミクロン島',90);
INSERT INTO mtb_country (id, name, rank) VALUES (674,'サンマリノ',91);
INSERT INTO mtb_country (id, name, rank) VALUES (663,'サン・マルタン (西インド諸島)|サン・マルタン（フランス領）',92);
INSERT INTO mtb_country (id, name, rank) VALUES (694,'シエラレオネ',93);
INSERT INTO mtb_country (id, name, rank) VALUES (262,'ジブチ',94);
INSERT INTO mtb_country (id, name, rank) VALUES (292,'ジブラルタル',95);
INSERT INTO mtb_country (id, name, rank) VALUES (832,'ジャージー',96);
INSERT INTO mtb_country (id, name, rank) VALUES (388,'ジャマイカ',97);
INSERT INTO mtb_country (id, name, rank) VALUES (760,'シリア|シリア・アラブ共和国',98);
INSERT INTO mtb_country (id, name, rank) VALUES (702,'シンガポール',99);
INSERT INTO mtb_country (id, name, rank) VALUES (534,'シント・マールテン|シント・マールテン（オランダ領）',100);
INSERT INTO mtb_country (id, name, rank) VALUES (716,'ジンバブエ',101);
INSERT INTO mtb_country (id, name, rank) VALUES (756,'スイス',102);
INSERT INTO mtb_country (id, name, rank) VALUES (752,'スウェーデン',103);
INSERT INTO mtb_country (id, name, rank) VALUES (729,'スーダン',104);
INSERT INTO mtb_country (id, name, rank) VALUES (744,'スヴァールバル諸島およびヤンマイエン島',105);
INSERT INTO mtb_country (id, name, rank) VALUES (724,'スペイン',106);
INSERT INTO mtb_country (id, name, rank) VALUES (740,'スリナム',107);
INSERT INTO mtb_country (id, name, rank) VALUES (144,'スリランカ',108);
INSERT INTO mtb_country (id, name, rank) VALUES (703,'スロバキア',109);
INSERT INTO mtb_country (id, name, rank) VALUES (705,'スロベニア',110);
INSERT INTO mtb_country (id, name, rank) VALUES (748,'スワジランド',111);
INSERT INTO mtb_country (id, name, rank) VALUES (690,'セーシェル',112);
INSERT INTO mtb_country (id, name, rank) VALUES (226,'赤道ギニア',113);
INSERT INTO mtb_country (id, name, rank) VALUES (686,'セネガル',114);
INSERT INTO mtb_country (id, name, rank) VALUES (688,'セルビア',115);
INSERT INTO mtb_country (id, name, rank) VALUES (659,'セントクリストファー・ネイビス',116);
INSERT INTO mtb_country (id, name, rank) VALUES (670,'セントビンセント・グレナディーン|セントビンセントおよびグレナディーン諸島',117);
INSERT INTO mtb_country (id, name, rank) VALUES (654,'セントヘレナ・アセンションおよびトリスタンダクーニャ',118);
INSERT INTO mtb_country (id, name, rank) VALUES (662,'セントルシア',119);
INSERT INTO mtb_country (id, name, rank) VALUES (706,'ソマリア',120);
INSERT INTO mtb_country (id, name, rank) VALUES (90,'ソロモン諸島',121);
INSERT INTO mtb_country (id, name, rank) VALUES (796,'タークス・カイコス諸島',122);
INSERT INTO mtb_country (id, name, rank) VALUES (764,'タイ王国|タイ',123);
INSERT INTO mtb_country (id, name, rank) VALUES (410,'大韓民国',124);
INSERT INTO mtb_country (id, name, rank) VALUES (158,'台湾',125);
INSERT INTO mtb_country (id, name, rank) VALUES (762,'タジキスタン',126);
INSERT INTO mtb_country (id, name, rank) VALUES (834,'タンザニア',127);
INSERT INTO mtb_country (id, name, rank) VALUES (203,'チェコ',128);
INSERT INTO mtb_country (id, name, rank) VALUES (148,'チャド',129);
INSERT INTO mtb_country (id, name, rank) VALUES (140,'中央アフリカ共和国',130);
INSERT INTO mtb_country (id, name, rank) VALUES (156,'中華人民共和国|中国',131);
INSERT INTO mtb_country (id, name, rank) VALUES (788,'チュニジア',132);
INSERT INTO mtb_country (id, name, rank) VALUES (408,'朝鮮民主主義人民共和国',133);
INSERT INTO mtb_country (id, name, rank) VALUES (152,'チリ',134);
INSERT INTO mtb_country (id, name, rank) VALUES (798,'ツバル',135);
INSERT INTO mtb_country (id, name, rank) VALUES (208,'デンマーク',136);
INSERT INTO mtb_country (id, name, rank) VALUES (276,'ドイツ',137);
INSERT INTO mtb_country (id, name, rank) VALUES (768,'トーゴ',138);
INSERT INTO mtb_country (id, name, rank) VALUES (772,'トケラウ',139);
INSERT INTO mtb_country (id, name, rank) VALUES (214,'ドミニカ共和国',140);
INSERT INTO mtb_country (id, name, rank) VALUES (212,'ドミニカ国',141);
INSERT INTO mtb_country (id, name, rank) VALUES (780,'トリニダード・トバゴ',142);
INSERT INTO mtb_country (id, name, rank) VALUES (795,'トルクメニスタン',143);
INSERT INTO mtb_country (id, name, rank) VALUES (792,'トルコ',144);
INSERT INTO mtb_country (id, name, rank) VALUES (776,'トンガ',145);
INSERT INTO mtb_country (id, name, rank) VALUES (566,'ナイジェリア',146);
INSERT INTO mtb_country (id, name, rank) VALUES (520,'ナウル',147);
INSERT INTO mtb_country (id, name, rank) VALUES (516,'ナミビア',148);
INSERT INTO mtb_country (id, name, rank) VALUES (10,'南極',149);
INSERT INTO mtb_country (id, name, rank) VALUES (570,'ニウエ',150);
INSERT INTO mtb_country (id, name, rank) VALUES (558,'ニカラグア',151);
INSERT INTO mtb_country (id, name, rank) VALUES (562,'ニジェール',152);
INSERT INTO mtb_country (id, name, rank) VALUES (392,'日本',153);
INSERT INTO mtb_country (id, name, rank) VALUES (732,'西サハラ',154);
INSERT INTO mtb_country (id, name, rank) VALUES (540,'ニューカレドニア',155);
INSERT INTO mtb_country (id, name, rank) VALUES (554,'ニュージーランド',156);
INSERT INTO mtb_country (id, name, rank) VALUES (524,'ネパール',157);
INSERT INTO mtb_country (id, name, rank) VALUES (574,'ノーフォーク島',158);
INSERT INTO mtb_country (id, name, rank) VALUES (578,'ノルウェー',159);
INSERT INTO mtb_country (id, name, rank) VALUES (334,'ハード島とマクドナルド諸島',160);
INSERT INTO mtb_country (id, name, rank) VALUES (48,'バーレーン',161);
INSERT INTO mtb_country (id, name, rank) VALUES (332,'ハイチ',162);
INSERT INTO mtb_country (id, name, rank) VALUES (586,'パキスタン',163);
INSERT INTO mtb_country (id, name, rank) VALUES (336,'バチカン|バチカン市国',164);
INSERT INTO mtb_country (id, name, rank) VALUES (591,'パナマ',165);
INSERT INTO mtb_country (id, name, rank) VALUES (548,'バヌアツ',166);
INSERT INTO mtb_country (id, name, rank) VALUES (44,'バハマ',167);
INSERT INTO mtb_country (id, name, rank) VALUES (598,'パプアニューギニア',168);
INSERT INTO mtb_country (id, name, rank) VALUES (60,'バミューダ諸島|バミューダ',169);
INSERT INTO mtb_country (id, name, rank) VALUES (585,'パラオ',170);
INSERT INTO mtb_country (id, name, rank) VALUES (600,'パラグアイ',171);
INSERT INTO mtb_country (id, name, rank) VALUES (52,'バルバドス',172);
INSERT INTO mtb_country (id, name, rank) VALUES (275,'パレスチナ',173);
INSERT INTO mtb_country (id, name, rank) VALUES (348,'ハンガリー',174);
INSERT INTO mtb_country (id, name, rank) VALUES (50,'バングラデシュ',175);
INSERT INTO mtb_country (id, name, rank) VALUES (626,'東ティモール',176);
INSERT INTO mtb_country (id, name, rank) VALUES (612,'ピトケアン諸島|ピトケアン',177);
INSERT INTO mtb_country (id, name, rank) VALUES (242,'フィジー',178);
INSERT INTO mtb_country (id, name, rank) VALUES (608,'フィリピン',179);
INSERT INTO mtb_country (id, name, rank) VALUES (246,'フィンランド',180);
INSERT INTO mtb_country (id, name, rank) VALUES (64,'ブータン',181);
INSERT INTO mtb_country (id, name, rank) VALUES (74,'ブーベ島',182);
INSERT INTO mtb_country (id, name, rank) VALUES (630,'プエルトリコ',183);
INSERT INTO mtb_country (id, name, rank) VALUES (234,'フェロー諸島',184);
INSERT INTO mtb_country (id, name, rank) VALUES (238,'フォークランド諸島|フォークランド（マルビナス）諸島',185);
INSERT INTO mtb_country (id, name, rank) VALUES (76,'ブラジル',186);
INSERT INTO mtb_country (id, name, rank) VALUES (250,'フランス',187);
INSERT INTO mtb_country (id, name, rank) VALUES (254,'フランス領ギアナ',188);
INSERT INTO mtb_country (id, name, rank) VALUES (258,'フランス領ポリネシア',189);
INSERT INTO mtb_country (id, name, rank) VALUES (260,'フランス領南方・南極地域',190);
INSERT INTO mtb_country (id, name, rank) VALUES (100,'ブルガリア',191);
INSERT INTO mtb_country (id, name, rank) VALUES (854,'ブルキナファソ',192);
INSERT INTO mtb_country (id, name, rank) VALUES (96,'ブルネイ|ブルネイ・ダルサラーム',193);
INSERT INTO mtb_country (id, name, rank) VALUES (108,'ブルンジ',194);
INSERT INTO mtb_country (id, name, rank) VALUES (704,'ベトナム',195);
INSERT INTO mtb_country (id, name, rank) VALUES (204,'ベナン',196);
INSERT INTO mtb_country (id, name, rank) VALUES (862,'ベネズエラ|ベネズエラ・ボリバル共和国',197);
INSERT INTO mtb_country (id, name, rank) VALUES (112,'ベラルーシ',198);
INSERT INTO mtb_country (id, name, rank) VALUES (84,'ベリーズ',199);
INSERT INTO mtb_country (id, name, rank) VALUES (604,'ペルー',200);
INSERT INTO mtb_country (id, name, rank) VALUES (56,'ベルギー',201);
INSERT INTO mtb_country (id, name, rank) VALUES (616,'ポーランド',202);
INSERT INTO mtb_country (id, name, rank) VALUES (70,'ボスニア・ヘルツェゴビナ',203);
INSERT INTO mtb_country (id, name, rank) VALUES (72,'ボツワナ',204);
INSERT INTO mtb_country (id, name, rank) VALUES (535,'BES諸島|ボネール、シント・ユースタティウスおよびサバ',205);
INSERT INTO mtb_country (id, name, rank) VALUES (68,'ボリビア|ボリビア多民族国',206);
INSERT INTO mtb_country (id, name, rank) VALUES (620,'ポルトガル',207);
INSERT INTO mtb_country (id, name, rank) VALUES (344,'香港',208);
INSERT INTO mtb_country (id, name, rank) VALUES (340,'ホンジュラス',209);
INSERT INTO mtb_country (id, name, rank) VALUES (584,'マーシャル諸島',210);
INSERT INTO mtb_country (id, name, rank) VALUES (446,'マカオ',211);
INSERT INTO mtb_country (id, name, rank) VALUES (807,'マケドニア共和国|マケドニア旧ユーゴスラビア共和国',212);
INSERT INTO mtb_country (id, name, rank) VALUES (450,'マダガスカル',213);
INSERT INTO mtb_country (id, name, rank) VALUES (175,'マヨット',214);
INSERT INTO mtb_country (id, name, rank) VALUES (454,'マラウイ',215);
INSERT INTO mtb_country (id, name, rank) VALUES (466,'マリ共和国|マリ',216);
INSERT INTO mtb_country (id, name, rank) VALUES (470,'マルタ',217);
INSERT INTO mtb_country (id, name, rank) VALUES (474,'マルティニーク',218);
INSERT INTO mtb_country (id, name, rank) VALUES (458,'マレーシア',219);
INSERT INTO mtb_country (id, name, rank) VALUES (833,'マン島',220);
INSERT INTO mtb_country (id, name, rank) VALUES (583,'ミクロネシア連邦',221);
INSERT INTO mtb_country (id, name, rank) VALUES (710,'南アフリカ共和国|南アフリカ',222);
INSERT INTO mtb_country (id, name, rank) VALUES (728,'南スーダン',223);
INSERT INTO mtb_country (id, name, rank) VALUES (104,'ミャンマー',224);
INSERT INTO mtb_country (id, name, rank) VALUES (484,'メキシコ',225);
INSERT INTO mtb_country (id, name, rank) VALUES (480,'モーリシャス',226);
INSERT INTO mtb_country (id, name, rank) VALUES (478,'モーリタニア',227);
INSERT INTO mtb_country (id, name, rank) VALUES (508,'モザンビーク',228);
INSERT INTO mtb_country (id, name, rank) VALUES (492,'モナコ',229);
INSERT INTO mtb_country (id, name, rank) VALUES (462,'モルディブ',230);
INSERT INTO mtb_country (id, name, rank) VALUES (498,'モルドバ|モルドバ共和国',231);
INSERT INTO mtb_country (id, name, rank) VALUES (504,'モロッコ',232);
INSERT INTO mtb_country (id, name, rank) VALUES (496,'モンゴル国|モンゴル',233);
INSERT INTO mtb_country (id, name, rank) VALUES (499,'モンテネグロ',234);
INSERT INTO mtb_country (id, name, rank) VALUES (500,'モントセラト',235);
INSERT INTO mtb_country (id, name, rank) VALUES (400,'ヨルダン',236);
INSERT INTO mtb_country (id, name, rank) VALUES (418,'ラオス|ラオス人民民主共和国',237);
INSERT INTO mtb_country (id, name, rank) VALUES (428,'ラトビア',238);
INSERT INTO mtb_country (id, name, rank) VALUES (440,'リトアニア',239);
INSERT INTO mtb_country (id, name, rank) VALUES (434,'リビア',240);
INSERT INTO mtb_country (id, name, rank) VALUES (438,'リヒテンシュタイン',241);
INSERT INTO mtb_country (id, name, rank) VALUES (430,'リベリア',242);
INSERT INTO mtb_country (id, name, rank) VALUES (642,'ルーマニア',243);
INSERT INTO mtb_country (id, name, rank) VALUES (442,'ルクセンブルク',244);
INSERT INTO mtb_country (id, name, rank) VALUES (646,'ルワンダ',245);
INSERT INTO mtb_country (id, name, rank) VALUES (426,'レソト',246);
INSERT INTO mtb_country (id, name, rank) VALUES (422,'レバノン',247);
INSERT INTO mtb_country (id, name, rank) VALUES (638,'レユニオン',248);
INSERT INTO mtb_country (id, name, rank) VALUES (643,'ロシア|ロシア連邦',249);

INSERT INTO mtb_csv_type (id, name, rank) VALUES (1, '商品管理', 3);
INSERT INTO mtb_csv_type (id, name, rank) VALUES (2, '会員管理', 4);
INSERT INTO mtb_csv_type (id, name, rank) VALUES (3, '受注管理', 1);
INSERT INTO mtb_csv_type (id, name, rank) VALUES (4, '配送管理', 2);
INSERT INTO mtb_csv_type (id, name, rank) VALUES (5, 'カテゴリ', 5);

INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 1, 'カテゴリ', 'category', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 2, 'カゴの中', 'cart', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 3, '商品検索', 'search_product', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 4, '新着情報', 'news', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 5, 'ログイン', 'login', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 6, 'ロゴ', 'logo', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 7, 'フッター', 'footer', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 8, '新着商品', 'new_product', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 9, 'フリーエリア', 'free', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 0);
INSERT INTO dtb_block (device_type_id, block_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 10, 'ギャラリー', 'garally', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 0);
SELECT setval('dtb_block_block_id_seq', 11);

INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 0, 'プレビューデータ', 'preview', NULL, 1, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 1, 'TOPページ', 'homepage', 'index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 2, '商品一覧ページ', 'product_list', 'Product/list', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 3, '商品詳細ページ', 'product_detail', 'Product/detail', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 4, 'MYページ', 'mypage', 'Mypage/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 5, 'MYページ/会員登録内容変更(入力ページ)', 'mypage_change', 'Mypage/change', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 6, 'MYページ/会員登録内容変更(完了ページ)', 'mypage_change_complete', 'Mypage/change_complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 7, 'MYページ/お届け先変更', 'mypage_delivery', 'Mypage/delivery', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 8, 'MYページ/お届け先追加', 'mypage_delivery_new', 'Mypage/delivery', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 9, 'MYページ/お気に入り一覧', 'mypage_favorite', 'Mypage/favorite', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 10, 'MYページ/購入履歴詳細', 'mypage_history', 'Mypage/history', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 11, 'MYページ/ログイン', 'mypage_login', 'Mypage/login', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 12, 'MYページ/退会手続き(入力ページ)', 'mypage_withdraw', 'Mypage/withdraw', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 13, 'MYページ/退会手続き(完了ページ)', 'mypage_withdraw_complete', 'Mypage/withdraw_complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 14, '当サイトについて', 'help_about', 'Help/about', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 15, '現在のカゴの中', 'cart', 'Cart/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 16, 'お問い合わせ(入力ページ)', 'contact', 'Contact/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 17, 'お問い合わせ(完了ページ)', 'contact_complete', 'Contact/complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 18, '会員登録(入力ページ)', 'entry', 'Entry/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 19, 'ご利用規約', 'entry_kiyaku', 'Entry/kiyaku', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 20, '会員登録(完了ページ)', 'entry_complete', 'Entry/complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 21, '特定商取引に関する法律に基づく表記', 'help_tradelaw', 'Help/tradelaw', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 22, '本会員登録(完了ページ)', 'entry_activate', 'Entry/activate', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 23, '商品購入/ログイン', 'shopping', 'Shopping/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 24, '商品購入/お届け先の指定', 'shopping_shipping', 'Shopping/shipping', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 25, '商品購入/お届け先の複数指定', 'shopping_multiple', 'Shopping/multiple', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 26, '商品購入/お支払方法・お届け時間等の指定', 'shopping_payment', 'Shopping/payment', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 27, '商品購入/ご入力内容のご確認', 'shopping_confirm', 'Shopping/confirm', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 28, '商品購入/ご注文完了', 'shopping_complete', 'Shopping/complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 29, 'プライバシーポリシー', 'help_privacy', 'Help/privacy', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 30, '商品購入ログイン', 'shopping_login', 'Shopping/login', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_page_layout (device_type_id, page_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 31, '非会員購入情報入力', 'shopping_nonmember', 'Shopping/nonmember', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
SELECT setval('dtb_page_layout_page_id_seq', 31);

INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 6, 1, 1);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 2, 2, 1);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 3, 3, 1);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 5, 4, 1);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 1, 5, 1);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 8, 8, 1, 0);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 8, 4, 2, 0);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 8, 9, 3, 0);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 8, 10, 4, 0);
INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 9, 7, 1, 1);

INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (1, 'キッチンツール', NULL, 1, 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (2, 'インテリア', NULL, 1, 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '食器', 1, 2, 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (4, '調理器具', 1, 2, 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (5, 'フォーク', 3, 3, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (6, '新入荷', NULL, 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
SELECT setval('dtb_category_category_id_seq', 6);

INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (1, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (6, 2, CURRENT_TIMESTAMP);

INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (1, 2, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (3, 2, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (6, 2, CURRENT_TIMESTAMP);

INSERT INTO dtb_class_name (class_name_id, name, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '材質', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_name (class_name_id, name, rank, creator_id, create_date, update_date, del_flg) VALUES (2, 'サイズ', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
SELECT setval('dtb_class_name_class_name_id_seq', 2);

INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '金', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '銀', 1, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (3, 'プラチナ', 1, 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (4, '120mm', 2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (5, '170mm', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (6, '150cm', 2, 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
-- INSERT INTO dtb_class_category (class_category_id, name, class_name_id, rank, creator_id, create_date, update_date, del_flg) VALUES (0, NULL, 0, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
SELECT setval('dtb_class_category_class_category_id_seq', 6);

INSERT INTO dtb_delivery (delivery_id, product_type_id, name, service_name, confirm_url, rank, del_flg, creator_id, create_date, update_date) VALUES (1, 1, 'サンプル業者', 'サンプル業者', NULL, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_delivery (delivery_id, product_type_id, name, service_name, confirm_url, rank, del_flg, creator_id, create_date, update_date) VALUES (2, 2, 'サンプル宅配', 'サンプル宅配', NULL, 2, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_delivery_delivery_id_seq', 2);

INSERT INTO dtb_payment (payment_id, payment_method, charge, rule_max, rank, fix_flg, del_flg, creator_id, create_date, update_date, payment_image, charge_flg, rule_min) VALUES (1, '郵便振替', 0, NULL, 4, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 1, 0);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule_max, rank, fix_flg, del_flg, creator_id, create_date, update_date, payment_image, charge_flg, rule_min) VALUES (2, '現金書留', 0, NULL, 3, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 1, 0);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule_max, rank, fix_flg, del_flg, creator_id, create_date, update_date, payment_image, charge_flg, rule_min) VALUES (3, '銀行振込', 0, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 1, 0);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule_max, rank, fix_flg, del_flg, creator_id, create_date, update_date, payment_image, charge_flg, rule_min) VALUES (4, '代金引換', 0, NULL, 1, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 1, 0);
SELECT setval('dtb_payment_payment_id_seq', 4);

INSERT INTO dtb_payment_option (delivery_id, payment_id) VALUES (1, 1);
INSERT INTO dtb_payment_option (delivery_id, payment_id) VALUES (1, 2);
INSERT INTO dtb_payment_option (delivery_id, payment_id) VALUES (1, 3);
INSERT INTO dtb_payment_option (delivery_id, payment_id) VALUES (1, 4);
INSERT INTO dtb_payment_option (delivery_id, payment_id) VALUES (2, 3);

INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (1, 1, 1000, 1);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (2, 1, 1000, 2);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (3, 1, 1000, 3);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (4, 1, 1000, 4);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (5, 1, 1000, 5);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (6, 1, 1000, 6);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (7, 1, 1000, 7);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (8, 1, 1000, 8);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (9, 1, 1000, 9);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (10, 1, 1000, 10);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (11, 1, 1000, 11);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (12, 1, 1000, 12);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (13, 1, 1000, 13);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (14, 1, 1000, 14);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (15, 1, 1000, 15);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (16, 1, 1000, 16);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (17, 1, 1000, 17);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (18, 1, 1000, 18);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (19, 1, 1000, 19);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (20, 1, 1000, 20);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (21, 1, 1000, 21);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (22, 1, 1000, 22);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (23, 1, 1000, 23);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (24, 1, 1000, 24);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (25, 1, 1000, 25);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (26, 1, 1000, 26);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (27, 1, 1000, 27);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (28, 1, 1000, 28);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (29, 1, 1000, 29);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (30, 1, 1000, 30);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (31, 1, 1000, 31);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (32, 1, 1000, 32);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (33, 1, 1000, 33);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (34, 1, 1000, 34);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (35, 1, 1000, 35);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (36, 1, 1000, 36);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (37, 1, 1000, 37);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (38, 1, 1000, 38);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (39, 1, 1000, 39);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (40, 1, 1000, 40);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (41, 1, 1000, 41);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (42, 1, 1000, 42);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (43, 1, 1000, 43);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (44, 1, 1000, 44);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (45, 1, 1000, 45);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (46, 1, 1000, 46);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (47, 1, 1000, 47);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (48, 2, 0, 1);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (49, 2, 0, 2);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (50, 2, 0, 3);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (51, 2, 0, 4);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (52, 2, 0, 5);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (53, 2, 0, 6);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (54, 2, 0, 7);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (55, 2, 0, 8);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (56, 2, 0, 9);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (57, 2, 0, 10);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (58, 2, 0, 11);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (59, 2, 0, 12);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (60, 2, 0, 13);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (61, 2, 0, 14);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (62, 2, 0, 15);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (63, 2, 0, 16);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (64, 2, 0, 17);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (65, 2, 0, 18);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (66, 2, 0, 19);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (67, 2, 0, 20);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (68, 2, 0, 21);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (69, 2, 0, 22);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (70, 2, 0, 23);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (71, 2, 0, 24);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (72, 2, 0, 25);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (73, 2, 0, 26);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (74, 2, 0, 27);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (75, 2, 0, 28);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (76, 2, 0, 29);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (77, 2, 0, 30);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (78, 2, 0, 31);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (79, 2, 0, 32);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (80, 2, 0, 33);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (81, 2, 0, 34);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (82, 2, 0, 35);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (83, 2, 0, 36);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (84, 2, 0, 37);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (85, 2, 0, 38);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (86, 2, 0, 39);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (87, 2, 0, 40);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (88, 2, 0, 41);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (89, 2, 0, 42);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (90, 2, 0, 43);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (91, 2, 0, 44);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (92, 2, 0, 45);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (93, 2, 0, 46);
INSERT INTO dtb_delivery_fee (fee_id, delivery_id, fee, pref) VALUES (94, 2, 0, 47);
SELECT setval('dtb_delivery_fee_fee_id_seq', 94);

INSERT INTO dtb_delivery_time (time_id, delivery_id, delivery_time) VALUES (1, 1, '指定なし');
INSERT INTO dtb_delivery_time (time_id, delivery_id, delivery_time) VALUES (2, 1, '午前');
INSERT INTO dtb_delivery_time (time_id, delivery_id, delivery_time) VALUES (3, 1, '午後');
SELECT setval('dtb_delivery_time_time_id_seq', 3);

INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (1, '即日', 0, 0);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (2, '1～2日後', 1, 1);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (3, '3～4日後', 3, 2);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (4, '1週間以降', 7, 3);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (5, '2週間以降', 14, 4);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (6, '3週間以降', 21, 5);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (7, '1ヶ月以降', 30, 6);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (8, '2ヶ月以降', 60, 7);
INSERT INTO dtb_delivery_date (date_id, name, value, rank) VALUES (9, 'お取り寄せ(商品入荷後)', 0, 8);
SELECT setval('dtb_delivery_date_date_id_seq', 9);

INSERT INTO dtb_help(id, customer_agreement, create_date, update_date) VALUES (1, '第1条 (会員)

1. 「会員」とは、当社が定める手続に従い本規約に同意の上、入会の申し込みを行う個人をいいます。
2. 「会員情報」とは、会員が当社に開示した会員の属性に関する情報および会員の取引に関する履歴等の情報をいいます。
3. 本規約は、全ての会員に適用され、登録手続時および登録後にお守りいただく規約です。

第2条 (登録)

1. 会員資格
本規約に同意の上、所定の入会申込みをされたお客様は、所定の登録手続完了後に会員としての資格を有します。会員登録手続は、会員となるご本人が行ってください。代理による登録は一切認められません。なお、過去に会員資格が取り消された方やその他当社が相応しくないと判断した方からの会員申込はお断りする場合があります。

2. 会員情報の入力
会員登録手続の際には、入力上の注意をよく読み、所定の入力フォームに必要事項を正確に入力してください。会員情報の登録において、特殊記号・旧漢字・ローマ数字などはご使用になれません。これらの文字が登録された場合は当社にて変更致します。

3. パスワードの管理
(1)パスワードは会員本人のみが利用できるものとし、第三者に譲渡・貸与できないものとします。
(2)パスワードは、他人に知られることがないよう定期的に変更する等、会員本人が責任をもって管理してください。
(3)パスワードを用いて当社に対して行われた意思表示は、会員本人の意思表示とみなし、そのために生じる支払等は全て会員の責任となります。

第3条 (変更)

1. 会員は、氏名、住所など当社に届け出た事項に変更があった場合には、速やかに当社に連絡するものとします。
2. 変更登録がなされなかったことにより生じた損害について、当社は一切責任を負いません。また、変更登録がなされた場合でも、変更登録前にすでに手続がなされた取引は、変更登録前の情報に基づいて行われますのでご注意ください。

第4条 (退会)

会員が退会を希望する場合には、会員本人が退会手続きを行ってください。所定の退会手続の終了後に、退会となります。

第5条 (会員資格の喪失及び賠償義務)

1. 会員が、会員資格取得申込の際に虚偽の申告をしたとき、通信販売による代金支払債務を怠ったとき、その他当社が会員として不適当と認める事由があるときは、当社は、会員資格を取り消すことができることとします。

2. 会員が、以下の各号に定める行為をしたときは、これにより当社が被った損害を賠償する責任を負います。
(1)会員番号、パスワードを不正に使用すること
(2)当ホームページにアクセスして情報を改ざんしたり、当ホームページに有害なコンピュータープログラムを送信するなどして、当社の営業を妨害すること
(3)当社が扱う商品の知的所有権を侵害する行為をすること
(4)その他、この利用規約に反する行為をすること

第6条 (会員情報の取扱い)
1. 当社は、原則として会員情報を会員の事前の同意なく第三者に対して開示することはありません。ただし、次の各号の場合には、会員の事前の同意なく、当社は会員情報その他のお客様情報を開示できるものとします。
(1)法令に基づき開示を求められた場合
(2)当社の権利、利益、名誉等を保護するために必要であると当社が判断した場合

2. 会員情報につきましては、当社の「個人情報保護への取組み」に従い、当社が管理します。当社は、会員情報を、会員へのサービス提供、サービス内容の向上、サービスの利用促進、およびサービスの健全かつ円滑な運営の確保を図る目的のために、当社おいて利用することができるものとします。

3. 当社は、会員に対して、メールマガジンその他の方法による情報提供(広告を含みます)を行うことができるものとします。会員が情報提供を希望しない場合は、当社所定の方法に従い、その旨を通知して頂ければ、情報提供を停止します。ただし、本サービス運営に必要な情報提供につきましては、会員の希望により停止をすることはできません。

第7条 (禁止事項)

本サービスの利用に際して、会員に対し次の各号の行為を行うことを禁止します。

1. 法令または本規約、本サービスご利用上のご注意、本サービスでのお買い物上のご注意その他の本規約等に違反すること
2. 当社、およびその他の第三者の権利、利益、名誉等を損ねること
3. 青少年の心身に悪影響を及ぼす恐れがある行為、その他公序良俗に反する行為を行うこと
4. 他の利用者その他の第三者に迷惑となる行為や不快感を抱かせる行為を行うこと
5. 虚偽の情報を入力すること
6. 有害なコンピュータープログラム、メール等を送信または書き込むこと
7. 当社のサーバーその他のコンピューターに不正にアクセスすること
8. パスワードを第三者に貸与・譲渡すること、または第三者と共用すること
9. その他当社が不適切と判断すること

第8条 (サービスの中断・停止等)

1. 当社は、本サービスの稼動状態を良好に保つために、次の各号の一に該当する場合、予告なしに、本サービスの提供全てあるいは一部を停止することがあります。
(1)システムの定期保守および緊急保守のために必要な場合
(2)システムに負荷が集中した場合
(3)火災、停電、第三者による妨害行為などによりシステムの運用が困難になった場合
(4)その他、止むを得ずシステムの停止が必要と当社が判断した場合

第9条 (サービスの変更・廃止)

当社は、その判断によりサービスの全部または一部を事前の通知なく、適宜変更・廃止できるものとします。

第10条 (免責)

1. 通信回線やコンピューターなどの障害によるシステムの中断・遅滞・中止・データの消失、データへの不正アクセスにより生じた損害、その他当社のサービスに関して会員に生じた損害について、当社は一切責任を負わないものとします。
2. 当社は、当社のウェブページ・サーバー・ドメインなどから送られるメール・コンテンツに、コンピューター・ウィルスなどの有害なものが含まれていないことを保証いたしません。
3. 会員が本規約等に違反したことによって生じた損害については、当社は一切責任を負いません。

第11条 (本規約の改定)

当社は、本規約を任意に改定できるものとし、また、当社において本規約を補充する規約(以下「補充規約」といいます)を定めることができます。本規約の改定または補充は、改定後の本規約または補充規約を当社所定のサイトに掲示したときにその効力を生じるものとします。この場合、会員は、改定後の規約および補充規約に従うものと致します。

第12条 (準拠法、管轄裁判所)

本規約に関して紛争が生じた場合、当社本店所在地を管轄する地方裁判所を第一審の専属的合意管轄裁判所とします。 ', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_mail_template (template_id, name, file_name, subject, header, footer, creator_id, del_flg, create_date, update_date) VALUES (1, '注文受付メール', 'Mail/order.twig', 'ご注文ありがとうございます', 'この度はご注文いただき誠にありがとうございます。
下記ご注文内容にお間違えがないかご確認下さい。

', '
============================================


このメッセージはお客様へのお知らせ専用ですので、
このメッセージへの返信としてご質問をお送りいただいても回答できません。
ご了承ください。

ご質問やご不明な点がございましたら、こちらからお願いいたします。

', 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_mail_template (template_id, name, file_name, subject, header, footer, creator_id, del_flg, create_date, update_date) VALUES (5, '問合受付メール', 'Mail/contact.twig', 'お問い合わせを受け付けました', NULL, NULL, 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_mail_template_template_id_seq', 5);

INSERT INTO dtb_news (news_id, news_date, rank, news_title, news_comment, news_url, news_select, link_method, creator_id, create_date, update_date, del_flg) VALUES (1, CURRENT_TIMESTAMP, 1, 'サイトオープンいたしました!', '一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！', NULL, 0, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
SELECT setval('dtb_news_news_id_seq', 1);

INSERT INTO dtb_product (product_id, name, status, note, del_flg, creator_id, create_date, update_date, delivery_date_id, description_detail) VALUES (1, 'ディナーフォーク', 1, NULL, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 2, 'セットで揃えたいディナー用のカトラリー。
定番の銀製は、シルバー特有の美しい輝きと柔らかな曲線が特徴です。適度な重みと日本人の手に合いやすいサイズ感で長く愛用いただけます。
最高級プラチナフォークは、贈り物としても人気です。');
INSERT INTO dtb_product (product_id, name, status, note, del_flg, creator_id, create_date, update_date, delivery_date_id, description_detail) VALUES (2, 'パーコレーター', 1, NULL, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 3, '
パーコレーターはコーヒーの粉をセットして直火にかけて抽出する器具です。
アウトドアでも淹れたてのコーヒーをお楽しみいただけます。
いまだけ、おいしい淹れ方の冊子つきです。');
SELECT setval('dtb_product_product_id_seq', 2);

INSERT INTO dtb_product_category (product_id, category_id, rank) VALUES (1, 5, 1);
INSERT INTO dtb_product_category (product_id, category_id, rank) VALUES (1, 6, 1);
INSERT INTO dtb_product_category (product_id, category_id, rank) VALUES (2, 1, 1);
INSERT INTO dtb_product_category (product_id, category_id, rank) VALUES (2, 4, 1);
INSERT INTO dtb_product_category (product_id, category_id, rank) VALUES (2, 6, 2);

INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(0, 1, NULL, NULL, 'fork-01', NULL, true, NULL, 115000, 110000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(1, 1, 3, 6, 'fork-01', NULL, true, NULL, 115000, 110000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(2, 1, 3, 5, 'fork-02', NULL, true, NULL, 95000, 93000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(3, 1, 3, 4, 'fork-03', NULL, true, NULL, 75000, 74000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(4, 1, 2, 6, 'fork-04', NULL, true, NULL, 95000, 93000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(5, 1, 2, 5, 'fork-05', NULL, true, NULL, 50000, 49000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(6, 1, 2, 4, 'fork-06', NULL, true, NULL, 35000, 34500, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(7, 1, 1, 6, 'fork-07', NULL, true, NULL, null, 18000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(8, 1, 1, 5, 'fork-08', NULL, true, NULL, null, 13000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(9, 1, 1, 4, 'fork-09', NULL, true, NULL, null, 5000, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
INSERT INTO dtb_product_class (product_class_id, product_id, class_category_id1, class_category_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, delivery_fee, creator_id, create_date, update_date, del_flg, product_type_id) VALUES(10, 2, NULL, NULL, 'cafe-01', 100, false, 5, 3000, 2800, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);
SELECT setval('dtb_product_class_product_class_id_seq', 10);

INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(1, 0, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(2, 1, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(3, 2, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(4, 3, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(5, 4, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(6, 5, NULL, 1,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(7, 6, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(8, 7, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(9, 8, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(10, 9, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_stock (product_stock_id, product_class_id, stock, creator_id, create_date, update_date) VALUES(11, 10, 100, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_product_stock_product_stock_id_seq', 11);

INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(1, 1, 1, 'fork-1.jpg', 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(2, 1, 1, 'fork-2.jpg', 2, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(3, 1, 1, 'fork-3.jpg', 3, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(4, 2, 1, 'cafe-1.jpg', 3, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(5, 2, 1, 'cafe-2.jpg', 3, CURRENT_TIMESTAMP);
INSERT INTO dtb_product_image (product_image_id, product_id, creator_id, file_name, rank, create_date) VALUES(6, 2, 1, 'cafe-3.jpg', 3, CURRENT_TIMESTAMP);
SELECT setval('dtb_product_image_product_image_id_seq', 6);

INSERT INTO dtb_template (template_id, template_code, device_type_id, template_name, create_date, update_date) VALUES (1, 'default', 10, 'デフォルト', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_template (template_id, template_code, device_type_id, template_name, create_date, update_date) VALUES (2, 'mobile', 1, 'モバイル', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_template (template_id, template_code, device_type_id, template_name, create_date, update_date) VALUES (4, 'sphone', 2, 'スマートフォン', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_template_template_id_seq', 4);

INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (1, 1, 'Eccube\\Entity\\Product', 'id', NULL, '商品ID', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (2, 1, 'Eccube\\Entity\\Product', 'Status', 'id', '公開ステータス(ID)', 2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (3, 1, 'Eccube\\Entity\\Product', 'Status', 'name', '公開ステータス(名称)', 3, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (4, 1, 'Eccube\\Entity\\Product', 'name', NULL, '商品名', 4, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (5, 1, 'Eccube\\Entity\\Product', 'note', NULL, 'ショップ用メモ欄', 5, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (6, 1, 'Eccube\\Entity\\Product', 'description_list', NULL, '商品説明(一覧)', 6, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (7, 1, 'Eccube\\Entity\\Product', 'description_detail', NULL, '商品説明(詳細)', 7, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (8, 1, 'Eccube\\Entity\\Product', 'search_word', NULL, '検索ワード', 8, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (9, 1, 'Eccube\\Entity\\Product', 'free_area', NULL, 'フリーエリア', 9, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (10, 1, 'Eccube\\Entity\\ProductClass', 'id', NULL, '商品規格ID', 10, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (11, 1, 'Eccube\\Entity\\ProductClass', 'ProductType', 'id', '商品種別(ID)', 11, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (12, 1, 'Eccube\\Entity\\ProductClass', 'ProductType', 'name', '商品種別(名称)', 12, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (13, 1, 'Eccube\\Entity\\ProductClass', 'ClassCategory1', 'id', '規格分類1(ID)', 13, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (14, 1, 'Eccube\\Entity\\ProductClass', 'ClassCategory1', 'name', '規格分類1(名称)', 14, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (15, 1, 'Eccube\\Entity\\ProductClass', 'ClassCategory2', 'id', '規格分類2(ID)', 15, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (16, 1, 'Eccube\\Entity\\ProductClass', 'ClassCategory2', 'name', '規格分類2(名称)', 16, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (17, 1, 'Eccube\\Entity\\ProductClass', 'DeliveryDate', 'id', '発送日目安(ID)', 17, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (18, 1, 'Eccube\\Entity\\ProductClass', 'DeliveryDate', 'name', '発送日目安(名称)', 18, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (19, 1, 'Eccube\\Entity\\ProductClass', 'code', NULL, '商品コード', 19, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (20, 1, 'Eccube\\Entity\\ProductClass', 'stock', NULL, '在庫数', 20, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (21, 1, 'Eccube\\Entity\\ProductClass', 'stock_unlimited', NULL, '在庫数無制限フラグ', 21, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (22, 1, 'Eccube\\Entity\\ProductClass', 'sale_limit', NULL, '販売制限数', 22, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (23, 1, 'Eccube\\Entity\\ProductClass', 'price01', NULL, '通常価格', 23, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (24, 1, 'Eccube\\Entity\\ProductClass', 'price02', NULL, '販売価格', 24, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (25, 1, 'Eccube\\Entity\\ProductClass', 'delivery_fee', NULL, '送料', 25, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (26, 1, 'Eccube\\Entity\\Product', 'ProductImage', 'file_name', '商品画像', 26, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (27, 1, 'Eccube\\Entity\\Product', 'Category', 'id', '商品カテゴリ(ID)', 27, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (28, 1, 'Eccube\\Entity\\Product', 'Category', 'name', '商品カテゴリ(名称)', 28, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (29, 2, 'Eccube\\Entity\\Customer', 'id', NULL, '会員ID',  1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (30, 2, 'Eccube\\Entity\\Customer', 'name01', NULL, 'お名前(姓)',  2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (31, 2, 'Eccube\\Entity\\Customer', 'name02', NULL, 'お名前(名)',  3, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (32, 2, 'Eccube\\Entity\\Customer', 'kana01', NULL, 'お名前(セイ)',  4, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (33, 2, 'Eccube\\Entity\\Customer', 'kana02', NULL, 'お名前(メイ)',  5, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (34, 2, 'Eccube\\Entity\\Customer', 'company_name', NULL, '会社名',  6, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (35, 2, 'Eccube\\Entity\\Customer', 'zip01', NULL, '郵便番号1',  7, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (36, 2, 'Eccube\\Entity\\Customer', 'zip02', NULL, '郵便番号2',  8, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (37, 2, 'Eccube\\Entity\\Customer', 'Pref', 'id', '都道府県(ID)',  9, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (38, 2, 'Eccube\\Entity\\Customer', 'Pref', 'name', '都道府県(名称)',  10, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (39, 2, 'Eccube\\Entity\\Customer', 'addr01', NULL, '住所1',  11, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (40, 2, 'Eccube\\Entity\\Customer', 'addr02', NULL, '住所2',  12, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (41, 2, 'Eccube\\Entity\\Customer', 'email', NULL, 'メールアドレス',  13, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (42, 2, 'Eccube\\Entity\\Customer', 'tel01', NULL, 'TEL1',  14, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (43, 2, 'Eccube\\Entity\\Customer', 'tel02', NULL, 'TEL2',  15, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (44, 2, 'Eccube\\Entity\\Customer', 'tel03', NULL, 'TEL3',  16, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (45, 2, 'Eccube\\Entity\\Customer', 'fax01', NULL, 'FAX1',  17, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (46, 2, 'Eccube\\Entity\\Customer', 'fax02', NULL, 'FAX2',  18, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (47, 2, 'Eccube\\Entity\\Customer', 'fax03', NULL, 'FAX3',  19, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (48, 2, 'Eccube\\Entity\\Customer', 'Sex', 'id', '性別(ID)',  20, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (49, 2, 'Eccube\\Entity\\Customer', 'Sex', 'name', '性別(名称)',  21, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (50, 2, 'Eccube\\Entity\\Customer', 'Job', 'id', '職業(ID)',  22, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (51, 2, 'Eccube\\Entity\\Customer', 'Job', 'name', '職業(名称)',  23, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (52, 2, 'Eccube\\Entity\\Customer', 'birth', NULL, '誕生日',  24, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (53, 2, 'Eccube\\Entity\\Customer', 'first_buy_date', NULL, '初回購入日',  25, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (54, 2, 'Eccube\\Entity\\Customer', 'last_buy_date', NULL, '最終購入日',  26, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (55, 2, 'Eccube\\Entity\\Customer', 'buy_times', NULL, '購入回数',  27, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (56, 2, 'Eccube\\Entity\\Customer', 'note', NULL, 'ショップ用メモ欄',  28, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (57, 2, 'Eccube\\Entity\\Customer', 'Status', 'id', '会員ステータス(ID)',  29, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (58, 2, 'Eccube\\Entity\\Customer', 'Status', 'name', '会員ステータス(名称)',  30, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (59, 2, 'Eccube\\Entity\\Customer', 'create_date', NULL, '登録日',  31, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (60, 2, 'Eccube\\Entity\\Customer', 'update_date', NULL, '更新日',  32, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (61, 3, 'Eccube\\Entity\\Order', 'id', NULL, '注文ID', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (62, 3, 'Eccube\\Entity\\Order', 'Customer', 'id', '会員ID', 2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (63, 3, 'Eccube\\Entity\\Order', 'name01', NULL, 'お名前(姓)', 3, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (64, 3, 'Eccube\\Entity\\Order', 'name02', NULL, 'お名前(名)', 4, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (65, 3, 'Eccube\\Entity\\Order', 'kana01', NULL, 'お名前(セイ)', 5, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (66, 3, 'Eccube\\Entity\\Order', 'kana02', NULL, 'お名前(メイ)', 6, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (67, 3, 'Eccube\\Entity\\Order', 'company_name', NULL, '会社名', 7, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (68, 3, 'Eccube\\Entity\\Order', 'zip01', NULL, '郵便番号1', 8, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (69, 3, 'Eccube\\Entity\\Order', 'zip02', NULL, '郵便番号2', 9, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (70, 3, 'Eccube\\Entity\\Order', 'Pref', 'id', '都道府県(ID)', 10, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (71, 3, 'Eccube\\Entity\\Order', 'Pref', 'name', '都道府県(名称)', 11, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (72, 3, 'Eccube\\Entity\\Order', 'addr01', NULL, '住所1', 12, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (73, 3, 'Eccube\\Entity\\Order', 'addr02', NULL, '住所2', 13, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (74, 3, 'Eccube\\Entity\\Order', 'email', NULL, 'メールアドレス', 14, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (75, 3, 'Eccube\\Entity\\Order', 'tel01', NULL, 'TEL1', 15, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (76, 3, 'Eccube\\Entity\\Order', 'tel02', NULL, 'TEL2', 16, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (77, 3, 'Eccube\\Entity\\Order', 'tel03', NULL, 'TEL3', 17, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (78, 3, 'Eccube\\Entity\\Order', 'fax01', NULL, 'FAX1', 18, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (79, 3, 'Eccube\\Entity\\Order', 'fax02', NULL, 'FAX2', 19, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (80, 3, 'Eccube\\Entity\\Order', 'fax03', NULL, 'FAX3', 20, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (81, 3, 'Eccube\\Entity\\Order', 'Sex', 'id', '性別(ID)', 21, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (82, 3, 'Eccube\\Entity\\Order', 'Sex', 'name', '性別(名称)', 22, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (83, 3, 'Eccube\\Entity\\Order', 'Job', 'id', '職業(ID)', 23, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (84, 3, 'Eccube\\Entity\\Order', 'Job', 'name', '職業(名称)', 24, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (85, 3, 'Eccube\\Entity\\Order', 'birth', NULL, '誕生日', 25, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (86, 3, 'Eccube\\Entity\\Order', 'note', NULL, 'ショップ用メモ欄', 26, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (87, 3, 'Eccube\\Entity\\Order', 'subtotal', NULL, '小計', 27, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (88, 3, 'Eccube\\Entity\\Order', 'discount', NULL, '値引き', 28, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (89, 3, 'Eccube\\Entity\\Order', 'delivery_fee_total', NULL, '送料', 29, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (90, 3, 'Eccube\\Entity\\Order', 'tax', NULL, '税金', 30, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (91, 3, 'Eccube\\Entity\\Order', 'total', NULL, '合計', 31, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (92, 3, 'Eccube\\Entity\\Order', 'payment_total', NULL, '支払合計', 32, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (93, 3, 'Eccube\\Entity\\Order', 'OrderStatus', 'id', '対応状況(ID)', 33, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (94, 3, 'Eccube\\Entity\\Order', 'OrderStatus', 'name', '対応状況(名称)', 34, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (95, 3, 'Eccube\\Entity\\Order', 'Payment', 'id', '支払方法(ID)', 35, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (96, 3, 'Eccube\\Entity\\Order', 'payment_method', NULL, '支払方法(名称)', 36, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (97, 3, 'Eccube\\Entity\\Order', 'order_date', NULL, '受注日', 37, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (98, 3, 'Eccube\\Entity\\Order', 'payment_date', NULL, '入金日', 38, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (99, 3, 'Eccube\\Entity\\Order', 'commit_date', NULL, '発送日', 39, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (100, 3, 'Eccube\\Entity\\OrderDetail', 'id', NULL, '注文詳細ID', 40, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (101, 3, 'Eccube\\Entity\\OrderDetail', 'Product', 'id', '商品ID', 41, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (102, 3, 'Eccube\\Entity\\OrderDetail', 'ProductClass', 'id', '商品規格ID', 42, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (103, 3, 'Eccube\\Entity\\OrderDetail', 'product_name', NULL, '商品名', 43, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (104, 3, 'Eccube\\Entity\\OrderDetail', 'product_code', NULL, '商品コード', 44, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (105, 3, 'Eccube\\Entity\\OrderDetail', 'class_name1', NULL, '規格名1', 45, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (106, 3, 'Eccube\\Entity\\OrderDetail', 'class_name2', NULL, '規格名2', 46, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (107, 3, 'Eccube\\Entity\\OrderDetail', 'class_category_name1', NULL, '規格分類名1', 47, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (108, 3, 'Eccube\\Entity\\OrderDetail', 'class_category_name2', NULL, '規格分類名2', 48, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (109, 3, 'Eccube\\Entity\\OrderDetail', 'price', NULL, '価格', 49, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (110, 3, 'Eccube\\Entity\\OrderDetail', 'quantity', NULL, '個数', 50, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (111, 3, 'Eccube\\Entity\\OrderDetail', 'tax_rate', NULL, '税率', 51, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (112, 3, 'Eccube\\Entity\\OrderDetail', 'tax_rule', NULL, '税率ルール(ID)', 52, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (113, 4, 'Eccube\\Entity\\Order', 'id', NULL, '注文ID', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (114, 4, 'Eccube\\Entity\\Order', 'Customer', 'id', '会員ID', 2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (115, 4, 'Eccube\\Entity\\Order', 'name01', NULL, 'お名前(姓)', 3, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (116, 4, 'Eccube\\Entity\\Order', 'name02', NULL, 'お名前(名)', 4, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (117, 4, 'Eccube\\Entity\\Order', 'kana01', NULL, 'お名前(セイ)', 5, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (118, 4, 'Eccube\\Entity\\Order', 'kana02', NULL, 'お名前(メイ)', 6, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (119, 4, 'Eccube\\Entity\\Order', 'company_name', NULL, '会社名', 7, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (120, 4, 'Eccube\\Entity\\Order', 'zip01', NULL, '郵便番号1', 8, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (121, 4, 'Eccube\\Entity\\Order', 'zip02', NULL, '郵便番号2', 9, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (122, 4, 'Eccube\\Entity\\Order', 'Pref', 'id', '都道府県(ID)', 10, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (123, 4, 'Eccube\\Entity\\Order', 'Pref', 'name', '都道府県(名称)', 11, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (124, 4, 'Eccube\\Entity\\Order', 'addr01', NULL, '住所1', 12, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (125, 4, 'Eccube\\Entity\\Order', 'addr02', NULL, '住所2', 13, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (126, 4, 'Eccube\\Entity\\Order', 'email', NULL, 'メールアドレス', 14, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (127, 4, 'Eccube\\Entity\\Order', 'tel01', NULL, 'TEL1', 15, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (128, 4, 'Eccube\\Entity\\Order', 'tel02', NULL, 'TEL2', 16, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (129, 4, 'Eccube\\Entity\\Order', 'tel03', NULL, 'TEL3', 17, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (130, 4, 'Eccube\\Entity\\Order', 'fax01', NULL, 'FAX1', 18, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (131, 4, 'Eccube\\Entity\\Order', 'fax02', NULL, 'FAX2', 19, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (132, 4, 'Eccube\\Entity\\Order', 'fax03', NULL, 'FAX3', 20, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (133, 4, 'Eccube\\Entity\\Order', 'Sex', 'id', '性別(ID)', 21, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (134, 4, 'Eccube\\Entity\\Order', 'Sex', 'name', '性別(名称)', 22, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (135, 4, 'Eccube\\Entity\\Order', 'Job', 'id', '職業(ID)', 23, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (136, 4, 'Eccube\\Entity\\Order', 'Job', 'name', '職業(名称)', 24, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (137, 4, 'Eccube\\Entity\\Order', 'birth', NULL, '誕生日', 25, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (138, 4, 'Eccube\\Entity\\Order', 'note', NULL, 'ショップ用メモ欄', 26, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (139, 4, 'Eccube\\Entity\\Order', 'subtotal', NULL, '小計', 27, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (140, 4, 'Eccube\\Entity\\Order', 'discount', NULL, '値引き', 28, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (141, 4, 'Eccube\\Entity\\Order', 'delivery_fee_total', NULL, '送料', 29, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (142, 4, 'Eccube\\Entity\\Order', 'tax', NULL, '税金', 30, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (143, 4, 'Eccube\\Entity\\Order', 'total', NULL, '合計', 31, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (144, 4, 'Eccube\\Entity\\Order', 'payment_total', NULL, '支払合計', 32, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (145, 4, 'Eccube\\Entity\\Order', 'OrderStatus', 'id', '対応状況(ID)', 33, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (146, 4, 'Eccube\\Entity\\Order', 'OrderStatus', 'name', '対応状況(名称)', 34, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (147, 4, 'Eccube\\Entity\\Order', 'Payment', 'id', '支払方法(ID)', 35, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (148, 4, 'Eccube\\Entity\\Order', 'payment_method', NULL, '支払方法(名称)', 36, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (149, 4, 'Eccube\\Entity\\Order', 'order_date', NULL, '受注日', 37, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (150, 4, 'Eccube\\Entity\\Order', 'payment_date', NULL, '入金日', 38, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (151, 4, 'Eccube\\Entity\\Order', 'commit_date', NULL, '発送日', 39, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (152, 4, 'Eccube\\Entity\\Shipping', 'id', NULL, '配送ID', 40, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (153, 4, 'Eccube\\Entity\\Shipping', 'name01', NULL, '配送先_お名前(姓)', 41, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (154, 4, 'Eccube\\Entity\\Shipping', 'name02', NULL, '配送先_お名前(名)', 42, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (155, 4, 'Eccube\\Entity\\Shipping', 'kana01', NULL, '配送先_お名前(セイ)', 43, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (156, 4, 'Eccube\\Entity\\Shipping', 'kana02', NULL, '配送先_お名前(メイ)', 44, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (157, 4, 'Eccube\\Entity\\Shipping', 'company_name', NULL, '配送先_会社名', 45, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (158, 4, 'Eccube\\Entity\\Shipping', 'zip01', NULL, '配送先_郵便番号1', 46, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (159, 4, 'Eccube\\Entity\\Shipping', 'zip02', NULL , '配送先_郵便番号2', 47, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (160, 4, 'Eccube\\Entity\\Shipping', 'Pref', 'id', '配送先_都道府県(ID)', 48, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (161, 4, 'Eccube\\Entity\\Shipping', 'Pref', 'name', '配送先_都道府県(名称)', 49, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (162, 4, 'Eccube\\Entity\\Shipping', 'addr01', NULL, '配送先_住所1', 50, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (163, 4, 'Eccube\\Entity\\Shipping', 'addr02', NULL, '配送先_住所2', 51, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (164, 4, 'Eccube\\Entity\\Shipping', 'tel01', NULL, '配送先_TEL1', 52, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (165, 4, 'Eccube\\Entity\\Shipping', 'tel02', NULL, '配送先_TEL2', 53, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (166, 4, 'Eccube\\Entity\\Shipping', 'tel03', NULL, '配送先_TEL3', 54, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (167, 4, 'Eccube\\Entity\\Shipping', 'fax01', NULL, '配送先_FAX1', 55, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (168, 4, 'Eccube\\Entity\\Shipping', 'fax02', NULL, '配送先_FAX2', 56, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (169, 4, 'Eccube\\Entity\\Shipping', 'fax03', NULL, '配送先_FAX3', 57, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (170, 4, 'Eccube\\Entity\\Shipping', 'Delivery', 'id', '配送業者(ID)', 58, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (171, 4, 'Eccube\\Entity\\Shipping', 'shipping_delivery_name', NULL, '配送業者(名称)', 59, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (172, 4, 'Eccube\\Entity\\Shipping', 'DeliveryTime', 'id', 'お届け時間ID', 60, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (173, 4, 'Eccube\\Entity\\Shipping', 'shipping_delivery_time', NULL, 'お届け時間(名称)', 61, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (174, 4, 'Eccube\\Entity\\Shipping', 'shipping_delivery_date', NULL, 'お届け希望日', 62, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (175, 4, 'Eccube\\Entity\\Shipping', 'DeliveryFee', 'id', '送料ID', 63, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (176, 4, 'Eccube\\Entity\\Shipping', 'shipping_delivery_fee', NULL, '送料', 64, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (177, 4, 'Eccube\\Entity\\Shipping', 'shipping_commit_date', NULL, '発送日', 65, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (178, 4, 'Eccube\\Entity\\ShipmentItem', 'id', NULL, '配送商品ID', 66, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (179, 4, 'Eccube\\Entity\\ShipmentItem', 'Product', 'id', '商品ID', 67, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (180, 4, 'Eccube\\Entity\\ShipmentItem', 'ProductClass', 'id', '商品規格ID', 68, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (181, 4, 'Eccube\\Entity\\ShipmentItem', 'product_name', NULL, '商品名', 69, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (182, 4, 'Eccube\\Entity\\ShipmentItem', 'product_code', NULL, '商品コード', 70, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (183, 4, 'Eccube\\Entity\\ShipmentItem', 'class_name1', NULL, '規格名1', 71, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (184, 4, 'Eccube\\Entity\\ShipmentItem', 'class_name2', NULL, '規格名2', 72, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (185, 4, 'Eccube\\Entity\\ShipmentItem', 'class_category_name1', NULL, '規格分類名1', 73, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (186, 4, 'Eccube\\Entity\\ShipmentItem', 'class_category_name2', NULL, '規格分類名2', 74, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (187, 4, 'Eccube\\Entity\\ShipmentItem', 'price', NULL, '価格', 75, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (188, 4, 'Eccube\\Entity\\ShipmentItem', 'quantity', NULL, '個数', 76, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (189, 5, 'Eccube\\Entity\\Category', 'id', NULL, 'カテゴリID', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (190, 5, 'Eccube\\Entity\\Category', 'rank', NULL, '表示ランク', 2, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (191, 5, 'Eccube\\Entity\\Category', 'name', NULL, 'カテゴリ名', 3, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (192, 5, 'Eccube\\Entity\\Category', 'Parent', 'id', '親カテゴリID', 4, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (csv_id, csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (193, 5, 'Eccube\\Entity\\Category', 'level', NULL, '階層', 5, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
SELECT setval('dtb_csv_csv_id_seq', 193);

COMMIT;
CREATE INDEX dtb_products_category_id_key ON dtb_products(category_id);
CREATE INDEX dtb_products_class_product_id_key ON dtb_products_class(product_id);
CREATE INDEX dtb_order_detail_product_id_key ON dtb_order_detail(product_id);
CREATE INDEX dtb_category_category_id_key ON dtb_category(category_id);

INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, del_flg, create_date, update_date) 
VALUES ('dummy','dummy','',0,0,1,1, now(), now());

INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, del_flg, rank, create_date, update_date) 
VALUES ('admin','admin','2c19f4a742398150cecc80b3e76b673a35b8c19c',0,0,1,0,1, now(), now());

insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('カテゴリ',	'html/user_data/include/bloc/category.tpl', 'category','html/frontparts/bloc/category.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('利用ガイド',	'html/user_data/include/bloc/guide.tpl', 'guide','', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('かごの中',	'html/user_data/include/bloc/cart.tpl', 'cart','html/frontparts/bloc/cart.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('商品検索',	'html/user_data/include/bloc/search_products.tpl', 'search_products','html/frontparts/bloc/search_products.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('新着情報',	'html/user_data/include/bloc/news.tpl', 'news','html/frontparts/bloc/news.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('ログイン',	'html/user_data/include/bloc/login.tpl', 'login','html/frontparts/bloc/login.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('オススメ商品','html/user_data/include/bloc/best5.tpl', 'best5','html/frontparts/bloc/best5.php', 1, now(), now());

insert into dtb_pagelayout (page_id,page_name,url, create_date, update_date)values(0, 'プレビューデータ','',now(),now());
update dtb_pagelayout set page_id = 0;
insert into dtb_pagelayout (page_id,page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values(1,'TOPページ','index.php',' ','/html/user_data/templates/','top',2,now(),now());
insert into dtb_pagelayout (page_id,page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values(2,'商品一覧ページ','products/list.php',' ','/html/user_data/templates/','list',2,now(),now());
insert into dtb_pagelayout (page_id,page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values(3,'商品詳細','products/detail.php',' ','/html/user_data/templates/','detail',2,now(),now());
insert into dtb_pagelayout (page_id,page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values(4,'MYページ','mypage/index.php',' ','','',2,now(),now());

INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'category'),2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'guide'),3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'cart'),1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,3,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'search_products'),2,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,4,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'news'),1,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,3,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'login'),1,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,4,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'best5'),2,'best5');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'category'),2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'guide'),3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'cart'),1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'search_products'),0,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'news'),0,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'login'),0,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'best5'),0,'best5');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'category'),2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'guide'),3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'cart'),1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'search_products'),0,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'news'),0,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'login'),0,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,(SELECT bloc_id FROM dtb_bloc WHERE filename = 'best5'),0,'best5');

insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_id','商品ID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_class_id','規格ID',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'classcategory_id1','規格名1',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'classcategory_id2','規格名2',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'name','商品名',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'status','公開フラグ',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_flag','商品ステータス',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_code','商品コード',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'price01','参考市場価格',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'price02','価格',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'stock','在庫数',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'deliv_fee','送料',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'point_rate','ポイント付与率',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sale_limit','購入制限',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'comment1','メーカーURL',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'comment3','検索ワード',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_list_comment','一覧-メインコメント',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_list_image','一覧-メイン画像',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_comment','詳細-メインコメント',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_image','詳細-メイン画像',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_large_image','詳細-メイン拡大画像 ',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'file1','カラー比較画像',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'file2','商品詳細ファイル    ',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title1','詳細-サブタイトル（1）',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment1','詳細-サブコメント（1）',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image1','詳細-サブ画像（1）',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image1','詳細-サブ拡大画像（1）',27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title2','詳細-サブタイトル（2）',28,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment2','詳細-サブコメント（2）',29,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image2','詳細-サブ画像（2）',30,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image2','詳細-サブ拡大画像（2）',31,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title3','詳細-サブタイトル（3）',32,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment3','詳細-サブコメント（3）',33,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image3','詳細-サブ画像（3）',34,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image3','詳細-サブ拡大画像（3）',35,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title4','詳細-サブタイトル（4）',36,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment4','詳細-サブコメント（4）',37,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image4','詳細-サブ画像（4）',38,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image4','詳細-サブ拡大画像（4）',39,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title5','詳細-サブタイトル（5）',40,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment5','詳細-サブコメント（5）',41,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image5','詳細-サブ画像（5）',42,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image5','詳細-サブ拡大画像（5）',43,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'deliv_date_id','発送日目安',44,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_product_id1','おすすめ商品(1)',45,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_comment1','おすすめコメント(1)',46,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_product_id2','おすすめ商品(2)',47,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_comment2','おすすめコメント(2)',48,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_product_id3','おすすめ商品(3)',49,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_comment3','おすすめコメント(3)',50,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_product_id4','おすすめ商品(4)',51,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_comment4','おすすめコメント(4)',52,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'customer_id','顧客ID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'name01','名前1',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'name02','名前2',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'kana01','フリガナ1',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'kana02','フリガナ2',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'zip01', '郵便番号1',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'zip02', '郵便番号2',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'pref', '都道府県',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'addr01', '住所1',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'addr02', '住所2',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'email', 'E-MAIL',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel01', 'TEL1',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel02', 'TEL2',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel03', 'TEL3',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax01', 'FAX1',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax02', 'FAX2',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax03', 'FAX3',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'sex', '性別',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'job', '職業',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'birth', '誕生日',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'first_buy_date', '初回購入日',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'last_buy_date', '最終購入日',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'buy_times', '購入回数',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'point', 'ポイント残高',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'note', '備考',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'create_date','登録日',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'update_date','更新日',   27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_id','注文ID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'customer_id','顧客ID',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'message','要望等',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_name01','顧客名1',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_name02','顧客名2',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_kana01','顧客名カナ1',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_kana02','顧客名カナ2',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_email','メールアドレス',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel01','電話番号1',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel02','電話番号2',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel03','電話番号3',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax01','FAX1',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax02','FAX2',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax03','FAX3',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_zip01','郵便番号1',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_zip02','郵便番号2',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_pref','都道府県',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_addr01','住所1',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_addr02','住所2',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_sex','性別',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_birth','生年月日',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_job','職種',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_name01','配送先名前',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_name02','配送先名前',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_kana01','配送先カナ',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_kana02','配送先カナ',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel01','電話番号1',27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel02','電話番号2',28,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel03','電話番号3',29,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax01','FAX1',30,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax02','FAX2',31,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax03','FAX3',32,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_zip01','郵便番号1',33,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_zip02','郵便番号2',34,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_pref','都道府県',35,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_addr01','住所1',36,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_addr02','住所2',37,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'subtotal','小計',38,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'discount','値引き',39,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fee','送料',40,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'charge','手数料',41,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'use_point','使用ポイント',42,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'add_point','加算ポイント',43,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'tax','税金',44,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'total','合計',45,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'payment_total','お支払い合計',46,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'payment_method','支払い方法',47,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_time','配送時間',48,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_no','配送伝票番号',49,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'note','SHOPメモ',50,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'status','対応状況',51,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'create_date','注文日時',52,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'update_date','更新日時',53,now(),now());


INSERT INTO dtb_templates (template_code, template_name, create_date, update_date) VALUES('default1','デフォルト1', now(), now());

insert into dtb_mailtemplate (template_id, subject, header, footer, creator_id, update_date, create_date) values (
1,
'ご注文ありがとうございます。',
'この度はご注文いただき誠に有難うございます。
下記ご注文内容にお間違えがないかご確認下さい。

',
'

==============================================================☆
このメッセージはお客様へのお知らせ専用ですので、
このメッセージへの返信としてご質問をお送りいただいても回答できません。
ご了承ください。

ご質問やご不明な点がございましたら、こちらからお願いいたします。
http://------.co.jp

',0,Now(), now());

insert into dtb_news (news_date,rank, news_title, news_comment, creator_id, create_date, update_date) 
values(now(),1,'サイトオープンいたしました!','一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！',1, now(), now());

INSERT INTO dtb_deliv (name,service_name,confirm_url,rank,status,del_flg,creator_id,create_date,update_date)
VALUES ('ダミー配送業者', 'ダミー配送業者', '', 1, 1, 0, 2, now(), now());

INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 1);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 2);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 3);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 4);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 5);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 6);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 7);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 8);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 9);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 10);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 11);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 12);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 13);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 14);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 15);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 16);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 17);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 18);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 19);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 20);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 21);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 22);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 23);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 24);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 25);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 26);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 27);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 28);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 29);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 30);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 31);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 32);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 33);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 34);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 35);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 36);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 37);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 38);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 39);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 40);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 41);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 42);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 43);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 44);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 45);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 46);
INSERT INTO dtb_delivfee (deliv_id,fee,pref) VALUES (1, '1000', 47);

INSERT INTO dtb_delivtime (deliv_id, deliv_time) VALUES (1, '午前');
INSERT INTO dtb_delivtime (deliv_id, deliv_time) VALUES (1, '午後');

INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('郵便振替', 0, NULL, 1, 4, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('現金書留', 0, NULL, 1, 3, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('銀行振込', 0, NULL, 1, 2, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('代金引換', 0, NULL, 1, 1, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);

INSERT INTO dtb_products (name,deliv_fee,sale_limit,sale_unlimited,category_id,rank,status,product_flag,point_rate,comment1,comment2,comment3,comment4,comment5,comment6,file1,file2,file3,file4,file5,file6,main_list_comment,main_list_image,main_comment,main_image,main_large_image,sub_title1,sub_comment1,sub_image1,sub_large_image1,sub_title2,sub_comment2,sub_image2,sub_large_image2,sub_title3,sub_comment3,sub_image3,sub_large_image3,sub_title4,sub_comment4,sub_image4,sub_large_image4,sub_title5,sub_comment5,sub_image5,sub_large_image5,sub_title6,sub_comment6,sub_image6,sub_large_image6,del_flg,creator_id,create_date,update_date,deliv_date_id) 
VALUES ('アイスクリーム', NULL, NULL, 1, 5, 1, 1, '10010', 10, NULL, NULL, 'アイス,バニラ,チョコ,抹茶', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '暑い夏にどうぞ。', '08311201_44f65122ee5fe.jpg', '冷たいものはいかがですか？', '08311202_44f6515906a41.jpg', '08311203_44f651959bcb5.jpg', NULL, '<b>おいしいよ<b>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, now(), now(), 2);
INSERT INTO dtb_products (name,deliv_fee,sale_limit,sale_unlimited,category_id,rank,status,product_flag,point_rate,comment1,comment2,comment3,comment4,comment5,comment6,file1,file2,file3,file4,file5,file6,main_list_comment,main_list_image,main_comment,main_image,main_large_image,sub_title1,sub_comment1,sub_image1,sub_large_image1,sub_title2,sub_comment2,sub_image2,sub_large_image2,sub_title3,sub_comment3,sub_image3,sub_large_image3,sub_title4,sub_comment4,sub_image4,sub_large_image4,sub_title5,sub_comment5,sub_image5,sub_large_image5,sub_title6,sub_comment6,sub_image6,sub_large_image6,del_flg,creator_id,create_date,update_date,deliv_date_id)
VALUES ('おなべ', NULL, 5, NULL, 4, 1, 1, '11001', 5, NULL, NULL, '鍋,なべ,ナベ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '一人用からあります。', '08311311_44f661811fec0.jpg', 'たまには鍋でもどうでしょう。', '08311313_44f661dc649fb.jpg', '08311313_44f661e5698a6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, now(), now(), 3);

INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
DELETE FROM dtb_products_class;
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date) 
VALUES (1, 3, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), NULL);
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (2, 0, 0, 'nabe-01', 100, NULL, NULL, 1700, 1650, NULL, 2, now(), NULL);

INSERT INTO dtb_recommend_products (product_id,rank,comment,status,creator_id,create_date,update_date) VALUES (2, 4, 'お口直しに。', 0, 2, now(), now());

INSERT INTO dtb_class (name,status,rank,creator_id,create_date,update_date,del_flg,product_id) VALUES ('味', NULL, 1, 2, now(), now(), 0, NULL);
INSERT INTO dtb_class (name,status,rank,creator_id,create_date,update_date,del_flg,product_id) VALUES ('大きさ', NULL, 2, 2, now(), NULL, 0, NULL);

INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('バニラ', 1, NULL, 1, 2, now(), NULL, 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('チョコ', 1, NULL, 2, 2, now(), NULL, 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('抹茶', 1, NULL, 3, 2, now(), NULL, 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('L', 2, NULL, 1, 2, now(), NULL, 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('M', 2, NULL, 2, 2, now(), NULL, 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('S', 2, NULL, 3, 2, now(), NULL, 0);

INSERT INTO dtb_classcategory (classcategory_id, class_id, rank, creator_id, create_date, update_date) 
VALUES (0, 0, 0, 0, now(), now());
UPDATE dtb_classcategory SET classcategory_id = 0 WHERE class_id = 0;

INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('食品', 0, 1, 4, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('雑貨', 0, 1, 5, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('お菓子', 1, 2, 2, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('なべ', 1, 2, 3, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('アイス', 3, 3, 1, 2, now(), now(), 0);

INSERT INTO dtb_category_count VALUES (4, 1, now());
INSERT INTO dtb_category_count VALUES (5, 1, now());

INSERT INTO dtb_category_total_count VALUES (3, 1, now());
INSERT INTO dtb_category_total_count VALUES (1, 2, now());
INSERT INTO dtb_category_total_count VALUES (2, NULL, now());
INSERT INTO dtb_category_total_count VALUES (5, 1, now());
INSERT INTO dtb_category_total_count VALUES (4, 1, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第１条（会員）','1. 「会員」とは、当社が定める手続に従い本規約に同意の上、入会の申し込みを行う個人をいいます。
2. 「会員情報」とは、会員が当社に開示した会員の属性に関する情報および会員の取引に関する履歴等の情報をいいます。
3. 本規約は、すべての会員に適用され、登録手続時および登録後にお守りいただく規約です。',
12,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第２条（登録）','1. 会員資格
本規約に同意の上、所定の入会申込みをされたお客様は、所定の登録手続完了後に会員としての資格を有します。会員登録手続は、会員となるご本人が行ってください。代理による登録は一切認められません。なお、過去に会員資格が取り消された方やその他当社が相応しくないと判断した方からの会員申込はお断りする場合があります。

2. 会員情報の入力
会員登録手続の際には、入力上の注意をよく読み、所定の入力フォームに必要事項を正確に入力してください。会員情報の登録において、特殊記号・旧漢字・ローマ数字などはご使用になれません。これらの文字が登録された場合は当社にて変更致します。

3. パスワードの管理
(1)パスワードは会員本人のみが利用できるものとし、第三者に譲渡・貸与できないものとします。
(2)パスワードは、他人に知られることがないよう定期的に変更する等、会員本人が責任をもって管理してください。
(3)パスワードを用いて当社に対して行われた意思表示は、会員本人の意思表示とみなし、そのために生じる支払等はすべて会員の責任となります。',
11,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第３条（変更）','1. 会員は、氏名、住所など当社に届け出た事項に変更があった場合には、速やかに当社に連絡するものとします。
2. 変更登録がなされなかったことにより生じた損害について、当社は一切責任を負いません。また、変更登録がなされた場合でも、変更登録前にすでに手続がなされた取引は、変更登録前の情報に基づいて行われますのでご注意ください。',
10,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第４条（退会）','会員が退会を希望する場合には、会員本人が退会手続きを行ってください。所定の退会手続の終了後に、退会となります。',
9,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第５条（会員資格の喪失及び賠償義務）','1. 会員が、会員資格取得申込の際に虚偽の申告をしたとき、通信販売による代金支払債務を怠ったとき、その他当社が会員として不適当と認める事由があるときは、当社は、会員資格を取り消すことができることとします。
2. 会員が、以下の各号に定める行為をしたときは、これにより当社が被った損害を賠償する責任を負います。
(1)会員番号、パスワードを不正に利用すること
(2)当ホームページにアクセスして情報を改ざんしたり、当ホームページに有害なコンピュータプログラムを送信するなどして、当社の営業を妨害すること
(3)当社が扱う商品の知的所有権を侵害する行為をすること
(4)その他、この利用規約に反する行為をすること',
8,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第６条（会員情報の取扱い）','1. 当社は、原則として会員情報を会員の事前の同意なく第三者に対して開示することはありません。ただし、次の各号の場合には、会員の事前の同意なく、当社は会員情報その他のお客様情報を開示できるものとします。
(1)法令に基づき開示を求められた場合
(2)当社の権利、利益、名誉等を保護するために必要であると当社が判断した場合
2. 会員情報につきましては、当社の「個人情報保護への取組み」に従い、当社が管理します。当社は、会員情報を、会員へのサービス提供、サービス内容の向上、サービスの利用促進、およびサービスの健全かつ円滑な運営の確保を図る目的のために、当社おいて利用することができるものとします。
3. 当社は、会員に対して、メールマガジンその他の方法による情報提供（広告を含みます）を行うことができるものとします。会員が情報提供を希望しない場合は、当社所定の方法に従い、その旨を通知して頂ければ、情報提供を停止します。ただし、本サービス運営に必要な情報提供につきましては、会員の希望により停止をすることはできません。',
7,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第７条（禁止事項）','本サービスの利用に際して、会員に対し次の各号の行為を行うことを禁止します。

1. 法令または本規約、本サービスご利用上のご注意、本サービスでのお買い物上のご注意その他の本規約等に違反すること
2. 当社、およびその他の第三者の権利、利益、名誉等を損ねること
3. 青少年の心身に悪影響を及ぼす恐れがある行為、その他公序良俗に反する行為を行うこと
4. 他の利用者その他の第三者に迷惑となる行為や不快感を抱かせる行為を行うこと
5. 虚偽の情報を入力すること
6. 有害なコンピュータプログラム、メール等を送信または書き込むこと
7. 当社のサーバその他のコンピュータに不正にアクセスすること
8. パスワードを第三者に貸与・譲渡すること、または第三者と共用すること
9. その他当社が不適切と判断すること',
6,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第８条（サービスの中断・停止等）','1. 当社は、本サービスの稼動状態を良好に保つために、次の各号の一に該当する場合、予告なしに、本サービスの提供全てあるいは一部を停止することがあります。
(1)システムの定期保守および緊急保守のために必要な場合
(2)システムに負荷が集中した場合
(3)火災、停電、第三者による妨害行為などによりシステムの運用が困難になった場合
(4)その他、止むを得ずシステムの停止が必要と当社が判断した場合',
5,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第９条（サービスの変更・廃止）','当社は、その判断によりサービスの全部または一部を事前の通知なく、適宜変更・廃止できるものとします。',
4,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第10条（免責）','1. 通信回線やコンピュータなどの障害によるシステムの中断・遅滞・中止・データの消失、データへの不正アクセスにより生じた損害、その他当社のサービスに関して会員に生じた損害について、当社は一切責任を負わないものとします。
2. 当社は、当社のウェブページ・サーバ・ドメインなどから送られるメール・コンテンツに、コンピュータ・ウィルスなどの有害なものが含まれていないことを保証いたしません。
3. 会員が本規約等に違反したことによって生じた損害については、当社は一切責任を負いません。',
3,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第11条（本規約の改定）','当社は、本規約を任意に改定できるものとし、また、当社において本規約を補充する規約（以下「補充規約」といいます）を定めることができます。本規約の改定または補充は、改定後の本規約または補充規約を当社所定のサイトに掲示したときにその効力を生じるものとします。この場合、会員は、改定後の規約および補充規約に従うものと致します。',
2,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('第12条（準拠法、管轄裁判所）','本規約に関して紛争が生じた場合、当社本店所在地を管轄する地方裁判所を第一審の専属的合意管轄裁判所とします。 ',
1,0,Now(),0, now());
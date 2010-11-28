INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (1, 'カテゴリ', 'bloc/category.tpl', 'category', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/category.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (2, '利用ガイド', 'bloc/guide.tpl', 'guide', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (3, 'かごの中', 'bloc/cart.tpl', 'cart', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/cart.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (4, '商品検索', 'bloc/search_products.tpl', 'search_products', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/search_products.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (5, '新着情報', 'bloc/news.tpl', 'news', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/news.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (6, 'ログイン', 'bloc/login.tpl', 'login', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/login.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (7, 'おすすめ商品', 'bloc/best5.tpl', 'best5', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/best5.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (8, 'カレンダー', 'bloc/calendar.tpl', 'calendar', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/calendar.php', 1);

INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 1, 1, 2, 'category', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 1, 2, 3, 'guide', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 1, 3, 1, 'cart', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 3, 4, 2, 'search_products', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 4, 5, 1, 'news', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 3, 6, 1, 'login', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (1, 4, 7, 2, 'best5', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (2, 1, 1, 2, 'category', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (2, 1, 2, 3, 'guide', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (2, 1, 3, 1, 'cart', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (3, 1, 1, 2, 'category', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (3, 1, 2, 3, 'guide', 0);
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename, anywhere) VALUES (3, 1, 3, 1, 'cart', 0);

INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '食品', 0, 1, 4, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '雑貨', 0, 1, 5, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (3, 'お菓子', 1, 2, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (4, 'なべ', 1, 2, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, level, rank, creator_id, create_date, update_date, del_flg) VALUES (5, 'アイス', 3, 3, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);

INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (3, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (1, 2, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (2, NULL, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);

INSERT INTO dtb_class (class_id, name, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '味', 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_class (class_id, name, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '大きさ', 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (1, 'バニラ', 1, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (2, 'チョコ', 1, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '抹茶', 1, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (4, 'L', 2, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (5, 'M', 2, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (6, 'S', 2, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, rank, creator_id, create_date, update_date, del_flg) VALUES (0, NULL, 0, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (1, 1, 'product_id', '商品ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (2, 1, 'product_class_id', '規格ID', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (3, 1, 'classcategory_id1', '規格名1', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (4, 1, 'classcategory_id2', '規格名2', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (5, 1, 'name', '商品名', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (6, 1, 'status', '公開フラグ', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (7, 1, 'product_flag', '商品ステータス', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (8, 1, 'product_code', '商品コード', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (9, 1, 'price01', '通常価格', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (10, 1, 'price02', '販売価格', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (11, 1, 'stock', '在庫数', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (12, 1, 'deliv_fee', '送料', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (13, 1, 'point_rate', 'ポイント付与率', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (14, 1, 'sale_limit', '購入制限', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (15, 1, 'comment1', 'メーカーURL', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (16, 1, 'comment3', '検索ワード', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (17, 1, 'note', '備考欄(SHOP専用)', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (18, 1, 'main_list_comment', '一覧-メインコメント', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (19, 1, 'main_list_image', '一覧-メイン画像', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (20, 1, 'main_comment', '詳細-メインコメント', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (21, 1, 'main_image', '詳細-メイン画像', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (22, 1, 'main_large_image', '詳細-メイン拡大画像 ', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (25, 1, 'sub_title1', '詳細-サブタイトル(1)', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (26, 1, 'sub_comment1', '詳細-サブコメント(1)', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (27, 1, 'sub_image1', '詳細-サブ画像(1)', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (28, 1, 'sub_large_image1', '詳細-サブ拡大画像(1)', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (29, 1, 'sub_title2', '詳細-サブタイトル(2)', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (30, 1, 'sub_comment2', '詳細-サブコメント(2)', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (31, 1, 'sub_image2', '詳細-サブ画像(2)', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (32, 1, 'sub_large_image2', '詳細-サブ拡大画像(2)', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (33, 1, 'sub_title3', '詳細-サブタイトル(3)', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (34, 1, 'sub_comment3', '詳細-サブコメント(3)', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (35, 1, 'sub_image3', '詳細-サブ画像(3)', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (36, 1, 'sub_large_image3', '詳細-サブ拡大画像(3)', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (37, 1, 'sub_title4', '詳細-サブタイトル(4)', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (38, 1, 'sub_comment4', '詳細-サブコメント(4)', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (39, 1, 'sub_image4', '詳細-サブ画像(4)', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (40, 1, 'sub_large_image4', '詳細-サブ拡大画像(4)', 40, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (41, 1, 'sub_title5', '詳細-サブタイトル(5)', 41, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (42, 1, 'sub_comment5', '詳細-サブコメント(5)', 42, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (43, 1, 'sub_image5', '詳細-サブ画像(5)', 43, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (44, 1, 'sub_large_image5', '詳細-サブ拡大画像(5)', 44, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (45, 1, 'deliv_date_id', '発送日目安', 45, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (46, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 0) AS recommend_product_id1', '関連商品(1)', 46, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (47, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 0) AS recommend_comment1', '関連商品コメント(1)', 47, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (48, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 1) AS recommend_product_id2', '関連商品(2)', 48, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (49, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 1) AS recommend_comment2', '関連商品コメント(2)', 49, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (50, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 2) AS recommend_product_id3', '関連商品(3)', 50, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (51, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 2) AS recommend_comment3', '関連商品コメント(3)', 51, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (52, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 3) AS recommend_product_id4', '関連商品(4)', 52, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (53, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 3) AS recommend_comment4', '関連商品コメント(4)', 53, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (54, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 4) AS recommend_product_id5', '関連商品(5)', 54, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (55, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 4) AS recommend_comment5', '関連商品コメント(5)', 55, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (56, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 5) AS recommend_product_id6', '関連商品(6)', 56, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (57, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY rank DESC, recommend_product_id DESC limit 1 offset 5) AS recommend_comment6', '関連商品コメント(6)', 57, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (58, 1, 'category_id', 'カテゴリID', 58, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (59, 1, 'down', '実商品・ダウンロード', 59, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (60, 1, 'down_filename', 'ダウンロードファイル名', 60, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (61, 1, 'down_realfilename', 'ダウンロード商品用ファイルアップロード', 61, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (62, 2, 'customer_id', '顧客ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (63, 2, 'name01', 'お名前(姓)', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (64, 2, 'name02', 'お名前(名)', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (65, 2, 'kana01', 'お名前(フリガナ・姓)', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (66, 2, 'kana02', 'お名前(フリガナ・名)', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (67, 2, 'zip01', '郵便番号1', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (68, 2, 'zip02', '郵便番号2', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (69, 2, 'pref', '都道府県', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (70, 2, 'addr01', '住所1', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (71, 2, 'addr02', '住所2', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (72, 2, 'email', 'E-MAIL', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (73, 2, 'tel01', 'TEL1', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (74, 2, 'tel02', 'TEL2', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (75, 2, 'tel03', 'TEL3', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (76, 2, 'fax01', 'FAX1', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (77, 2, 'fax02', 'FAX2', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (78, 2, 'fax03', 'FAX3', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (79, 2, 'sex', '性別', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (80, 2, 'job', '職業', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (81, 2, 'birth', '誕生日', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (82, 2, 'first_buy_date', '初回購入日', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (83, 2, 'last_buy_date', '最終購入日', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (84, 2, 'buy_times', '購入回数', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (85, 2, 'point', 'ポイント残高', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (86, 2, 'note', '備考', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (87, 2, 'create_date', '登録日', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (88, 2, 'update_date', '更新日', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (89, 3, 'order_id', '注文番号', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (90, 3, 'customer_id', '顧客ID', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (91, 3, 'message', '要望等', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (92, 3, 'order_name01', '顧客名(姓)', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (93, 3, 'order_name02', '顧客名(名)', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (94, 3, 'order_kana01', '顧客名(フリガナ・姓)', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (95, 3, 'order_kana02', '顧客名(フリガナ・名)', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (96, 3, 'order_email', 'メールアドレス', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (97, 3, 'order_tel01', '電話番号1', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (98, 3, 'order_tel02', '電話番号2', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (99, 3, 'order_tel03', '電話番号3', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (100, 3, 'order_fax01', 'FAX1', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (101, 3, 'order_fax02', 'FAX2', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (102, 3, 'order_fax03', 'FAX3', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (103, 3, 'order_zip01', '郵便番号1', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (104, 3, 'order_zip02', '郵便番号2', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (105, 3, '(SELECT pref_name FROM mtb_pref WHERE pref_id = dtb_order.order_pref)', '都道府県', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (106, 3, 'order_addr01', '住所1', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (107, 3, 'order_addr02', '住所2', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (108, 3, 'order_sex', '性別', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (109, 3, 'order_birth', '生年月日', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (110, 3, 'order_job', '職種', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (111, 3, 'deliv_name01', 'お届け先名前(姓)', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (112, 3, 'deliv_name02', 'お届け先名前(名)', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (113, 3, 'deliv_kana01', 'お届け先(フリガナ・姓)', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (114, 3, 'deliv_kana02', 'お届け先(フリガナ・名)', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (115, 3, 'deliv_tel01', '電話番号1', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (116, 3, 'deliv_tel02', '電話番号2', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (117, 3, 'deliv_tel03', '電話番号3', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (118, 3, 'deliv_fax01', 'FAX1', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (119, 3, 'deliv_fax02', 'FAX2', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (120, 3, 'deliv_fax03', 'FAX3', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (121, 3, 'deliv_zip01', '郵便番号1', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (122, 3, 'deliv_zip02', '郵便番号2', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (123, 3, '(SELECT pref_name FROM mtb_pref WHERE pref_id = dtb_order.deliv_pref)', '都道府県', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (124, 3, 'deliv_addr01', '住所1', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (125, 3, 'deliv_addr02', '住所2', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (126, 3, 'subtotal', '小計', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (127, 3, 'discount', '値引き', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (128, 3, 'deliv_fee', '送料', 40, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (129, 3, 'charge', '手数料', 41, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (130, 3, 'use_point', '使用ポイント', 42, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (131, 3, 'add_point', '加算ポイント', 43, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (132, 3, 'tax', '税金', 44, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (133, 3, 'total', '合計', 45, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (134, 3, 'payment_total', 'お支払い合計', 46, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (135, 3, 'payment_method', '支払い方法', 47, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (136, 3, 'deliv_time', 'お届け時間', 48, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (137, 3, 'deliv_no', '配送伝票番号', 49, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (138, 3, 'note', 'SHOPメモ', 50, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (139, 3, 'status', '対応状況', 51, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (140, 3, 'create_date', '注文日時', 52, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (141, 3, 'update_date', '更新日時', 53, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (142, 3, 'deliv_date', 'お届け指定日', 54, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (143, 4, 'order_id', '注文番号', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (144, 4, 'customer_id', '顧客ID', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (145, 4, 'message', '要望等', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (146, 4, 'order_name01', '顧客名1', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (147, 4, 'order_name02', '顧客名2', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (148, 4, 'order_kana01', '顧客名カナ1', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (149, 4, 'order_kana02', '顧客名カナ2', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (150, 4, 'order_email', 'メールアドレス', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (151, 4, 'order_tel01', '電話番号1', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (152, 4, 'order_tel02', '電話番号2', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (153, 4, 'order_tel03', '電話番号3', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (154, 4, 'order_fax01', 'FAX1', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (155, 4, 'order_fax02', 'FAX2', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (156, 4, 'order_fax03', 'FAX3', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (157, 4, 'order_zip01', '郵便番号1', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (158, 4, 'order_zip02', '郵便番号2', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (159, 4, '(SELECT pref_name FROM mtb_pref WHERE pref_id = dtb_order.order_pref)', '都道府県', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (160, 4, 'order_addr01', '住所1', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (161, 4, 'order_addr02', '住所2', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (162, 4, 'order_sex', '性別', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (163, 4, 'order_birth', '生年月日', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (164, 4, 'order_job', '職種', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (165, 4, 'deliv_name01', 'お届け先名前', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (166, 4, 'deliv_name02', 'お届け先名前', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (167, 4, 'deliv_kana01', 'お届け先カナ', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (168, 4, 'deliv_kana02', 'お届け先カナ', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (169, 4, 'deliv_tel01', '電話番号1', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (170, 4, 'deliv_tel02', '電話番号2', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (171, 4, 'deliv_tel03', '電話番号3', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (172, 4, 'deliv_fax01', 'FAX1', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (173, 4, 'deliv_fax02', 'FAX2', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (174, 4, 'deliv_fax03', 'FAX3', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (175, 4, 'deliv_zip01', '郵便番号1', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (176, 4, 'deliv_zip02', '郵便番号2', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (177, 4, '(SELECT pref_name FROM mtb_pref WHERE pref_id = dtb_order.deliv_pref)', '都道府県', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (178, 4, 'deliv_addr01', '住所1', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (179, 4, 'deliv_addr02', '住所2', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (180, 4, 'payment_total', 'お支払い合計', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (181, 5, 'category_id', 'カテゴリID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (182, 5, 'category_name', 'カテゴリ名', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (183, 5, 'parent_category_id', '親カテゴリID', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (184, 5, 'level', '階層', NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv (no, csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (185, 5, 'rank', '表示ランク', NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_deliv (deliv_id, product_type_id, name, service_name, confirm_url, rank, status, del_flg, creator_id, create_date, update_date) VALUES (1, 1, 'サンプル業者', 'サンプル業者', NULL, 1, 1, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 1, 1000, 1);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 2, 1000, 2);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 3, 1000, 3);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 4, 1000, 4);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 5, 1000, 5);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 6, 1000, 6);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 7, 1000, 7);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 8, 1000, 8);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 9, 1000, 9);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 10, 1000, 10);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 11, 1000, 11);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 12, 1000, 12);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 13, 1000, 13);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 14, 1000, 14);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 15, 1000, 15);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 16, 1000, 16);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 17, 1000, 17);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 18, 1000, 18);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 19, 1000, 19);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 20, 1000, 20);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 21, 1000, 21);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 22, 1000, 22);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 23, 1000, 23);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 24, 1000, 24);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 25, 1000, 25);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 26, 1000, 26);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 27, 1000, 27);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 28, 1000, 28);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 29, 1000, 29);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 30, 1000, 30);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 31, 1000, 31);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 32, 1000, 32);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 33, 1000, 33);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 34, 1000, 34);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 35, 1000, 35);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 36, 1000, 36);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 37, 1000, 37);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 38, 1000, 38);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 39, 1000, 39);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 40, 1000, 40);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 41, 1000, 41);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 42, 1000, 42);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 43, 1000, 43);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 44, 1000, 44);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 45, 1000, 45);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 46, 1000, 46);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 47, 1000, 47);

INSERT INTO dtb_delivtime (deliv_id, time_id, deliv_time) VALUES (1, 1, '午前');
INSERT INTO dtb_delivtime (deliv_id, time_id, deliv_time) VALUES (1, 2, '午後');

INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '元旦(1月1日)', 1, 1, 100, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '成人の日(1月第2月曜日)', 1, 14, 98, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '建国記念の日(2月11日)', 2, 11, 96, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (4, '春分の日(3月21日)', 3, 21, 94, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (5, '昭和の日(4月29日)', 4, 29, 92, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (6, '憲法記念日(5月3日)', 5, 3, 90, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (7, 'みどりの日(5月4日)', 5, 4, 88, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (8, 'こどもの日(5月5日)', 5, 5, 86, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (9, '海の日(7月第3月曜日)', 7, 21, 84, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (10, '敬老の日(9月第3月曜日)', 9, 15, 82, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (11, '秋分の日(9月23日)', 9, 23, 80, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (12, '体育の日(10月第2月曜日)', 10, 13, 78, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (13, '文化の日(11月3日)', 11, 3, 76, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (14, '勤労感謝の日(11月23日)', 11, 23, 74, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_holiday (holiday_id, title, month, day, rank, creator_id, create_date, update_date, del_flg) VALUES (15, '天皇誕生日(12月23日)', 12, 23, 72, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '第1条 (会員)', '1. 「会員」とは、当社が定める手続に従い本規約に同意の上、入会の申し込みを行う個人をいいます。
2. 「会員情報」とは、会員が当社に開示した会員の属性に関する情報および会員の取引に関する履歴等の情報をいいます。
3. 本規約は、すべての会員に適用され、登録手続時および登録後にお守りいただく規約です。', 12, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '第2条 (登録)', '1. 会員資格
本規約に同意の上、所定の入会申込みをされたお客様は、所定の登録手続完了後に会員としての資格を有します。会員登録手続は、会員となるご本人が行ってください。代理による登録は一切認められません。なお、過去に会員資格が取り消された方やその他当社が相応しくないと判断した方からの会員申込はお断りする場合があります。

2. 会員情報の入力
会員登録手続の際には、入力上の注意をよく読み、所定の入力フォームに必要事項を正確に入力してください。会員情報の登録において、特殊記号・旧漢字・ローマ数字などはご使用になれません。これらの文字が登録された場合は当社にて変更致します。

3. パスワードの管理
(1)パスワードは会員本人のみが利用できるものとし、第三者に譲渡・貸与できないものとします。
(2)パスワードは、他人に知られることがないよう定期的に変更する等、会員本人が責任をもって管理してください。
(3)パスワードを用いて当社に対して行われた意思表示は、会員本人の意思表示とみなし、そのために生じる支払等はすべて会員の責任となります。', 11, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '第3条 (変更)', '1. 会員は、氏名、住所など当社に届け出た事項に変更があった場合には、速やかに当社に連絡するものとします。
2. 変更登録がなされなかったことにより生じた損害について、当社は一切責任を負いません。また、変更登録がなされた場合でも、変更登録前にすでに手続がなされた取引は、変更登録前の情報に基づいて行われますのでご注意ください。', 10, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (4, '第4条 (退会)', '会員が退会を希望する場合には、会員本人が退会手続きを行ってください。所定の退会手続の終了後に、退会となります。', 9, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (5, '第5条 (会員資格の喪失及び賠償義務)', '1. 会員が、会員資格取得申込の際に虚偽の申告をしたとき、通信販売による代金支払債務を怠ったとき、その他当社が会員として不適当と認める事由があるときは、当社は、会員資格を取り消すことができることとします。
2. 会員が、以下の各号に定める行為をしたときは、これにより当社が被った損害を賠償する責任を負います。
(1)会員番号、パスワードを不正に使用すること
(2)当ホームページにアクセスして情報を改ざんしたり、当ホームページに有害なコンピュータプログラムを送信するなどして、当社の営業を妨害すること
(3)当社が扱う商品の知的所有権を侵害する行為をすること
(4)その他、この利用規約に反する行為をすること', 8, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (6, '第6条 (会員情報の取扱い)', '1. 当社は、原則として会員情報を会員の事前の同意なく第三者に対して開示することはありません。ただし、次の各号の場合には、会員の事前の同意なく、当社は会員情報その他のお客様情報を開示できるものとします。
(1)法令に基づき開示を求められた場合
(2)当社の権利、利益、名誉等を保護するために必要であると当社が判断した場合
2. 会員情報につきましては、当社の「個人情報保護への取組み」に従い、当社が管理します。当社は、会員情報を、会員へのサービス提供、サービス内容の向上、サービスの利用促進、およびサービスの健全かつ円滑な運営の確保を図る目的のために、当社おいて利用することができるものとします。
3. 当社は、会員に対して、メールマガジンその他の方法による情報提供(広告を含みます)を行うことができるものとします。会員が情報提供を希望しない場合は、当社所定の方法に従い、その旨を通知して頂ければ、情報提供を停止します。ただし、本サービス運営に必要な情報提供につきましては、会員の希望により停止をすることはできません。', 7, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (7, '第7条 (禁止事項)', '本サービスの利用に際して、会員に対し次の各号の行為を行うことを禁止します。

1. 法令または本規約、本サービスご利用上のご注意、本サービスでのお買い物上のご注意その他の本規約等に違反すること
2. 当社、およびその他の第三者の権利、利益、名誉等を損ねること
3. 青少年の心身に悪影響を及ぼす恐れがある行為、その他公序良俗に反する行為を行うこと
4. 他の利用者その他の第三者に迷惑となる行為や不快感を抱かせる行為を行うこと
5. 虚偽の情報を入力すること
6. 有害なコンピュータプログラム、メール等を送信または書き込むこと
7. 当社のサーバその他のコンピュータに不正にアクセスすること
8. パスワードを第三者に貸与・譲渡すること、または第三者と共用すること
9. その他当社が不適切と判断すること', 6, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (8, '第8条 (サービスの中断・停止等)', '1. 当社は、本サービスの稼動状態を良好に保つために、次の各号の一に該当する場合、予告なしに、本サービスの提供全てあるいは一部を停止することがあります。
(1)システムの定期保守および緊急保守のために必要な場合
(2)システムに負荷が集中した場合
(3)火災、停電、第三者による妨害行為などによりシステムの運用が困難になった場合
(4)その他、止むを得ずシステムの停止が必要と当社が判断した場合', 5, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (9, '第9条 (サービスの変更・廃止)', '当社は、その判断によりサービスの全部または一部を事前の通知なく、適宜変更・廃止できるものとします。', 4, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (10, '第10条 (免責)', '1. 通信回線やコンピュータなどの障害によるシステムの中断・遅滞・中止・データの消失、データへの不正アクセスにより生じた損害、その他当社のサービスに関して会員に生じた損害について、当社は一切責任を負わないものとします。
2. 当社は、当社のウェブページ・サーバ・ドメインなどから送られるメール・コンテンツに、コンピュータ・ウィルスなどの有害なものが含まれていないことを保証いたしません。
3. 会員が本規約等に違反したことによって生じた損害については、当社は一切責任を負いません。', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (11, '第11条 (本規約の改定)', '当社は、本規約を任意に改定できるものとし、また、当社において本規約を補充する規約(以下「補充規約」といいます)を定めることができます。本規約の改定または補充は、改定後の本規約または補充規約を当社所定のサイトに掲示したときにその効力を生じるものとします。この場合、会員は、改定後の規約および補充規約に従うものと致します。', 2, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (12, '第12条 (準拠法、管轄裁判所)', '本規約に関して紛争が生じた場合、当社本店所在地を管轄する地方裁判所を第一審の専属的合意管轄裁判所とします。 ', 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_mailtemplate (template_id, subject, header, footer, creator_id, del_flg, create_date, update_date) VALUES (1, 'ご注文ありがとうございます', 'この度はご注文いただき誠にありがとうございます。
下記ご注文内容にお間違えがないかご確認下さい。

', '

===============================================================


このメッセージはお客様へのお知らせ専用ですので、
このメッセージへの返信としてご質問をお送りいただいても回答できません。
ご了承ください。

ご質問やご不明な点がございましたら、こちらからお願いいたします。
http://------.co.jp

', 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_mailtemplate (template_id, subject, header, footer, creator_id, del_flg, create_date, update_date) VALUES (5, 'お問い合わせを受け付けました', NULL, NULL, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_member (member_id, name, department, login_id, password, authority, rank, work, del_flg, creator_id, update_date, create_date, login_date) VALUES (1, 'dummy', NULL, 'dummy', 'dummy', 0, 0, 1, 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);

INSERT INTO dtb_module (module_id, module_code, module_name, sub_data, auto_update_flg, del_flg, create_date, update_date) VALUES (0, '0', 'patch', NULL, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_news (news_id, news_date, rank, news_title, news_comment, news_url, news_select, link_method, creator_id, create_date, update_date, del_flg) VALUES (1, '2010-08-19 00:00:00', 1, 'サイトオープンいたしました!', '一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！', NULL, 0, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (1, 'TOPページ', 'index.php', ' ', 'user_data/templates/', 'top', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (2, '商品一覧ページ', 'products/list.php', ' ', 'user_data/templates/', 'list', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (3, '商品詳細ページ', 'products/detail.php', ' ', 'user_data/templates/', 'detail', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (4, 'MYページ', 'mypage/index.php', ' ', NULL, NULL, 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (0, 'プレビューデータ', 'preview', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_payment (payment_id, payment_method, charge, rule, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (1, 'クレジット', 0, NULL, 5, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (2, '郵便振替', 0, NULL, 4, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (3, '現金書留', 0, NULL, 3, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (4, '銀行振込', 0, NULL, 2, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, rule, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (5, '代金引換', 0, NULL, 1, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (1, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (1, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (1, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (1, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (1, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (2, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (2, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (2, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (2, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (2, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (3, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (3, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (3, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (3, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (3, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (4, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (4, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (4, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (4, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (4, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (5, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (5, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (5, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (5, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (5, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (6, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (6, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (6, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (6, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (6, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (7, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (7, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (7, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (7, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (7, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (8, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (8, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (8, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (8, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (8, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (9, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (9, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (9, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (9, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (9, 5, 5);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (10, 1, 1);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (10, 2, 2);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (10, 3, 3);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (10, 4, 4);
INSERT INTO dtb_payment_options (product_class_id, payment_id, rank) VALUES (10, 5, 5);

INSERT INTO dtb_product_categories (product_id, category_id, rank) VALUES (1, 5, 1);
INSERT INTO dtb_product_categories (product_id, category_id, rank) VALUES (2, 4, 1);

INSERT INTO dtb_products (product_id, name, maker_id, rank, status, comment1, comment2, comment3, comment4, comment5, comment6, note, main_list_comment, main_list_image, main_comment, main_image, main_large_image, sub_title1, sub_comment1, sub_image1, sub_large_image1, sub_title2, sub_comment2, sub_image2, sub_large_image2, sub_title3, sub_comment3, sub_image3, sub_large_image3, sub_title4, sub_comment4, sub_image4, sub_large_image4, sub_title5, sub_comment5, sub_image5, sub_large_image5, sub_title6, sub_comment6, sub_image6, sub_large_image6, del_flg, creator_id, create_date, update_date, deliv_date_id) VALUES (1, 'アイスクリーム', NULL, 1, 1, NULL, NULL, 'アイス,バニラ,チョコ,抹茶', NULL, NULL, NULL, NULL, '暑い夏にどうぞ。', '08311201_44f65122ee5fe.jpg', '冷たいものはいかがですか?', '08311202_44f6515906a41.jpg', '08311203_44f651959bcb5.jpg', NULL, '<b>おいしいよ<b>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 2);
INSERT INTO dtb_products (product_id, name, maker_id, rank, status, comment1, comment2, comment3, comment4, comment5, comment6, note, main_list_comment, main_list_image, main_comment, main_image, main_large_image, sub_title1, sub_comment1, sub_image1, sub_large_image1, sub_title2, sub_comment2, sub_image2, sub_large_image2, sub_title3, sub_comment3, sub_image3, sub_large_image3, sub_title4, sub_comment4, sub_image4, sub_large_image4, sub_title5, sub_comment5, sub_image5, sub_large_image5, sub_title6, sub_comment6, sub_image6, sub_large_image6, del_flg, creator_id, create_date, update_date, deliv_date_id) VALUES (2, 'おなべ', NULL, 1, 1, NULL, NULL, '鍋,なべ,ナベ', NULL, NULL, NULL, NULL, '一人用からあります。', '08311311_44f661811fec0.jpg', 'たまには鍋でもどうでしょう。', '08311313_44f661dc649fb.jpg', '08311313_44f661e5698a6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 3);

INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(1, 1, 10, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(2, 1, 11, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(3, 1, 12, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(4, 1, 13, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(5, 1, 14, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(6, 1, 15, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(7, 1, 16, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(8, 1, 17, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(9, 1, 18, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);
INSERT INTO dtb_products_class (product_class_id, product_id, class_combination_id, product_code, stock, stock_unlimited, sale_limit, price01, price02, deliv_fee, point_rate, creator_id, create_date, update_date, del_flg, product_type_id, down_filename, down_realfilename) VALUES(10, 2, NULL, 'nabe-01', 100, 0, 5, 1700, 1650, NULL, 10, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1, NULL, NULL);

INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(1, NULL, 3, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(2, NULL, 3, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(3, NULL, 3, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(4, NULL, 2, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(5, NULL, 2, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(6, NULL, 2, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(7, NULL, 1, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(8, NULL, 1, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(9, NULL, 1, 1);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(10, 1, 6, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(11, 2, 5, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(12, 3, 4, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(13, 4, 6, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(14, 5, 5, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(15, 6, 4, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(16, 7, 6, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(17, 8, 5, 2);
INSERT INTO dtb_class_combination (class_combination_id, parent_class_combination_id, classcategory_id, level) VALUES(18, 9, 4, 2);

INSERT INTO dtb_product_status (product_status_id, product_id, creator_id, create_date, update_date, del_flg) VALUES (1, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);

INSERT INTO dtb_recommend_products (product_id, recommend_product_id, rank, comment, status, creator_id, create_date, update_date) VALUES (2, 1, 4, 'お口直しに。', 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_site_control (control_id, control_title, control_text, control_flg, del_flg, memo, create_date, update_date) VALUES (1, 'トラックバック機能', 'トラックバック機能を使用するかどうかを決定します。', 2, 0, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO dtb_templates (template_code, template_name, create_date, update_date) VALUES ('default', 'デフォルト', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (0, 'table', 0);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (1, 'tr', 1);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (2, 'td', 2);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (3, 'a', 3);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (4, 'b', 4);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (5, 'blink', 5);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (6, 'br', 6);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (7, 'center', 7);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (8, 'font', 8);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (9, 'h', 9);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (10, 'hr', 10);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (11, 'img', 11);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (12, 'li', 12);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (13, 'strong', 13);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (14, 'p', 14);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (15, 'div', 15);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (16, 'i', 16);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (17, 'u', 17);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (18, 's', 18);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (19, '/table', 19);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (20, '/tr', 20);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (21, '/td', 21);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (22, '/a', 22);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (23, '/b', 23);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (24, '/blink', 24);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (25, '/br', 25);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (26, '/center', 26);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (27, '/font', 27);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (28, '/h', 28);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (29, '/hr', 29);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (30, '/img', 30);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (31, '/li', 31);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (32, '/strong', 32);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (33, '/p', 33);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (34, '/div', 34);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (35, '/i', 35);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (36, '/u', 36);
INSERT INTO mtb_allowed_tag (id, name, rank) VALUES (37, '/s', 37);

INSERT INTO mtb_authority (id, name, rank) VALUES (0, 'システム管理者', 0);
INSERT INTO mtb_authority (id, name, rank) VALUES (1, '店舗オーナー', 1);

INSERT INTO mtb_class (id, name, rank) VALUES (1, '規格無し', 0);
INSERT INTO mtb_class (id, name, rank) VALUES (2, '規格有り', 1);

INSERT INTO mtb_conveni_message (id, name, rank) VALUES (1, '上記URLから振込票を印刷、もしくは振込票番号を紙に控えて、全国のセブンイレブンにてお支払いください。', 0);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (2, '企業コード、受付番号を紙などに控えて、全国のファミリーマートにお支払いください。', 1);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (3, '上記URLから振込票を印刷、もしくはケータイ決済番号を紙などに控えて、全国のサークルKサンクスにてお支払ください。', 2);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (4, '振込票番号を紙に控えて、全国のローソンまたはセイコーマートにてお支払いください。', 3);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (5, '上記URLから振込票を印刷し、全国のミニストップ・デイリーヤマザキ・ヤマザキデイリーストアにてお支払いください。', 4);

INSERT INTO mtb_convenience (id, name, rank) VALUES (1, 'セブンイレブン', 0);
INSERT INTO mtb_convenience (id, name, rank) VALUES (2, 'ファミリーマート', 1);
INSERT INTO mtb_convenience (id, name, rank) VALUES (3, 'サークルKサンクス', 2);
INSERT INTO mtb_convenience (id, name, rank) VALUES (4, 'ローソン・セイコーマート', 3);
INSERT INTO mtb_convenience (id, name, rank) VALUES (5, 'ミニストップ・デイリーヤマザキ・ヤマザキデイリーストア', 4);

INSERT INTO mtb_db (id, name, rank) VALUES (1, 'PostgreSQL', 0);
INSERT INTO mtb_db (id, name, rank) VALUES (2, 'MySQL', 1);

INSERT INTO mtb_delivery_date (id, name, rank) VALUES (1, '即日', 0);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (2, '1～2日後', 1);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (3, '3～4日後', 2);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (4, '1週間以降', 3);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (5, '2週間以降', 4);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (6, '3週間以降', 5);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (7, '1ヶ月以降', 6);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (8, '2ヶ月以降', 7);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (9, 'お取り寄せ(商品入荷後)', 8);

INSERT INTO mtb_disable_logout (id, name, rank) VALUES (1, '/shopping/deliv.php', 0);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (2, '/shopping/payment.php', 1);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (3, '/shopping/confirm.php', 2);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (4, '/shopping/card.php', 3);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (5, '/shopping/loan.php', 4);

INSERT INTO mtb_disp (id, name, rank) VALUES (1, '公開', 0);
INSERT INTO mtb_disp (id, name, rank) VALUES (2, '非公開', 1);

INSERT INTO mtb_product_type (id, name, rank) VALUES (1, '通常商品', 0);
INSERT INTO mtb_product_type (id, name, rank) VALUES (2, 'ダウンロード商品', 1);

INSERT INTO mtb_job (id, name, rank) VALUES (1, '公務員', 0);
INSERT INTO mtb_job (id, name, rank) VALUES (2, 'コンサルタント', 1);
INSERT INTO mtb_job (id, name, rank) VALUES (3, 'コンピュータ関連技術職', 2);
INSERT INTO mtb_job (id, name, rank) VALUES (4, 'コンピュータ関連以外の技術職', 3);
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

INSERT INTO mtb_magazine_type (id, name, rank) VALUES (1, 'HTML', 0);
INSERT INTO mtb_magazine_type (id, name, rank) VALUES (2, 'テキスト', 1);
INSERT INTO mtb_magazine_type (id, name, rank) VALUES (3, 'HTMLテンプレート', 2);

INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (1, 'HTMLメール', 0);
INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (2, 'テキストメール', 1);
INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (3, '希望しない', 2);

INSERT INTO mtb_mail_template (id, name, rank) VALUES (1, '注文受付メール', 0);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (2, '注文受付メール(携帯)', 1);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (3, '注文キャンセル受付メール', 2);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (4, '取り寄せ確認メール', 3);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (5, 'お問い合わせ受付メール', 4);

INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (1, 'mail_templates/order_mail.tpl', 0);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (2, 'mobile/mail_templates/order_mail.tpl', 1);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (3, 'mail_templates/order_mail.tpl', 2);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (4, 'mail_templates/order_mail.tpl', 3);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (5, 'mail_templates/contact_mail.tpl', 4);

INSERT INTO mtb_mail_type (id, name, rank) VALUES (1, 'PCメールアドレス', 0);
INSERT INTO mtb_mail_type (id, name, rank) VALUES (2, '携帯メールアドレス', 1);
INSERT INTO mtb_mail_type (id, name, rank) VALUES (3, 'PCメールアドレス (携帯メールアドレスを登録している顧客は除外)', 2);
INSERT INTO mtb_mail_type (id, name, rank) VALUES (4, '携帯メールアドレス (PCメールアドレスを登録している顧客は除外)', 3);

INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (1, 'docomo.ne.jp', 0);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (2, 'ezweb.ne.jp', 1);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (3, 'softbank.ne.jp', 2);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (4, 'vodafone.ne.jp', 3);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (5, 'pdx.ne.jp', 4);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (6, 'disney.ne.jp', 5);
INSERT INTO mtb_mobile_domain (id, name, rank) VALUES (7, 'willcom.com', 6);

INSERT INTO mtb_order_status (id, name, rank) VALUES (7, '決済処理中', 0);
INSERT INTO mtb_order_status (id, name, rank) VALUES (1, '新規受付', 1);
INSERT INTO mtb_order_status (id, name, rank) VALUES (2, '入金待ち', 2);
INSERT INTO mtb_order_status (id, name, rank) VALUES (6, '入金済み', 3);
INSERT INTO mtb_order_status (id, name, rank) VALUES (3, 'キャンセル', 4);
INSERT INTO mtb_order_status (id, name, rank) VALUES (4, '取り寄せ中', 5);
INSERT INTO mtb_order_status (id, name, rank) VALUES (5, '発送済み', 6);


INSERT INTO mtb_order_status_color (id, name, rank) VALUES (1, '#FFFFFF', 0);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (2, '#FFDE9B', 1);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (3, '#C9C9C9', 2);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (4, '#FFD9D9', 3);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (5, '#BFDFFF', 4);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (6, '#FFFFAB', 5);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (7, '#FFCCCC', 6);

INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1000', '不明なエラーが発生しました。', 0);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1001', '不正なパラメータが送信されました。', 1);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1002', '認証に失敗しました。<br />・仮会員の方は、本会員登録を行ってください<br />・認証キーが正しく設定されているか確認してください', 2);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1003', '認証に失敗しました。<br />・仮会員の方は、本会員登録を行ってください<br />・認証キーが正しく設定されているか確認してください', 3);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1004', '購入済みの商品はありません。', 4);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1005', 'ダウンロード可能なアップデータはありません。<br />・ステータスが「入金待ち」の可能性があります<br />・インストールされているモジュールが既に最新版の可能性があります。', 5);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1006', '配信サーバでエラーが発生しました。', 6);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('1007', 'ダウンロード完了通知に失敗しました。', 7);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2001', '管理画面の認証に失敗しました。<br />管理画面トップページへ戻り、ログインし直してください。', 8);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2002', '配信サーバへ接続できません。', 9);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2003', '配信サーバへ接続できません。', 10);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2004', '配信サーバでエラーが発生しました。', 11);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2005', '認証キーが設定されていません。<br />・「認証キー設定」で認証キーを設定してください。', 12);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2006', '不正なアクセスです。', 13);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2007', '不正なパラメータが送信されました。', 14);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2008', '自動アップデートが無効です', 15);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2009', 'ファイルの書き込みに失敗しました。<br />・書き込み権限が正しく設定されていません。<br />・data/downloads/tmpディレクトリに書き込み権限があるかどうか確認してください', 16);
INSERT INTO mtb_ownersstore_err (id, name, rank) VALUES ('2010', 'ファイルの書き込みに失敗しました。<br />・「ログ管理」で詳細を確認してください。', 17);

INSERT INTO mtb_ownersstore_ips (id, name, rank) VALUES ('0', '210.188.195.143', 0);

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

INSERT INTO mtb_page_rows (id, name, rank) VALUES (10, '10', 0);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (20, '20', 1);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (30, '30', 2);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (40, '40', 3);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (50, '50', 4);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (60, '60', 5);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (70, '70', 6);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (80, '80', 7);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (90, '90', 8);
INSERT INTO mtb_page_rows (id, name, rank) VALUES (100, '100', 9);

INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/index.php', '0', 0);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/delete.php', '0', 1);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/input.php', '0', 2);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/master.php', '0', 3);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/master_delete.php', '0', 4);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/master_rank.php', '0', 5);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/mastercsv.php', '0', 6);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/system/rank.php', '0', 7);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/entry/index.php', '1', 8);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/entry/delete.php', '1', 9);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/entry/inputzip.php', '1', 10);
INSERT INTO mtb_permission (id, name, rank) VALUES ('/admin/search/delete_note.php', '1', 11);

INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (1, '北海道', 1);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (2, '青森県', 2);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (3, '岩手県', 3);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (4, '宮城県', 4);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (5, '秋田県', 5);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (6, '山形県', 6);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (7, '福島県', 7);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (8, '茨城県', 8);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (9, '栃木県', 9);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (10, '群馬県', 10);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (11, '埼玉県', 11);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (12, '千葉県', 12);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (13, '東京都', 13);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (14, '神奈川県', 14);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (15, '新潟県', 15);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (16, '富山県', 16);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (17, '石川県', 17);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (18, '福井県', 18);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (19, '山梨県', 19);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (20, '長野県', 20);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (21, '岐阜県', 21);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (22, '静岡県', 22);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (23, '愛知県', 23);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (24, '三重県', 24);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (25, '滋賀県', 25);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (26, '京都府', 26);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (27, '大阪府', 27);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (28, '兵庫県', 28);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (29, '奈良県', 29);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (30, '和歌山県', 30);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (31, '鳥取県', 31);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (32, '島根県', 32);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (33, '岡山県', 33);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (34, '広島県', 34);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (35, '山口県', 35);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (36, '徳島県', 36);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (37, '香川県', 37);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (38, '愛媛県', 38);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (39, '高知県', 39);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (40, '福岡県', 40);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (41, '佐賀県', 41);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (42, '長崎県', 42);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (43, '熊本県', 43);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (44, '大分県', 44);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (45, '宮崎県', 45);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (46, '鹿児島県', 46);
INSERT INTO mtb_pref (pref_id, pref_name, rank) VALUES (47, '沖縄県', 47);

INSERT INTO mtb_product_list_max (id, name, rank) VALUES (15, '15件', 0);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (30, '30件', 1);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (50, '50件', 2);

INSERT INTO mtb_product_status_color (id, name, rank) VALUES (1, '#FFFFFF', 0);
INSERT INTO mtb_product_status_color (id, name, rank) VALUES (2, '#C9C9C9', 1);
INSERT INTO mtb_product_status_color (id, name, rank) VALUES (3, '#DDE6F2', 2);

INSERT INTO mtb_recommend (id, name, rank) VALUES (5, '★★★★★', 0);
INSERT INTO mtb_recommend (id, name, rank) VALUES (4, '★★★★', 1);
INSERT INTO mtb_recommend (id, name, rank) VALUES (3, '★★★', 2);
INSERT INTO mtb_recommend (id, name, rank) VALUES (2, '★★', 3);
INSERT INTO mtb_recommend (id, name, rank) VALUES (1, '★', 4);

INSERT INTO mtb_reminder (id, name, rank) VALUES (1, '母親の旧姓は？', 0);
INSERT INTO mtb_reminder (id, name, rank) VALUES (2, 'お気に入りのマンガは？', 1);
INSERT INTO mtb_reminder (id, name, rank) VALUES (3, '大好きなペットの名前は？', 2);
INSERT INTO mtb_reminder (id, name, rank) VALUES (4, '初恋の人の名前は？', 3);
INSERT INTO mtb_reminder (id, name, rank) VALUES (5, '面白かった映画は？', 4);
INSERT INTO mtb_reminder (id, name, rank) VALUES (6, '尊敬していた先生の名前は？', 5);
INSERT INTO mtb_reminder (id, name, rank) VALUES (7, '好きな食べ物は？', 6);

INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (0, 'http://', 0);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (1, 'https://', 1);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (2, 'ttp://', 2);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (3, 'ttps://', 3);

INSERT INTO mtb_sex (id, name, rank) VALUES (1, '男性', 0);
INSERT INTO mtb_sex (id, name, rank) VALUES (2, '女性', 1);

INSERT INTO mtb_site_control_affiliate (id, name, rank) VALUES (1, '有効', 0);
INSERT INTO mtb_site_control_affiliate (id, name, rank) VALUES (2, '無効', 1);

INSERT INTO mtb_site_control_track_back (id, name, rank) VALUES (1, '有効', 0);
INSERT INTO mtb_site_control_track_back (id, name, rank) VALUES (2, '無効', 1);

INSERT INTO mtb_status (id, name, rank) VALUES (1, 'NEW', 0);
INSERT INTO mtb_status (id, name, rank) VALUES (2, '残りわずか', 1);
INSERT INTO mtb_status (id, name, rank) VALUES (3, 'ポイント２倍', 2);
INSERT INTO mtb_status (id, name, rank) VALUES (4, 'オススメ', 3);
INSERT INTO mtb_status (id, name, rank) VALUES (5, '限定品', 4);

INSERT INTO mtb_status_image (id, name, rank) VALUES (1, 'img/right_product/icon01.gif', 0);
INSERT INTO mtb_status_image (id, name, rank) VALUES (2, 'img/right_product/icon02.gif', 1);
INSERT INTO mtb_status_image (id, name, rank) VALUES (3, 'img/right_product/icon03.gif', 2);
INSERT INTO mtb_status_image (id, name, rank) VALUES (4, 'img/right_product/icon04.gif', 3);
INSERT INTO mtb_status_image (id, name, rank) VALUES (5, 'img/right_product/icon05.gif', 4);

INSERT INTO mtb_target (id, name, rank) VALUES (0, 'Unused', 0);
INSERT INTO mtb_target (id, name, rank) VALUES (1, 'LeftNavi', 1);
INSERT INTO mtb_target (id, name, rank) VALUES (2, 'MainHead', 2);
INSERT INTO mtb_target (id, name, rank) VALUES (3, 'RightNavi', 3);
INSERT INTO mtb_target (id, name, rank) VALUES (4, 'MainFoot', 4);
INSERT INTO mtb_target (id, name, rank) VALUES (5, 'TopNavi', 5);
INSERT INTO mtb_target (id, name, rank) VALUES (6, 'BottomNavi', 6);
INSERT INTO mtb_target (id, name, rank) VALUES (7, 'HeadNavi', 7);
INSERT INTO mtb_target (id, name, rank) VALUES (8, 'HeaderTopNavi', 8);
INSERT INTO mtb_target (id, name, rank) VALUES (9, 'FooterBottomNavi', 9);
INSERT INTO mtb_target (id, name, rank) VALUES (10, 'HeaderInternalNavi', 10);

INSERT INTO mtb_taxrule (id, name, rank) VALUES (1, '四捨五入', 0);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (2, '切り捨て', 1);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (3, '切り上げ', 2);

INSERT INTO mtb_track_back_status (id, name, rank) VALUES (1, '表示', 0);
INSERT INTO mtb_track_back_status (id, name, rank) VALUES (2, '非表示', 1);
INSERT INTO mtb_track_back_status (id, name, rank) VALUES (3, 'スパム', 2);

INSERT INTO mtb_wday (id, name, rank) VALUES (0, '日', 0);
INSERT INTO mtb_wday (id, name, rank) VALUES (1, '月', 1);
INSERT INTO mtb_wday (id, name, rank) VALUES (2, '火', 2);
INSERT INTO mtb_wday (id, name, rank) VALUES (3, '水', 3);
INSERT INTO mtb_wday (id, name, rank) VALUES (4, '木', 4);
INSERT INTO mtb_wday (id, name, rank) VALUES (5, '金', 5);
INSERT INTO mtb_wday (id, name, rank) VALUES (6, '土', 6);

INSERT INTO mtb_work (id, name, rank) VALUES (0, '非稼働', 0);
INSERT INTO mtb_work (id, name, rank) VALUES (1, '稼働', 1);

INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SAMPLE_ADDRESS1', '"市区町村名 (例：千代田区神田神保町)"', 1, 'フロント表示関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SAMPLE_ADDRESS2', '"番地・ビル名 (例：1-3-5)"', 2, 'フロント表示関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_DIR', '"user_data/"', 3, 'ユーザファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_PATH', 'HTML_PATH . USER_DIR', 4, 'ユーザファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_INC_PATH', 'USER_PATH . "include/"', 5, 'ユーザインクルードファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ZIP_DSN', 'DEFAULT_DSN', 8, '郵便番号専用DB');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_URL', 'SITE_URL . USER_DIR', 9, 'ユーザー作成ページ等');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AUTH_MAGIC', '"31eafcbd7a81d7b401a7fdc12bba047c02d1fae6"', 10, '認証用 magic');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_TEMPLATE_DIR', '"templates/"', 16, 'テンプレートファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_PACKAGE_DIR', '"packages/"', 17, 'テンプレートファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_TEMPLATE_PATH', 'USER_PATH . USER_PACKAGE_DIR', 18, 'テンプレートファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_TEMP_DIR', 'HTML_PATH . "upload/temp_template/"', 19, 'テンプレートファイル一時保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_DEF_PHP', 'HTML_PATH . "__default.php"', 20, 'ユーザー作成画面のデフォルトPHPファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEF_LAYOUT', '"products/list.php"', 21, 'その他画面のデフォルトページレイアウト');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_DIR', '"downloads/module/"', 22, 'ダウンロードモジュール保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_PATH', 'DATA_PATH . MODULE_DIR', 23, 'ダウンロードモジュール保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAX_LIFETIME', '7200', 26, 'DBセッションの有効期限(秒)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MASTER_DATA_DIR', 'DATA_PATH . "cache/"', 27, 'マスタデータキャッシュディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_HTTP', '"http://sv01.ec-cube.net/info/index.php"', 28, 'アップデート管理用ファイル格納場所');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_CSV_LINE_MAX', '4096', 29, 'アップデート管理用CSV1行辺りの最大文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_CSV_COL_MAX', '13', 30, 'アップデート管理用CSVカラム数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_CSV_COL_MAX', '16', 31, 'モジュール管理用CSVカラム数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AFF_SHOPPING_COMPLETE', '1', 34, '商品購入完了');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AFF_ENTRY_COMPLETE', '2', 35, 'ユーザ登録完了');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CHAR_CODE', '"UTF-8"', 39, '文字コード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOCALE', '"ja_JP.UTF-8"', 40, 'ロケール設定');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ECCUBE_PAYMENT', '"EC-CUBE"', 41, '決済モジュール付与文言');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PEAR_DB_DEBUG', '9', 42, 'PEAR::DBのデバッグモード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PEAR_DB_PERSISTENT', 'false', 43, 'PEAR::DBの持続的接続オプション');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOAD_BATCH_PASS', '3600', 44, 'バッチを実行する最短の間隔(秒)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CLOSE_DAY', '31', 45, '締め日の指定(末日の場合は、31を指定してください。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FAVORITE_ERROR', '13', 46, '一般サイトエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TTF_DIR', 'DATA_PATH . "fonts/"', 48, 'フォントのパス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_DIR', 'HTML_PATH . "upload/graph_image/"', 49, 'グラフ格納ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_URL', 'URL_DIR . "upload/graph_image/"', 50, 'グラフURL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_PIE_MAX', '10', 51, '円グラフ最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_LABEL_MAX', '40', 52, 'グラフのラベルの文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PDF_DIR', 'DATA_PATH . "pdf/"', 53, 'PDF格納ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BAT_ORDER_AGE', '70', 54, '何歳まで集計の対象とするか');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCTS_TOTAL_MAX', '15', 55, '商品集計で何位まで表示するか');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_PRODUCT_DISP', '2', 56, '1:公開 2:非公開');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_FREE_AMOUNT', '0', 57, '送料無料購入数量 (0の場合は、いくつ買っても無料にならない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('INPUT_DELIV_FEE', '1', 58, '配送料の設定画面表示(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_PRODUCT_DELIV_FEE', '0', 59, '商品ごとの送料設定(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_DELIV_FEE', '1', 60, '配送業者ごとの配送料を加算する(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_RECOMMEND', '1', 61, 'おすすめ商品登録(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_CLASS_REGIST', '1', 62, '商品規格登録(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_PASSWORD', '"UAhgGR3L"', 66, '会員登録変更(マイページ)パスワード用');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_ADDR_MAX', '20', 67, '別のお届け先最大登録数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_STATUS_MAX', '50', 70, '管理画面ステータス一覧表示件数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('REVIEW_REGIST_MAX', '5', 71, 'フロントレビュー書き込み最大数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEBUG_MODE', 'false', 72, 'デバッグモード(true：sfPrintRやDBのエラーメッセージを出力する、false：出力しない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_ID', '"1"', 73, '管理ユーザID(メンテナンス用表示されない。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_CONFIRM_MAIL', 'false', 74, '会員登録時に仮会員確認メールを送信するか (true:仮会員、false:本会員)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MELMAGA_SEND', 'true', 75, 'メルマガ配信(true:配信する、false:配信しない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MELMAGA_BATCH_MODE', 'false', 76, 'メイルマガジンバッチモード(true:バッチで送信する ※要cron設定、false:リアルタイムで送信する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOGIN_FRAME', '"login_frame.tpl"', 77, 'ログイン画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIN_FRAME', '"main_frame.tpl"', 78, '管理画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_FRAME', '"site_frame.tpl"', 79, '一般サイト画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CERT_STRING', '"7WDhcBTF"', 80, '認証文字列');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DUMMY_PASS', '"########"', 81, 'ダミーパスワード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BIRTH_YEAR', '1901', 83, '生年月日登録開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RELEASE_YEAR', '2005', 84, '本システムの稼働開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_ADD_YEAR', '10', 85, 'クレジットカードの期限＋何年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PARENT_CAT_MAX', '12', 86, '親カテゴリのカテゴリIDの最大数 (これ以下は親カテゴリとする。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NUMBER_MAX', '1000000000', 87, 'GET値変更などのいたずらを防ぐため最大数制限を設ける。');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('POINT_RULE', '2', 88, 'ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('POINT_VALUE', '1', 89, '1ポイント当たりの値段(円)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_MODE', '0', 90, '管理モード 1:有効　0:無効(納品時)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DAILY_BATCH_MODE', 'false', 91, '売上集計バッチモード(true:バッチで集計する ※要cron設定、false:リアルタイムで集計する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAX_LOG_QUANTITY', '5', 92, 'ログファイル最大数(ログテーション)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAX_LOG_SIZE', '"1000000"', 93, '1つのログファイルに保存する最大容量(byte)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRANSACTION_ID_NAME', '"transactionid"', 94, 'トランザクションID の名前');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FORGOT_MAIL', '0', 95, 'パスワード忘れの確認メールを送付するか否か。(0:送信しない、1:送信する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('HTML_TEMPLATE_SUB_MAX', '12', 96, '登録できるサブ商品の数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LINE_LIMIT_SIZE', '60', 97, '文字数が多すぎるときに強制改行するサイズ(半角)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BIRTH_MONTH_POINT', '0', 98, '誕生日月ポイント');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_1', '2', 101, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_2', '3', 102, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_3', '4', 103, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_4', '5', 104, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_5', '6', 105, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_6', '7', 106, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ROOT_CATEGORY_7', '8', 107, 'ルートカテゴリID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PAYMENT_CREDIT_ID', '1', 108, 'クレジットカード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PAYMENT_CONVENIENCE_ID', '2', 109, 'コンビニ決済');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LARGE_IMAGE_WIDTH', '500', 110, '拡大画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LARGE_IMAGE_HEIGHT', '500', 111, '拡大画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMALL_IMAGE_WIDTH', '130', 112, '一覧画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMALL_IMAGE_HEIGHT', '130', 113, '一覧画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_IMAGE_WIDTH', '260', 114, '通常画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_IMAGE_HEIGHT', '260', 115, '通常画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_SUBIMAGE_WIDTH', '200', 116, '通常サブ画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_SUBIMAGE_HEIGHT', '200', 117, '通常サブ画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LARGE_SUBIMAGE_WIDTH', '500', 118, '拡大サブ画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LARGE_SUBIMAGE_HEIGHT', '500', 119, '拡大サブ画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DISP_IMAGE_WIDTH', '65', 120, '一覧表示画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DISP_IMAGE_HEIGHT', '65', 121, '一覧表示画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OTHER_IMAGE1_WIDTH', '500', 122, 'その他の画像1');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OTHER_IMAGE1_HEIGHT', '500', 123, 'その他の画像1');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('HTMLMAIL_IMAGE_WIDTH', '110', 124, 'HTMLメールテンプレートメール担当画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('HTMLMAIL_IMAGE_HEIGHT', '120', 125, 'HTMLメールテンプレートメール担当画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SIZE', '1000', 126, '画像サイズ制限(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CSV_SIZE', '2000', 127, 'CSVサイズ制限(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CSV_LINE_MAX', '10000', 128, 'CSVアップロード1行あたりの最大文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PDF_SIZE', '5000', 129, 'PDFサイズ制限(KB):商品詳細ファイル等');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FILE_SIZE', '10000', 130, 'ファイル管理画面アップ制限(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_SIZE', '10000', 131, 'アップできるテンプレートファイル制限(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LEVEL_MAX', '5', 132, 'カテゴリの最大階層');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CATEGORY_MAX', '1000', 133, '最大カテゴリ登録数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_TITLE', '"管理機能"', 134, '管理機能タイトル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SELECT_RGB', '"#ffffdf"', 135, '編集時強調表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DISABLED_RGB', '"#C9C9C9"', 136, '入力項目無効時の表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ERR_COLOR', '"#ffe8e8"', 137, 'エラー時表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CATEGORY_HEAD', '">"', 138, '親カテゴリ表示文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('START_BIRTH_YEAR', '1901', 139, '生年月日選択開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_PRICE_TITLE', '"通常価格"', 140, '価格名称');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SALE_PRICE_TITLE', '"販売価格"', 141, '価格名称');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOG_PATH', 'DATA_PATH . "logs/site.log"', 142, 'ログファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_LOG_PATH', 'DATA_PATH . "logs/customer.log"', 143, '会員ログイン ログファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_DIR', 'HTML_PATH . "upload/temp_image/"', 150, '画像一時保存');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_DIR', 'HTML_PATH . "upload/save_image/"', 151, '画像保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_URL', 'URL_DIR . "upload/temp_image/"', 152, '画像一時保存URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_URL', 'URL_DIR . "upload/save_image/"', 153, '画像保存先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_URL_RSS', 'SITE_URL . "upload/temp_image/"', 154, 'RSS用画像一時保存URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_URL_RSS', 'SITE_URL . "upload/save_image/"', 155, 'RSS用画像保存先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CSV_TEMP_DIR', 'DATA_PATH . "upload/csv/"', 156, 'エンコードCSVの一時保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NO_IMAGE_URL', 'URL_DIR . "misc/blank.gif"', 157, '画像がない場合に表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NO_IMAGE_DIR', 'HTML_PATH . "misc/blank.gif"', 158, '画像がない場合に表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SYSTEM_TOP', 'URL_DIR . "admin/system/" . DIR_INDEX_URL', 159, 'システム管理トップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CLASS_REGIST', 'URL_DIR . "admin/products/class.php"', 160, '規格登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_INPUT_ZIP', 'URL_DIR . "input_zip.php"', 161, '郵便番号入力');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_DELIVERY_TOP', 'URL_DIR . "admin/basis/delivery.php"', 162, '配送業者登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_PAYMENT_TOP', 'URL_DIR . "admin/basis/payment.php"', 163, '支払い方法登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CONTROL_TOP', 'URL_DIR . "admin/basis/control.php"', 164, 'サイト管理情報登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_HOME', 'URL_DIR . "admin/home.php"', 165, 'ホーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LOGIN', 'URL_DIR . "admin/" . DIR_INDEX_URL', 166, 'ログインページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SEARCH_TOP', 'URL_DIR . "admin/products/" . DIR_INDEX_URL', 167, '商品検索ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ORDER_EDIT', 'URL_DIR . "admin/order/edit.php"', 168, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SEARCH_ORDER', 'URL_DIR . "admin/order/" . DIR_INDEX_URL', 169, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ORDER_MAIL', 'URL_DIR . "admin/order/mail.php"', 170, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LOGOUT', 'URL_DIR . "admin/logout.php"', 171, 'ログアウトページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SYSTEM_CSV', 'URL_DIR . "admin/system/member_csv.php"', 172, 'システム管理CSV出力ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ADMIN_CSS', 'URL_DIR . "admin/css/"', 173, '管理機能用CSS保管ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SUCCESS', '0', 176, 'アクセス成功');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MEMBER_PMAX', '10', 182, 'メンバー管理ページ表示行数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEARCH_PMAX', '10', 183, '検索ページ表示行数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NAVI_PMAX', '4', 184, 'ページ番号の最大表示数量');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCTSUB_MAX', '5', 185, '商品サブ情報最大数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIVTIME_MAX', '16', 186, 'お届け時間の最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIVFEE_MAX', '47', 187, '配送料金の最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('STEXT_LEN', '50', 188, '短い項目の文字数 (名前など)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMTEXT_LEN', '100', 189, NULL);
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MTEXT_LEN', '200', 190, '長い項目の文字数 (住所など)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MLTEXT_LEN', '1000', 191, '長中文の文字数 (問い合わせなど)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LTEXT_LEN', '3000', 192, '長文の文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LLTEXT_LEN', '99999', 193, '超長文の文字数 (メルマガなど)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LEN', '1024', 194, 'URLの文字長');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ID_MAX_LEN', '15', 195, '管理画面用：ID・パスワードの文字数制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ID_MIN_LEN', '4', 196, '管理画面用：ID・パスワードの文字数制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRICE_LEN', '8', 197, '金額桁数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PERCENTAGE_LEN', '3', 198, '率桁数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AMOUNT_LEN', '6', 199, '在庫数、販売制限数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ZIP01_LEN', '3', 200, '郵便番号1');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ZIP02_LEN', '4', 201, '郵便番号2');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEL_ITEM_LEN', '6', 202, '電話番号各項目制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEL_LEN', '12', 203, '電話番号総数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PASSWORD_LEN1', '4', 204, 'フロント画面用：パスワードの最小文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PASSWORD_LEN2', '10', 205, 'フロント画面用：パスワードの最大文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('INT_LEN', '9', 206, '検査数値用桁数(INT)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_NO_LEN', '4', 207, 'クレジットカードの文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEARCH_CATEGORY_LEN', '18', 208, '検索カテゴリ最大表示文字数(byte)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FILE_NAME_LEN', '10', 209, 'ファイル名表示文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SALE_LIMIT_MAX', '10', 210, '購入制限なしの場合の最大購入数量');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COOKIE_EXPIRE', '365', 212, 'クッキー保持期限(日)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEPA_CATNAVI', '" > "', 235, 'カテゴリ区切り文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEPA_CATLIST', '" | "', 236, 'カテゴリ区切り文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_TOP', 'SSL_URL . "shopping/" . DIR_INDEX_URL', 237, '会員情報入力');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ENTRY_TOP', 'SSL_URL . "entry/" . DIR_INDEX_URL', 238, '会員登録ページTOP');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SITE_TOP', 'URL_DIR . DIR_INDEX_URL', 239, 'サイトトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CART_TOP', 'URL_DIR . "cart/" . DIR_INDEX_URL', 240, 'カートトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_DELIV_TOP', 'URL_DIR . "shopping/deliv.php"', 241, 'お届け先設定');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_MYPAGE_TOP', 'SSL_URL . "mypage/login.php"', 242, 'Myページトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CONFIRM', 'URL_DIR . "shopping/confirm.php"', 243, '購入確認ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_PAYMENT', 'URL_DIR . "shopping/payment.php"', 244, 'お支払い方法選択ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_COMPLETE', 'URL_DIR . "shopping/complete.php"', 245, '購入完了画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CREDIT', 'URL_DIR . "shopping/card.php"', 246, 'カード決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_LOAN', 'URL_DIR . "shopping/loan.php"', 247, 'ローン決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CONVENIENCE', 'URL_DIR . "shopping/convenience.php"', 248, 'コンビニ決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_MODULE', 'URL_DIR . "shopping/load_payment_module.php"', 249, 'モジュール追加用画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DETAIL_P_HTML', 'URL_DIR . "products/detail.php?product_id="', 253, '商品詳細(HTML出力)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MYPAGE_DELIVADDR_URL', 'URL_DIR . "mypage/delivery.php"', 254, 'マイページお届け先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIL_TYPE_PC', '1', 255, 'メールアドレス種別');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIL_TYPE_MOBILE', '2', 256, 'メールアドレス種別');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ODERSTATUS_COMMIT', 'ORDER_DELIV', 263, '受注ステータス変更の際にポイント等を加算するステータス番号 (発送済み)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_NEWS_STARTYEAR', '2005', 264, '新着情報管理画面 開始年(西暦) ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_CUSTOMER_TEMP_SUBJECT', '"会員仮登録が完了いたしました。"', 265, '会員登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_CUSTOMER_REGIST_SUBJECT', '"本会員登録が完了いたしました。"', 266, '会員登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_LIMIT_HOUR', '1', 267, '再入会制限時間 (単位: 時間)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RECOMMEND_PRODUCT_MAX', '6', 268, '関連商品表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RECOMMEND_NUM', '8', 269, 'おすすめ商品表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_DATE_END_MAX', '21', 272, 'お届け可能日以降のプルダウン表示最大日数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PURCHASE_CUSTOMER_REGIST', '0', 273, '購入時強制会員登録(1:有効　0:無効)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CV_PAYMENT_LIMIT', '14', 275, '支払期限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('REVIEW_ALLOW_URL', '0', 277, '商品レビューでURL書き込みを許可するか否か');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_VIEW', '1', 278, 'トラックバック 表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_NOT_VIEW', '2', 279, 'トラックバック 非表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_SPAM', '3', 280, 'トラックバック スパム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_VIEW_MAX', '10', 281, 'フロント最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_TO_URL', 'SITE_URL . "tb/" . DIR_INDEX_URL . "?pid="', 282, 'トラックバック先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_CONTROL_TRACKBACK', '1', 283, 'サイト管理 トラックバック');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_CONTROL_AFFILIATE', '2', 284, 'サイト管理 アフィリエイト');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIL_BACKEND', '"smtp"', 285, 'Pear::Mail バックエンド:mail|smtp|sendmail');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMTP_HOST', '"127.0.0.1"', 287, 'SMTPサーバー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMTP_PORT', '"25"', 288, 'SMTPポート');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_SEND_SITE_INFO', 'false', 289, 'アップデート時にサイト情報を送出するか');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USE_POINT', 'true', 290, 'ポイントを利用するか(true:利用する、false:利用しない) (false は一部対応)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NOSTOCK_HIDDEN', 'false', 291, '在庫無し商品の非表示(true:非表示、false:表示)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USE_MOBILE', 'true', 292, 'モバイルサイトを利用するか(true:利用する、false:利用しない) (false は一部対応)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_TEMPLATE_NAME', '"default"', 300, 'デフォルトテンプレート名');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_NAME', '"default"', 301, 'テンプレート名');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_TEMPLATE_NAME', '"mobile"', 302, 'モバイルテンプレート名');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMARTPHONE_TEMPLATE_NAME', '"sphone"', 303, 'スマートフォンテンプレート名');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMARTY_TEMPLATES_DIR', ' DATA_PATH . "Smarty/templates/"', 304, 'SMARTYテンプレート');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_DIR', 'SMARTY_TEMPLATES_DIR . TEMPLATE_NAME . "/"', 305, 'SMARTYテンプレート(PC)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_ADMIN_DIR', 'SMARTY_TEMPLATES_DIR . "admin/"', 306, 'SMARTYテンプレート(管理機能)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_DIR', 'DATA_PATH . "Smarty/templates_c/" . TEMPLATE_NAME . "/"', 307, 'SMARTYコンパイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_ADMIN_DIR', 'DATA_PATH . "Smarty/templates_c/admin/"', 308, 'SMARTYコンパイル(管理機能)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_FTP_DIR', 'USER_PATH . USER_PACKAGE_DIR . TEMPLATE_NAME . "/"', 309, 'SMARTYテンプレート(FTP許可)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_FTP_DIR', 'COMPILE_DIR . USER_DIR', 310, 'SMARTYコンパイル(FTP許可)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BLOC_DIR', '"bloc/"', 311, 'ブロックファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_TEMPLATE_DIR', 'SMARTY_TEMPLATES_DIR . MOBILE_TEMPLATE_NAME . "/"', 312, 'SMARTYテンプレート(mobile)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_COMPILE_DIR', 'DATA_PATH . "Smarty/templates_c/" . MOBILE_TEMPLATE_NAME . "/"', 313, 'SMARTYコンパイル(mobile)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMARTPHONE_TEMPLATE_DIR', 'SMARTY_TEMPLATES_DIR . SMARTPHONE_TEMPLATE_NAME . "/"', 314, 'SMARTYテンプレート(smart phone)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMARTPHONE_COMPILE_DIR', 'DATA_PATH . "Smarty/templates_c/" . SMARTPHONE_TEMPLATE_NAME . "/"', 315, 'SMARTYコンパイル(smartphone)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BLOC_PATH', 'TEMPLATE_DIR . BLOC_DIR', 316, 'ブロックファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RFC_COMPLIANT_EMAIL_CHECK', 'false', 401, 'EメールアドレスチェックをRFC準拠にするか(true:準拠する、false:準拠しない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_SESSION_LIFETIME', '1800', 402, 'モバイルサイトのセッションの存続時間 (秒)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_USE_KARA_MAIL', 'false', 403, '空メール機能を使用するかどうか(true:送信する、false:送信しない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_KARA_MAIL_ADDRESS_USER', '"eccube"', 404, '空メール受け付けアドレスのユーザー名部分');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_KARA_MAIL_ADDRESS_DELIMITER', '"+"', 405, '空メール受け付けアドレスのユーザー名とコマンドの間の区切り文字 qmail の場合は -');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_KARA_MAIL_ADDRESS_DOMAIN', '""', 406, '空メール受け付けアドレスのドメイン部分');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_ADDITIONAL_MAIL_DOMAINS', '""', 407, '携帯のメールアドレスではないが、携帯だとみなすドメインのリスト 任意の数の「,」「 」で区切る。');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_IMAGE_DIR', 'HTML_PATH . "upload/mobile_image"', 408, '携帯電話向け変換画像保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_IMAGE_URL', 'URL_DIR . "upload/mobile_image"', 409, '携帯電話向け変換画像保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SITE_TOP', 'MOBILE_URL_DIR . DIR_INDEX_URL', 410, 'モバイルURL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_CART_TOP', 'MOBILE_URL_DIR . "cart/" . DIR_INDEX_URL', 411, 'カートトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SHOP_TOP', 'MOBILE_SSL_URL . "shopping/" . DIR_INDEX_URL', 412, '会員情報入力');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SHOP_CONFIRM', 'MOBILE_URL_DIR . "shopping/confirm.php"', 413, '購入確認ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SHOP_PAYMENT', 'MOBILE_URL_DIR . "shopping/payment.php"', 414, 'お支払い方法選択ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_DETAIL_P_HTML', 'MOBILE_URL_DIR . "products/detail.php?product_id="', 415, '商品詳細(HTML出力)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SHOP_COMPLETE', 'MOBILE_URL_DIR . "shopping/complete.php"', 416, '購入完了画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MOBILE_URL_SHOP_MODULE', 'MOBILE_URL_DIR . "shopping/load_payment_module.php"', 417, 'モジュール追加用画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SESSION_KEEP_METHOD', '"useCookie"', 418, 'セッション維持方法：useCookie|useRequest');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SESSION_LIFETIME', '1800', 419, 'セッションの存続時間 (秒)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_URL', '"http://store.ec-cube.net/"', 500, 'オーナーズストアURL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_SSLURL', '"https://store.ec-cube.net/"', 501, 'オーナーズストアURL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_LOG_PATH', 'DATA_PATH . "logs/ownersstore.log"', 502, 'オーナーズストアログパス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_STATUS_ERROR', '"ERROR"', 503, 'オーナーズストア通信ステータス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_STATUS_SUCCESS', '"SUCCESS"', 504, 'オーナーズストア通信ステータス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_UNKNOWN', '"1000"', 505, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_INVALID_PARAM', '"1001"', 506, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_NO_CUSTOMER', '"1002"', 507, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_WRONG_URL_PASS', '"1003"', 508, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_NO_PRODUCTS', '"1004"', 509, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_NO_DL_DATA', '"1005"', 510, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_DL_DATA_OPEN', '"1006"', 511, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_DLLOG_AUTH', '"1007"', 512, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_ADMIN_AUTH', '"2001"', 513, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_HTTP_REQ', '"2002"', 514, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_HTTP_RESP', '"2003"', 515, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_FAILED_JSON_PARSE', '"2004"', 516, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_NO_KEY', '"2005"', 517, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_INVALID_ACCESS', '"2006"', 518, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_INVALID_PARAM', '"2007"', 519, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_AUTOUP_DISABLE', '"2008"', 520, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_PERMISSION', '"2009"', 521, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OSTORE_E_C_BATCH_ERR', '"2010"', 522, 'オーナーズストア通信エラーコード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_FAVOFITE_PRODUCT', '1', 523, 'お気に入り商品登録(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_RENAME', 'true', 525, '画像リネーム設定 (商品画像のみ) (true:リネームする、false:リネームしない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PLUGIN_DIR', '"plugins/"', 600, 'プラグインディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PLUGIN_PATH', 'USER_PATH . PLUGIN_DIR', 601, 'プラグイン保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PLUGIN_URL', 'USER_URL . PLUGIN_DIR', 602, 'プラグイン URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWNLOAD_DAYS_LEN', '3', 700, '日数桁数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWNLOAD_EXTENSION', '"zip,lzh,jpg,jpeg,gif,png,mp3,pdf,csv"', 701, 'ダウンロードファイル登録可能拡張子(カンマ区切り)"');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWN_SIZE', '50000', 702, 'ダウンロード販売ファイル用サイズ制限(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_PRODUCT_DOWN', '1', 703, '1:実商品 2:ダウンロード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWN_TEMP_DIR', 'DATA_PATH . "download/temp/"', 704, 'ダウンロードファイル一時保存');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWN_SAVE_DIR', 'DATA_PATH . "download/save/"', 705, 'ダウンロードファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWNFILE_NOT_FOUND', '22', 706, 'ダウンロードファイル存在エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ONLINE_PAYMENT', '"1"', 707, 'ダウンロード販売機能用オンライン決済payment_id(カンマ区切り)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DOWNLOAD_BLOCK', '1024', 708, 'ダウンロード販売機能 ダウンロードファイル読み込みバイト(KB)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_NEW', '1', 800, '新規注文');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_PAY_WAIT', '2', 801, '入金待ち');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_PRE_END', '6', 802, '入金済み');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_CANCEL', '3', 803, 'キャンセル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_BACK_ORDER', '4', 804, '取り寄せ中');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_DELIV', '5', 805, '発送済み');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_PENDING', '7', 806, '決済処理中');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCT_TYPE_NORMAL', '1', 900, '通常商品');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCT_TYPE_DOWNLOAD', '2', 901, 'ダウンロード商品');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SQL_QUERY_LOG_MODE', '1', 902, 'SQLログを取得するフラグ(1:表示, 0:非表示)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SQL_QUERY_LOG_MIN_EXEC_TIME', '2', 903, 'SQLログを取得する時間設定(設定値以上かかった場合に取得)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PAGE_DISPLAY_TIME_LOG_MODE', '1', 904, 'ページ表示時間のログを取得するフラグ(1:表示, 0:非表示)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME', '2', 905, 'ページ表示時間のログを取得する時間設定(設定値以上かかった場合に取得)');

INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_customer', 'email_mobile', 0, '会員数増加時のログイン処理速度を向上させたいときに試してみてください');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_products', 'name', 2, '商品名検索速度を向上させたいときに試してみてください');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order_temp', 'order_temp_id', 0, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'status', 2, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_email', 2, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_name01', 2, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_name02', 0, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_tel01', 0, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_tel02', 0, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order', 'order_tel03', 0, '注文数が多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_customer', 'mobile_phone_id', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_products_class', 'product_id', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_order_detail', 'product_id', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_send_customer', 'customer_id', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_ext_session_id', 'param_key', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_ext_session_id', 'param_value', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_ext_session_id', 'url', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_ext_session_id', 'create_date', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_kara_mail', 'token', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_kara_mail', 'create_date', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_mobile_kara_mail', 'receive_date', 1, '');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('dtb_product_categories', 'category_id', 2, 'カテゴリが多いときに試してみてください。');
INSERT INTO dtb_index_list (table_name, column_name, recommend_flg, recommend_comment) VALUES ('mtb_zip', 'zipcode', 2, '郵便番号検索が遅いときに試してみてください。郵便番号データの更新時には無効にしていることをおすすめします。');
--
-- FIXME 要修正
--

INSERT INTO dtb_baseinfo (company_name, company_kana, zip01, zip02, pref, addr01, addr02, tel01, tel02, tel03, fax01, fax02, fax03, business_hour, law_company, law_manager, law_zip01, law_zip02, law_pref, law_addr01, law_addr02, law_tel01, law_tel02, law_tel03, law_fax01, law_fax02, law_fax03, law_email, law_url, law_term01, law_term02, law_term03, law_term04, law_term05, law_term06, law_term07, law_term08, law_term09, law_term10, tax, tax_rule, email01, email02, email03, email04, email05, free_rule, shop_name, shop_kana, point_rate, welcome_point, update_date, top_tpl, product_tpl, detail_tpl, mypage_tpl, good_traded, message) VALUES (NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 1, 'spa@nanasess.net', 'spa@nanasess.net', 'spa@nanasess.net', 'spa@nanasess.net', 'spa@nanasess.net', NULL, 'EC-CUBE1.5', NULL, NULL, NULL, NULL, 'default1', 'default1', 'default1', 'default1', NULL, NULL);

INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (1, 'カテゴリ', 'include/bloc/category.tpl', 'category', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/category.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (2, '利用ガイド', 'include/bloc/guide.tpl', 'guide', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (3, 'かごの中', 'include/bloc/cart.tpl', 'cart', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/cart.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (4, '商品検索', 'include/bloc/search_products.tpl', 'search_products', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/search_products.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (5, '新着情報', 'include/bloc/news.tpl', 'news', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/news.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (6, 'ログイン', 'include/bloc/login.tpl', 'login', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/login.php', 1);
INSERT INTO dtb_bloc (bloc_id, bloc_name, tpl_path, filename, create_date, update_date, php_path, del_flg) VALUES (7, 'オススメ商品', 'include/bloc/best5.tpl', 'best5', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'frontparts/bloc/best5.php', 1);


--
-- Data for Name: dtb_blocposition; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 1, 1, 2, 'category');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 1, 2, 3, 'guide');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 1, 3, 1, 'cart');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 3, 4, 2, 'search_products');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 4, 5, 1, 'news');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 3, 6, 1, 'login');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (1, 4, 7, 2, 'best5');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 1, 1, 2, 'category');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 1, 2, 3, 'guide');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 1, 3, 1, 'cart');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 5, 4, 0, 'search_products');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 5, 5, 0, 'news');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 5, 6, 0, 'login');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (2, 5, 7, 0, 'best5');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 1, 1, 2, 'category');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 1, 2, 3, 'guide');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 1, 3, 1, 'cart');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 5, 4, 0, 'search_products');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 5, 5, 0, 'news');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 5, 6, 0, 'login');
INSERT INTO dtb_blocposition (page_id, target_id, bloc_id, bloc_row, filename) VALUES (3, 5, 7, 0, 'best5');


--
-- Data for Name: dtb_campaign; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_campaign_detail; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_campaign_order; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_category; Type: TABLE DATA; Schema: public; Owner: nanasess
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--
--

INSERT INTO dtb_category (category_id, category_name, parent_category_id, "category_level", rank, creator_id, create_date, update_date, del_flg) VALUES (1, '食品', 0, 1, 4, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, "category_level", rank, creator_id, create_date, update_date, del_flg) VALUES (2, '雑貨', 0, 1, 5, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, "category_level", rank, creator_id, create_date, update_date, del_flg) VALUES (3, 'お菓子', 1, 2, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, "category_level", rank, creator_id, create_date, update_date, del_flg) VALUES (4, 'なべ', 1, 2, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_category (category_id, category_name, parent_category_id, "category_level", rank, creator_id, create_date, update_date, del_flg) VALUES (5, 'アイス', 3, 3, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);


--
-- Data for Name: dtb_category_count; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_category_total_count; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (3, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (1, 2, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (2, NULL, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (5, 1, CURRENT_TIMESTAMP);
INSERT INTO dtb_category_total_count (category_id, product_count, create_date) VALUES (4, 1, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_class; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_class (class_id, name, status, rank, creator_id, create_date, update_date, del_flg, product_id) VALUES (1, '味', NULL, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, NULL);
INSERT INTO dtb_class (class_id, name, status, rank, creator_id, create_date, update_date, del_flg, product_id) VALUES (2, '大きさ', NULL, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, NULL);


--
-- Data for Name: dtb_classcategory; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (1, 'バニラ', 1, NULL, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (2, 'チョコ', 1, NULL, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '抹茶', 1, NULL, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (4, 'L', 2, NULL, 1, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (5, 'M', 2, NULL, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (6, 'S', 2, NULL, 3, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_classcategory (classcategory_id, name, class_id, status, rank, creator_id, create_date, update_date, del_flg) VALUES (0, NULL, 0, NULL, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);


--
-- Data for Name: dtb_csv; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (1, 1, 'product_id', '商品ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (2, 1, 'product_class_id', '規格ID', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (3, 1, 'classcategory_id1', '規格名1', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (4, 1, 'classcategory_id2', '規格名2', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (5, 1, 'name', '商品名', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (6, 1, 'status', '公開フラグ', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (7, 1, 'product_flag', '商品ステータス', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (8, 1, 'product_code', '商品コード', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (9, 1, 'price01', '通常価格', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (10, 1, 'price02', '販売価格', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (11, 1, 'stock', '在庫数', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (12, 1, 'deliv_fee', '送料', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (13, 1, 'point_rate', 'ポイント付与率', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (14, 1, 'sale_limit', '購入制限', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (15, 1, 'comment1', 'メーカーURL', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (16, 1, 'comment3', '検索ワード', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (17, 1, 'main_list_comment', '一覧-メインコメント', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (18, 1, 'main_list_image', '一覧-メイン画像', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (19, 1, 'main_comment', '詳細-メインコメント', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (20, 1, 'main_image', '詳細-メイン画像', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (21, 1, 'main_large_image', '詳細-メイン拡大画像 ', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (22, 1, 'file1', 'カラー比較画像', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (23, 1, 'file2', '商品詳細ファイル    ', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (24, 1, 'sub_title1', '詳細-サブタイトル（1）', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (25, 1, 'sub_comment1', '詳細-サブコメント（1）', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (26, 1, 'sub_image1', '詳細-サブ画像（1）', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (27, 1, 'sub_large_image1', '詳細-サブ拡大画像（1）', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (28, 1, 'sub_title2', '詳細-サブタイトル（2）', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (29, 1, 'sub_comment2', '詳細-サブコメント（2）', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (30, 1, 'sub_image2', '詳細-サブ画像（2）', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (31, 1, 'sub_large_image2', '詳細-サブ拡大画像（2）', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (32, 1, 'sub_title3', '詳細-サブタイトル（3）', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (33, 1, 'sub_comment3', '詳細-サブコメント（3）', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (34, 1, 'sub_image3', '詳細-サブ画像（3）', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (35, 1, 'sub_large_image3', '詳細-サブ拡大画像（3）', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (36, 1, 'sub_title4', '詳細-サブタイトル（4）', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (37, 1, 'sub_comment4', '詳細-サブコメント（4）', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (38, 1, 'sub_image4', '詳細-サブ画像（4）', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (39, 1, 'sub_large_image4', '詳細-サブ拡大画像（4）', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (40, 1, 'sub_title5', '詳細-サブタイトル（5）', 40, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (41, 1, 'sub_comment5', '詳細-サブコメント（5）', 41, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (42, 1, 'sub_image5', '詳細-サブ画像（5）', 42, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (43, 1, 'sub_large_image5', '詳細-サブ拡大画像（5）', 43, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (44, 1, 'deliv_date_id', '発送日目安', 44, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (45, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_product_id1', 'おすすめ商品(1)', 45, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (46, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_comment1', 'おすすめコメント(1)', 46, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (47, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_product_id2', 'おすすめ商品(2)', 47, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (48, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_comment2', 'おすすめコメント(2)', 48, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (49, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_product_id3', 'おすすめ商品(3)', 49, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (50, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_comment3', 'おすすめコメント(3)', 50, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (51, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_product_id4', 'おすすめ商品(4)', 51, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (52, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_comment4', 'おすすめコメント(4)', 52, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (53, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 4) AS recommend_product_id5', 'おすすめ商品(5)', 53, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (54, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 4) AS recommend_comment5', 'おすすめコメント(5)', 54, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (55, 1, '(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 5) AS recommend_product_id6', 'おすすめ商品(6)', 55, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (56, 1, '(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 5) AS recommend_comment6', 'おすすめコメント(6)', 56, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (57, 1, 'category_id', 'カテゴリID', 57, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (58, 2, 'customer_id', '顧客ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (59, 2, 'name01', '名前1', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (60, 2, 'name02', '名前2', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (61, 2, 'kana01', 'フリガナ1', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (62, 2, 'kana02', 'フリガナ2', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (63, 2, 'zip01', '郵便番号1', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (64, 2, 'zip02', '郵便番号2', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (65, 2, 'pref', '都道府県', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (66, 2, 'addr01', '住所1', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (67, 2, 'addr02', '住所2', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (68, 2, 'email', 'E-MAIL', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (69, 2, 'tel01', 'TEL1', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (70, 2, 'tel02', 'TEL2', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (71, 2, 'tel03', 'TEL3', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (72, 2, 'fax01', 'FAX1', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (73, 2, 'fax02', 'FAX2', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (74, 2, 'fax03', 'FAX3', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (75, 2, 'sex', '性別', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (76, 2, 'job', '職業', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (77, 2, 'birth', '誕生日', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (78, 2, 'first_buy_date', '初回購入日', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (79, 2, 'last_buy_date', '最終購入日', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (80, 2, 'buy_times', '購入回数', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (81, 2, 'point', 'ポイント残高', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (82, 2, 'note', '備考', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (83, 2, 'create_date', '登録日', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (84, 2, 'update_date', '更新日', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (85, 3, 'order_id', '注文ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (86, 3, 'customer_id', '顧客ID', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (87, 3, 'message', '要望等', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (88, 3, 'order_name01', '顧客名1', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (89, 3, 'order_name02', '顧客名2', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (90, 3, 'order_kana01', '顧客名カナ1', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (91, 3, 'order_kana02', '顧客名カナ2', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (92, 3, 'order_email', 'メールアドレス', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (93, 3, 'order_tel01', '電話番号1', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (94, 3, 'order_tel02', '電話番号2', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (95, 3, 'order_tel03', '電話番号3', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (96, 3, 'order_fax01', 'FAX1', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (97, 3, 'order_fax02', 'FAX2', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (98, 3, 'order_fax03', 'FAX3', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (99, 3, 'order_zip01', '郵便番号1', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (100, 3, 'order_zip02', '郵便番号2', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (101, 3, 'order_pref', '都道府県', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (102, 3, 'order_addr01', '住所1', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (103, 3, 'order_addr02', '住所2', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (104, 3, 'order_sex', '性別', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (105, 3, 'order_birth', '生年月日', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (106, 3, 'order_job', '職種', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (107, 3, 'deliv_name01', '配送先名前', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (108, 3, 'deliv_name02', '配送先名前', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (109, 3, 'deliv_kana01', '配送先カナ', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (110, 3, 'deliv_kana02', '配送先カナ', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (111, 3, 'deliv_tel01', '電話番号1', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (112, 3, 'deliv_tel02', '電話番号2', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (113, 3, 'deliv_tel03', '電話番号3', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (114, 3, 'deliv_fax01', 'FAX1', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (115, 3, 'deliv_fax02', 'FAX2', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (116, 3, 'deliv_fax03', 'FAX3', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (117, 3, 'deliv_zip01', '郵便番号1', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (118, 3, 'deliv_zip02', '郵便番号2', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (119, 3, 'deliv_pref', '都道府県', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (120, 3, 'deliv_addr01', '住所1', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (121, 3, 'deliv_addr02', '住所2', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (122, 3, 'subtotal', '小計', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (123, 3, 'discount', '値引き', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (124, 3, 'deliv_fee', '送料', 40, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (125, 3, 'charge', '手数料', 41, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (126, 3, 'use_point', '使用ポイント', 42, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (127, 3, 'add_point', '加算ポイント', 43, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (128, 3, 'tax', '税金', 44, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (129, 3, 'total', '合計', 45, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (130, 3, 'payment_total', 'お支払い合計', 46, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (131, 3, 'payment_method', '支払い方法', 47, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (132, 3, 'deliv_time', '配送時間', 48, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (133, 3, 'deliv_no', '配送伝票番号', 49, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (134, 3, 'note', 'SHOPメモ', 50, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (135, 3, 'status', '対応状況', 51, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (136, 3, 'create_date', '注文日時', 52, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (137, 3, 'update_date', '更新日時', 53, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (138, 4, 'order_id', '注文ID', 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (139, 4, 'campaign_id', 'キャンペーンID', 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (140, 4, 'customer_id', '顧客ID', 3, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (141, 4, 'message', '要望等', 4, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (142, 4, 'order_name01', '顧客名1', 5, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (143, 4, 'order_name02', '顧客名2', 6, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (144, 4, 'order_kana01', '顧客名カナ1', 7, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (145, 4, 'order_kana02', '顧客名カナ2', 8, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (146, 4, 'order_email', 'メールアドレス', 9, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (147, 4, 'order_tel01', '電話番号1', 10, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (148, 4, 'order_tel02', '電話番号2', 11, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (149, 4, 'order_tel03', '電話番号3', 12, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (150, 4, 'order_fax01', 'FAX1', 13, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (151, 4, 'order_fax02', 'FAX2', 14, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (152, 4, 'order_fax03', 'FAX3', 15, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (153, 4, 'order_zip01', '郵便番号1', 16, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (154, 4, 'order_zip02', '郵便番号2', 17, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (155, 4, 'order_pref', '都道府県', 18, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (156, 4, 'order_addr01', '住所1', 19, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (157, 4, 'order_addr02', '住所2', 20, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (158, 4, 'order_sex', '性別', 21, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (159, 4, 'order_birth', '生年月日', 22, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (160, 4, 'order_job', '職種', 23, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (161, 4, 'deliv_name01', '配送先名前', 24, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (162, 4, 'deliv_name02', '配送先名前', 25, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (163, 4, 'deliv_kana01', '配送先カナ', 26, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (164, 4, 'deliv_kana02', '配送先カナ', 27, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (165, 4, 'deliv_tel01', '電話番号1', 28, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (166, 4, 'deliv_tel02', '電話番号2', 29, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (167, 4, 'deliv_tel03', '電話番号3', 30, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (168, 4, 'deliv_fax01', 'FAX1', 31, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (169, 4, 'deliv_fax02', 'FAX2', 32, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (170, 4, 'deliv_fax03', 'FAX3', 33, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (171, 4, 'deliv_zip01', '郵便番号1', 34, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (172, 4, 'deliv_zip02', '郵便番号2', 35, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (173, 4, 'deliv_pref', '都道府県', 36, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (174, 4, 'deliv_addr01', '住所1', 37, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (175, 4, 'deliv_addr02', '住所2', 38, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_csv ("no", csv_id, col, disp_name, rank, status, create_date, update_date) VALUES (176, 4, 'payment_total', 'お支払い合計', 39, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_csv_sql; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_customer; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_customer (customer_id, name01, name02, kana01, kana02, zip01, zip02, pref, addr01, addr02, email, email_mobile, tel01, tel02, tel03, fax01, fax02, fax03, sex, job, birth, "password", reminder, reminder_answer, secret_key, first_buy_date, last_buy_date, buy_times, buy_total, point, note, status, create_date, update_date, del_flg, cell01, cell02, cell03, mobile_phone_id, mailmaga_flg) VALUES (13, '大河内', '健太郎', 'オオコウチ', 'ケンタロウ', '444', '0026', 1, '八戸市', '2', 'spa@nanasess.net', NULL, '000', '000', '000', NULL, NULL, NULL, 1, NULL, NULL, '28be58e756294fc0b1ea693e1fabea92223f12f2', 1, 'ムサシ', 'r46a9a8071b3d07JD3prFJ', NULL, NULL, 0, 0, 0, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, NULL, NULL, NULL, NULL, 1);


--
-- Data for Name: dtb_customer_mail_temp; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_customer_reading; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_deliv; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_deliv (deliv_id, name, service_name, confirm_url, rank, status, del_flg, creator_id, create_date, update_date) VALUES (1, 'サンプル業者', 'サンプル業者', '', 1, 1, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_delivfee; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 1, '1000', 1);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 2, '1000', 2);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 3, '1000', 3);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 4, '1000', 4);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 5, '1000', 5);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 6, '1000', 6);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 7, '1000', 7);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 8, '1000', 8);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 9, '1000', 9);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 10, '1000', 10);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 11, '1000', 11);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 12, '1000', 12);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 13, '1000', 13);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 14, '1000', 14);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 15, '1000', 15);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 16, '1000', 16);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 17, '1000', 17);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 18, '1000', 18);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 19, '1000', 19);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 20, '1000', 20);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 21, '1000', 21);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 22, '1000', 22);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 23, '1000', 23);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 24, '1000', 24);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 25, '1000', 25);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 26, '1000', 26);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 27, '1000', 27);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 28, '1000', 28);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 29, '1000', 29);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 30, '1000', 30);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 31, '1000', 31);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 32, '1000', 32);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 33, '1000', 33);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 34, '1000', 34);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 35, '1000', 35);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 36, '1000', 36);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 37, '1000', 37);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 38, '1000', 38);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 39, '1000', 39);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 40, '1000', 40);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 41, '1000', 41);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 42, '1000', 42);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 43, '1000', 43);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 44, '1000', 44);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 45, '1000', 45);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 46, '1000', 46);
INSERT INTO dtb_delivfee (deliv_id, fee_id, fee, pref) VALUES (1, 47, '1000', 47);


--
-- Data for Name: dtb_delivtime; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_delivtime (deliv_id, time_id, deliv_time) VALUES (1, 1, '午前');
INSERT INTO dtb_delivtime (deliv_id, time_id, deliv_time) VALUES (1, 2, '午後');


--
-- Data for Name: dtb_kiyaku; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (1, '第1条（会員）', '1. 「会員」とは、当社が定める手続に従い本規約に同意の上、入会の申し込みを行う個人をいいます。 2. 「会員情報」とは、会員が当社に開示した会員の属性に関する情報および会員の取引に関する履歴等の情報をいいます。 3. 本規約は、すべての会員に適用され、登録手続時および登録後にお守りいただく規約です。', 12, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (2, '第2条（登録）', '1. 会員資格 本規約に同意の上、所定の入会申込みをされたお客様は、所定の登録手続完了後に会員としての資格を有します。会員登録手続は、会員となるご本人が行ってください。代理による登録は一切認められません。なお、過去に会員資格が取り消された方やその他当社が相応しくないと判断した方からの会員申込はお断りする場合があります。  2. 会員情報の入力 会員登録手続の際には、入力上の注意をよく読み、所定の入力フォームに必要事項を正確に入力してください。会員情報の登録において、特殊記号・旧漢字・ローマ数字などはご使用になれません。これらの文字が登録された場合は当社にて変更致します。  3. パスワードの管理 (1)パスワードは会員本人のみが利用できるものとし、第三者に譲渡・貸与できないものとします。 (2)パスワードは、他人に知られることがないよう定期的に変更する等、会員本人が責任をもって管理してください。 (3)パスワードを用いて当社に対して行われた意思表示は、会員本人の意思表示とみなし、そのために生じる支払等はすべて会員の責任となります。', 11, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (3, '第3条（変更）', '1. 会員は、氏名、住所など当社に届け出た事項に変更があった場合には、速やかに当社に連絡するものとします。 2. 変更登録がなされなかったことにより生じた損害について、当社は一切責任を負いません。また、変更登録がなされた場合でも、変更登録前にすでに手続がなされた取引は、変更登録前の情報に基づいて行われますのでご注意ください。', 10, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (4, '第4条（退会）', '会員が退会を希望する場合には、会員本人が退会手続きを行ってください。所定の退会手続の終了後に、退会となります。', 9, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (5, '第5条（会員資格の喪失及び賠償義務）', '1. 会員が、会員資格取得申込の際に虚偽の申告をしたとき、通信販売による代金支払債務を怠ったとき、その他当社が会員として不適当と認める事由があるときは、当社は、会員資格を取り消すことができることとします。 2. 会員が、以下の各号に定める行為をしたときは、これにより当社が被った損害を賠償する責任を負います。 (1)会員番号、パスワードを不正に利用すること (2)当ホームページにアクセスして情報を改ざんしたり、当ホームページに有害なコンピュータプログラムを送信するなどして、当社の営業を妨害すること (3)当社が扱う商品の知的所有権を侵害する行為をすること (4)その他、この利用規約に反する行為をすること', 8, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (6, '第6条（会員情報の取扱い）', '1. 当社は、原則として会員情報を会員の事前の同意なく第三者に対して開示することはありません。ただし、次の各号の場合には、会員の事前の同意なく、当社は会員情報その他のお客様情報を開示できるものとします。 (1)法令に基づき開示を求められた場合 (2)当社の権利、利益、名誉等を保護するために必要であると当社が判断した場合 2. 会員情報につきましては、当社の「個人情報保護への取組み」に従い、当社が管理します。当社は、会員情報を、会員へのサービス提供、サービス内容の向上、サービスの利用促進、およびサービスの健全かつ円滑な運営の確保を図る目的のために、当社おいて利用することができるものとします。 3. 当社は、会員に対して、メールマガジンその他の方法による情報提供（広告を含みます）を行うことができるものとします。会員が情報提供を希望しない場合は、当社所定の方法に従い、その旨を通知して頂ければ、情報提供を停止します。ただし、本サービス運営に必要な情報提供につきましては、会員の希望により停止をすることはできません。', 7, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (7, '第7条（禁止事項）', '本サービスの利用に際して、会員に対し次の各号の行為を行うことを禁止します。  1. 法令または本規約、本サービスご利用上のご注意、本サービスでのお買い物上のご注意その他の本規約等に違反すること 2. 当社、およびその他の第三者の権利、利益、名誉等を損ねること 3. 青少年の心身に悪影響を及ぼす恐れがある行為、その他公序良俗に反する行為を行うこと 4. 他の利用者その他の第三者に迷惑となる行為や不快感を抱かせる行為を行うこと 5. 虚偽の情報を入力すること 6. 有害なコンピュータプログラム、メール等を送信または書き込むこと 7. 当社のサーバその他のコンピュータに不正にアクセスすること 8. パスワードを第三者に貸与・譲渡すること、または第三者と共用すること 9. その他当社が不適切と判断すること', 6, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (8, '第8条（サービスの中断・停止等）', '1. 当社は、本サービスの稼動状態を良好に保つために、次の各号の一に該当する場合、予告なしに、本サービスの提供全てあるいは一部を停止することがあります。 (1)システムの定期保守および緊急保守のために必要な場合 (2)システムに負荷が集中した場合 (3)火災、停電、第三者による妨害行為などによりシステムの運用が困難になった場合 (4)その他、止むを得ずシステムの停止が必要と当社が判断した場合', 5, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (9, '第9条（サービスの変更・廃止）', '当社は、その判断によりサービスの全部または一部を事前の通知なく、適宜変更・廃止できるものとします。', 4, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (10, '第10条（免責）', '1. 通信回線やコンピュータなどの障害によるシステムの中断・遅滞・中止・データの消失、データへの不正アクセスにより生じた損害、その他当社のサービスに関して会員に生じた損害について、当社は一切責任を負わないものとします。 2. 当社は、当社のウェブページ・サーバ・ドメインなどから送られるメール・コンテンツに、コンピュータ・ウィルスなどの有害なものが含まれていないことを保証いたしません。 3. 会員が本規約等に違反したことによって生じた損害については、当社は一切責任を負いません。', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (11, '第11条（本規約の改定）', '当社は、本規約を任意に改定できるものとし、また、当社において本規約を補充する規約（以下「補充規約」といいます）を定めることができます。本規約の改定または補充は、改定後の本規約または補充規約を当社所定のサイトに掲示したときにその効力を生じるものとします。この場合、会員は、改定後の規約および補充規約に従うものと致します。', 2, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_kiyaku (kiyaku_id, kiyaku_title, kiyaku_text, rank, creator_id, create_date, update_date, del_flg) VALUES (12, '第12条（準拠法、管轄裁判所）', '本規約に関して紛争が生じた場合、当社本店所在地を管轄する地方裁判所を第一審の専属的合意管轄裁判所とします。 ', 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);


--
-- Data for Name: dtb_mail_history; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_mailmaga_template; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_mailtemplate; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_mailtemplate (template_id, subject, "header", footer, creator_id, del_flg, create_date, update_date) VALUES (1, 'ご注文ありがとうございます。', 'この度はご注文いただき誠に有難うございます。 下記ご注文内容にお間違えがないかご確認下さい。  ', '  ==============================================================☆   このメッセージはお客様へのお知らせ専用ですので、 このメッセージへの返信としてご質問をお送りいただいても回答できません。 ご了承ください。  ご質問やご不明な点がございましたら、こちらからお願いいたします。 http://------.co.jp  ', 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_member; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_member (member_id, name, department, login_id, "password", authority, rank, "work", del_flg, creator_id, update_date, create_date, login_date) VALUES (1, 'dummy', NULL, 'dummy', ' ', 0, 0, 1, 1, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);
INSERT INTO dtb_member (member_id, name, department, login_id, "password", authority, rank, "work", del_flg, creator_id, update_date, create_date, login_date) VALUES (2, '管理者', NULL, 'nanasess', '28be58e756294fc0b1ea693e1fabea92223f12f2', 0, 1, 1, 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2007-07-19 12:19:18');


--
-- Data for Name: dtb_mobile_ext_session_id; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_mobile_kara_mail; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_module; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_news; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_news (news_id, news_date, rank, news_title, news_comment, news_url, news_select, link_method, creator_id, create_date, update_date, del_flg) VALUES (1, CURRENT_TIMESTAMP, 1, 'サイトオープンいたしました!', '一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！一人暮らしからオフィスなどさまざまなシーンで あなたの生活をサポートするグッズをご家庭へお届けします！', NULL, 0, NULL, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);


--
-- Data for Name: dtb_order; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_order_detail; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_order_temp; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_other_deliv; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_pagelayout; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (1, 'TOPページ', 'index.php', ' ', 'user_data/templates/', 'top', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (2, '商品一覧ページ', 'products/list.php', ' ', 'user_data/templates/', 'list', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (3, '商品詳細ページ', 'products/detail.php', ' ', 'user_data/templates/', 'detail', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (4, 'MYページ', 'mypage/index.php', ' ', '', '', 1, 1, 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_pagelayout (page_id, page_name, url, php_dir, tpl_dir, filename, header_chk, footer_chk, edit_flg, author, description, keyword, update_url, create_date, update_date) VALUES (0, 'プレビューデータ', ' ', NULL, NULL, NULL, 1, 1, 1, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_payment; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_payment (payment_id, payment_method, charge, "rule", deliv_id, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (1, '郵便振替', 0, NULL, 1, 4, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, "rule", deliv_id, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (2, '現金書留', 0, NULL, 1, 3, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, "rule", deliv_id, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (3, '銀行振込', 0, NULL, 1, 2, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO dtb_payment (payment_id, payment_method, charge, "rule", deliv_id, rank, note, fix, status, del_flg, creator_id, create_date, update_date, payment_image, upper_rule, charge_flg, rule_min, upper_rule_max, module_id, module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10) VALUES (4, '代金引換', 0, NULL, 1, 1, NULL, 2, 1, 0, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


--
-- Data for Name: dtb_products; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_products (product_id, name, deliv_fee, sale_limit, sale_unlimited, category_id, rank, status, product_flag, point_rate, comment1, comment2, comment3, comment4, comment5, comment6, file1, file2, file3, file4, file5, file6, main_list_comment, main_list_image, main_comment, main_image, main_large_image, sub_title1, sub_comment1, sub_image1, sub_large_image1, sub_title2, sub_comment2, sub_image2, sub_large_image2, sub_title3, sub_comment3, sub_image3, sub_large_image3, sub_title4, sub_comment4, sub_image4, sub_large_image4, sub_title5, sub_comment5, sub_image5, sub_large_image5, sub_title6, sub_comment6, sub_image6, sub_large_image6, del_flg, creator_id, create_date, update_date, deliv_date_id) VALUES (1, 'アイスクリーム', NULL, NULL, 1, 5, 1, 1, '10010', 10, NULL, NULL, 'アイス,バニラ,チョコ,抹茶', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '暑い夏にどうぞ。', '08311201_44f65122ee5fe.jpg', '冷たいものはいかがですか？', '08311202_44f6515906a41.jpg', '08311203_44f651959bcb5.jpg', NULL, '<b>おいしいよ<b>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 2);
INSERT INTO dtb_products (product_id, name, deliv_fee, sale_limit, sale_unlimited, category_id, rank, status, product_flag, point_rate, comment1, comment2, comment3, comment4, comment5, comment6, file1, file2, file3, file4, file5, file6, main_list_comment, main_list_image, main_comment, main_image, main_large_image, sub_title1, sub_comment1, sub_image1, sub_large_image1, sub_title2, sub_comment2, sub_image2, sub_large_image2, sub_title3, sub_comment3, sub_image3, sub_large_image3, sub_title4, sub_comment4, sub_image4, sub_large_image4, sub_title5, sub_comment5, sub_image5, sub_large_image5, sub_title6, sub_comment6, sub_image6, sub_large_image6, del_flg, creator_id, create_date, update_date, deliv_date_id) VALUES (2, 'おなべ', NULL, 5, NULL, 4, 1, 1, '11001', 5, NULL, NULL, '鍋,なべ,ナベ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '一人用からあります。', '08311311_44f661811fec0.jpg', 'たまには鍋でもどうでしょう。', '08311313_44f661dc649fb.jpg', '08311313_44f661e5698a6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 3);


--
-- Data for Name: dtb_products_class; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (2, 1, 3, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (3, 1, 3, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (4, 1, 3, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (5, 1, 2, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (6, 1, 2, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (7, 1, 2, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (8, 1, 1, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (9, 1, 1, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (10, 1, 1, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_products_class (product_class_id, product_id, classcategory_id1, classcategory_id2, product_code, stock, stock_unlimited, sale_limit, price01, price02, status, creator_id, create_date, update_date) VALUES (11, 2, 0, 0, 'nabe-01', 100, NULL, NULL, 1700, 1650, NULL, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_question; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_question_result; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_recommend_products; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_recommend_products (product_id, recommend_product_id, rank, "comment", status, creator_id, create_date, update_date) VALUES (2, 1, 4, 'お口直しに。', 0, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_review; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_review (review_id, product_id, reviewer_name, reviewer_url, sex, customer_id, recommend_level, title, "comment", status, creator_id, create_date, update_date, del_flg) VALUES (1, 1, '大河内けんたろう', NULL, NULL, NULL, 4, '無題', 'テスト', 2, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_review (review_id, product_id, reviewer_name, reviewer_url, sex, customer_id, recommend_level, title, "comment", status, creator_id, create_date, update_date, del_flg) VALUES (2, 2, '大河内健太郎', NULL, NULL, NULL, 4, 'テスト', 'テスト', 2, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);
INSERT INTO dtb_review (review_id, product_id, reviewer_name, reviewer_url, sex, customer_id, recommend_level, title, "comment", status, creator_id, create_date, update_date, del_flg) VALUES (3, 2, '大河内健太郎', NULL, NULL, NULL, 5, 'テスト2', 'テスト', 2, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0);


--
-- Data for Name: dtb_send_customer; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_send_history; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_session; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_session (sess_id, sess_data, create_date, update_date) VALUES ('4ae91d90b65cfb3553e1c4a0e87de817', 'cart|a:1:{s:8:"prev_url";s:10:"/index.php";}customer|N;', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO dtb_session (sess_id, sess_data, create_date, update_date) VALUES ('047f9dc6cfd1cff98d24f9650514fb58', 'cart|a:1:{s:8:"prev_url";s:1:"/";}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_site_control; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_site_control (control_id, control_title, control_text, control_flg, del_flg, memo, create_date, update_date) VALUES (1, 'トラックバック機能', 'トラックバック機能を使用するかどうかを決定します。', 2, 0, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_table_comment; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (1, 'dtb_baseinfo', 'company_name', '会社名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (2, 'dtb_baseinfo', 'company_kana', '会社名（カナ）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (3, 'dtb_baseinfo', 'zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (4, 'dtb_baseinfo', 'zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (5, 'dtb_baseinfo', 'pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (6, 'dtb_baseinfo', 'addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (7, 'dtb_baseinfo', 'addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (8, 'dtb_baseinfo', 'tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (9, 'dtb_baseinfo', 'tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (10, 'dtb_baseinfo', 'tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (11, 'dtb_baseinfo', 'fax01', 'FAX番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (12, 'dtb_baseinfo', 'fax02', 'FAX番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (13, 'dtb_baseinfo', 'fax03', 'FAX番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (14, 'dtb_baseinfo', 'business_hour', '店舗営業時間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (15, 'dtb_baseinfo', 'law_company', '販売業者(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (16, 'dtb_baseinfo', 'law_manager', '運営責任者(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (17, 'dtb_baseinfo', 'law_zip01', '郵便番号1(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (18, 'dtb_baseinfo', 'law_zip02', '郵便番号2(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (19, 'dtb_baseinfo', 'law_pref', '都道府県(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (20, 'dtb_baseinfo', 'law_addr01', '住所1(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (21, 'dtb_baseinfo', 'law_addr02', '住所2(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (22, 'dtb_baseinfo', 'law_tel01', '電話番号1(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (23, 'dtb_baseinfo', 'law_tel02', '電話番号2(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (24, 'dtb_baseinfo', 'law_tel03', '電話番号3(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (25, 'dtb_baseinfo', 'law_fax01', 'FAX番号1(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (26, 'dtb_baseinfo', 'law_fax02', 'FAX番号2(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (27, 'dtb_baseinfo', 'law_fax03', 'FAX番号3(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (28, 'dtb_baseinfo', 'law_email', 'メールアドレス(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (29, 'dtb_baseinfo', 'law_url', 'URL(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (30, 'dtb_baseinfo', 'law_term01', '商品代金以外の必要料金(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (31, 'dtb_baseinfo', 'law_term02', '注文方法(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (32, 'dtb_baseinfo', 'law_term03', '支払方法(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (33, 'dtb_baseinfo', 'law_term04', '支払期限(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (34, 'dtb_baseinfo', 'law_term05', '引き渡し時期(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (35, 'dtb_baseinfo', 'law_term06', '返品・交換について(特定商取引)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (36, 'dtb_baseinfo', 'law_term07', '予備');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (37, 'dtb_baseinfo', 'law_term08', '予備');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (38, 'dtb_baseinfo', 'law_term09', '予備');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (39, 'dtb_baseinfo', 'law_term10', '予備');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (40, 'dtb_baseinfo', 'tax', '消費税');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (41, 'dtb_baseinfo', 'tax_rule', '1:四捨五入　2：切捨て　3:切り上げ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (42, 'dtb_baseinfo', 'email01', '受注情報受付メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (43, 'dtb_baseinfo', 'email02', '問い合わせ受付メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (44, 'dtb_baseinfo', 'email03', '送信エラー受付メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (45, 'dtb_baseinfo', 'email04', 'メール送信元メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (46, 'dtb_baseinfo', 'email05', 'メルマガ送信元メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (47, 'dtb_baseinfo', 'free_rule', '送料・手数料無料条件(円以上)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (48, 'dtb_baseinfo', 'shop_name', '店名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (49, 'dtb_baseinfo', 'shop_kana', '店名(カナ)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (50, 'dtb_baseinfo', 'point_rate', 'ポイント付与率');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (51, 'dtb_baseinfo', 'welcome_point', '会員登録時付与ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (52, 'dtb_baseinfo', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (53, 'dtb_baseinfo', 'top_tpl', 'topのテンプレートファイル番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (54, 'dtb_baseinfo', 'product_tpl', '商品一覧のテンプレートファイル番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (55, 'dtb_baseinfo', 'detail_tpl', '商品詳細のテンプレートファイル番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (56, 'dtb_baseinfo', 'mypage_tpl', 'MYページのテンプレートファイル番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (57, 'dtb_deliv', 'deliv_id', '配送業者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (58, 'dtb_deliv', 'name', '配送業者名（ヤマト運輸）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (59, 'dtb_deliv', 'service_name', '名称（クロネコヤマト）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (60, 'dtb_deliv', 'confirm_url', '伝票確認URL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (61, 'dtb_deliv', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (62, 'dtb_deliv', 'status', '1:有効 2:無効');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (63, 'dtb_deliv', 'del_flg', '0:既定 1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (64, 'dtb_deliv', 'creator_id', '作成者の管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (65, 'dtb_deliv', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (66, 'dtb_deliv', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (67, 'dtb_delivtime', 'deliv_id', '配送ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (68, 'dtb_delivtime', 'time_id', '配送時間ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (69, 'dtb_delivtime', 'time', '配送時間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (70, 'dtb_delivfee', 'deliv_id', '配送ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (71, 'dtb_delivfee', 'fee_id', '配送料金ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (72, 'dtb_delivfee', 'fee', '配送料金');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (73, 'dtb_payment', 'payment_id', '支払いID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (74, 'dtb_payment', 'payment_method', '支払い方法');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (75, 'dtb_payment', 'charge', '手数料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (76, 'dtb_payment', 'rule', '利用条件(円以上)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (77, 'dtb_payment', 'deliv_id', '指定無しの場合:0(配送業者）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (78, 'dtb_payment', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (79, 'dtb_payment', 'note', '備考');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (80, 'dtb_payment', 'fix', '固定:1　自由設定:2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (81, 'dtb_payment', 'status', '1:有効 2:無効');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (82, 'dtb_payment', 'del_flg', '0:既定 1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (83, 'dtb_payment', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (84, 'dtb_payment', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (85, 'dtb_payment', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (86, 'dtb_payment', 'payment_image', '支払い方法表示用ロゴ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (87, 'dtb_payment', 'upper_rule', '利用条件(円以下)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (88, 'dtb_mailtemplate', 'template_id', 'テンプレートID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (89, 'dtb_mailtemplate', 'subject', 'メールタイトル');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (90, 'dtb_mailtemplate', 'header', 'ヘッダー文書');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (91, 'dtb_mailtemplate', 'footer', 'フッター文書');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (92, 'dtb_mailtemplate', 'creator_id', '作成した管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (93, 'dtb_mailtemplate', 'del_flg', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (94, 'dtb_mailtemplate', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (95, 'dtb_mailtemplate', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (96, 'dtb_mailmaga_template', 'template_id', 'テンプレートID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (97, 'dtb_mailmaga_template', 'subject', '件名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (98, 'dtb_mailmaga_template', 'charge_image', '担当者の写真');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (99, 'dtb_mailmaga_template', 'mail_method', '1:テキストメール 2:HTMLメール 3:HTMLTEMPLATE');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (100, 'dtb_mailmaga_template', 'header', 'ヘッダーテキスト');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (101, 'dtb_mailmaga_template', 'body', '本文（テキスト登録用）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (102, 'dtb_mailmaga_template', 'main_title', 'メインのタイトル（HTMLメール専用）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (103, 'dtb_mailmaga_template', 'main_comment', 'メインのコメント（HTMLメール専用）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (104, 'dtb_mailmaga_template', 'main_product_id', 'メインの商品ID（HTMLメール専用）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (105, 'dtb_mailmaga_template', 'sub_title', 'サブタイトル');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (106, 'dtb_mailmaga_template', 'sub_comment', 'サブコメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (107, 'dtb_mailmaga_template', 'sub_product_id01', 'サブ商品ID1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (108, 'dtb_mailmaga_template', 'sub_product_id02', 'サブ商品ID2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (109, 'dtb_mailmaga_template', 'sub_product_id03', 'サブ商品ID3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (110, 'dtb_mailmaga_template', 'sub_product_id04', 'サブ商品ID4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (111, 'dtb_mailmaga_template', 'sub_product_id05', 'サブ商品ID5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (112, 'dtb_mailmaga_template', 'sub_product_id06', 'サブ商品ID6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (113, 'dtb_mailmaga_template', 'sub_product_id07', 'サブ商品ID7');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (114, 'dtb_mailmaga_template', 'sub_product_id08', 'サブ商品ID8');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (115, 'dtb_mailmaga_template', 'sub_product_id09', 'サブ商品ID9');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (116, 'dtb_mailmaga_template', 'sub_product_id10', 'サブ商品ID10');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (117, 'dtb_mailmaga_template', 'sub_product_id11', 'サブ商品ID11');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (118, 'dtb_mailmaga_template', 'sub_product_id12', 'サブ商品ID12');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (119, 'dtb_mailmaga_template', 'del_flg', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (120, 'dtb_mailmaga_template', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (121, 'dtb_mailmaga_template', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (122, 'dtb_mailmaga_template', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (123, 'dtb_send_history', 'send_id', '配信ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (124, 'dtb_send_history', 'mail_method', '1:テキスト 2:HTML');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (125, 'dtb_send_history', 'subject', 'メール件名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (126, 'dtb_send_history', 'body', 'メール内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (127, 'dtb_send_history', 'send_count', '送信数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (128, 'dtb_send_history', 'complete_count', '送信完了数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (129, 'dtb_send_history', 'start_date', '送信開始');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (130, 'dtb_send_history', 'end_date', '送信停止');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (131, 'dtb_send_history', 'search_data', '検索条件');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (132, 'dtb_send_history', 'del_flg', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (133, 'dtb_send_history', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (134, 'dtb_send_history', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (135, 'dtb_send_history', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (136, 'dtb_send_customer', 'customer_id', '顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (137, 'dtb_send_customer', 'send_id', '配信履歴ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (138, 'dtb_send_customer', 'email', '送信先メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (139, 'dtb_send_customer', 'name', '名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (140, 'dtb_send_customer', 'send_flag', '1:送信済み　2:送信失敗');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (141, 'dtb_products', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (142, 'dtb_products', 'name', '商品名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (143, 'dtb_products', 'deliv_fee', '商品送料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (144, 'dtb_products', 'sale_limit', '購入制限数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (145, 'dtb_products', 'sale_unlimited', '購入制限（1:購入制限無し)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (146, 'dtb_products', 'category_id', '商品カテゴリー');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (147, 'dtb_products', 'rank', '表示ランク');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (148, 'dtb_products', 'status', '1:表示、2:非表示、3:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (149, 'dtb_products', 'product_flag', '"1:NEW 2:お勧め 3:注目 4:限定"');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (150, 'dtb_products', 'point_rate', 'ポイント付与率');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (151, 'dtb_products', 'comment1', 'コメント1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (152, 'dtb_products', 'comment2', 'コメント2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (153, 'dtb_products', 'comment3', 'コメント3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (154, 'dtb_products', 'comment4', 'コメント4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (155, 'dtb_products', 'comment5', 'コメント5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (156, 'dtb_products', 'comment6', 'コメント6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (157, 'dtb_products', 'file1', 'アップロードファイル1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (158, 'dtb_products', 'file2', 'アップロードファイル2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (159, 'dtb_products', 'file3', 'アップロードファイル3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (160, 'dtb_products', 'file4', 'アップロードファイル4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (161, 'dtb_products', 'file5', 'アップロードファイル5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (162, 'dtb_products', 'file6', 'アップロードファイル6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (163, 'dtb_products', 'main_list_comment', 'メイン一覧コメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (164, 'dtb_products', 'main_list_image', 'メイン一覧画像');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (165, 'dtb_products', 'main_comment', 'メインコメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (166, 'dtb_products', 'main_image', 'メイン画像');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (167, 'dtb_products', 'main_large_image', 'メイン拡大画像');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (168, 'dtb_products', 'sub_title1', 'サブタイトル1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (169, 'dtb_products', 'sub_comment1', 'サブコメント1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (170, 'dtb_products', 'sub_image1', 'サブ通常画像1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (171, 'dtb_products', 'sub_large_image1', 'サブ拡大画像1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (172, 'dtb_products', 'sub_title2', 'サブタイトル2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (173, 'dtb_products', 'sub_comment2', 'サブコメント2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (174, 'dtb_products', 'sub_image2', 'サブ通常画像2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (175, 'dtb_products', 'sub_large_image2', 'サブ拡大画像2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (176, 'dtb_products', 'sub_title3', 'サブタイトル3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (177, 'dtb_products', 'sub_comment3', 'サブコメント3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (178, 'dtb_products', 'sub_image3', 'サブ通常画像3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (179, 'dtb_products', 'sub_large_image3', 'サブ拡大画像3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (180, 'dtb_products', 'sub_title4', 'サブタイトル4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (181, 'dtb_products', 'sub_comment4', 'サブコメント4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (182, 'dtb_products', 'sub_image4', 'サブ通常画像4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (183, 'dtb_products', 'sub_large_image4', 'サブ拡大画像4');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (184, 'dtb_products', 'sub_title5', 'サブタイトル5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (185, 'dtb_products', 'sub_comment5', 'サブコメント5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (186, 'dtb_products', 'sub_image5', 'サブ通常画像5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (187, 'dtb_products', 'sub_large_image5', 'サブ拡大画像5');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (188, 'dtb_products', 'sub_title6', 'サブタイトル6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (189, 'dtb_products', 'sub_comment6', 'サブコメント6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (190, 'dtb_products', 'sub_image6', 'サブ通常画像6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (191, 'dtb_products', 'sub_large_image6', 'サブ拡大画像6');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (192, 'dtb_products', 'del_flg', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (193, 'dtb_products', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (194, 'dtb_products', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (195, 'dtb_products', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (196, 'dtb_products', 'deliv_date_id', '発送日目安(conf.php参照)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (197, 'dtb_products_class', 'product_class_id', '商品規格ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (198, 'dtb_products_class', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (199, 'dtb_products_class', 'classcategory_id1', '規格分類ID1(規格なしの場合0)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (200, 'dtb_products_class', 'classcategory_id2', '規格分類ID2（規格なしの場合:0)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (201, 'dtb_products_class', 'product_code', '商品コード');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (202, 'dtb_products_class', 'stock', '在庫数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (203, 'dtb_products_class', 'stock_unlimited', '在庫制限（1:無制限)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (204, 'dtb_products_class', 'sale_limit', '販売制限');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (205, 'dtb_products_class', 'price01', '価格');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (206, 'dtb_products_class', 'price02', '商品価格');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (207, 'dtb_products_class', 'status', '状態（表示:1、非表示:2）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (208, 'dtb_products_class', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (209, 'dtb_products_class', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (210, 'dtb_products_class', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (211, 'dtb_class', 'class_id', '規格ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (212, 'dtb_class', 'name', '規格名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (213, 'dtb_class', 'status', '状態');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (214, 'dtb_class', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (215, 'dtb_class', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (216, 'dtb_class', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (217, 'dtb_class', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (218, 'dtb_class', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (219, 'dtb_class', 'product_id', 'null：マスタ登録データ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (220, 'dtb_classcategory', 'classcategory_id', '規格分類ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (221, 'dtb_classcategory', 'name', '規格分類名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (222, 'dtb_classcategory', 'class_id', '規格ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (223, 'dtb_classcategory', 'status', '状態');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (224, 'dtb_classcategory', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (225, 'dtb_classcategory', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (226, 'dtb_classcategory', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (227, 'dtb_classcategory', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (228, 'dtb_classcategory', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (229, 'dtb_category', 'category_id', '分類ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (230, 'dtb_category', 'category_name', '分類名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (231, 'dtb_category', 'parent_category_id', 'rootの場合:0');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (232, 'dtb_category', 'level', '階層');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (233, 'dtb_category', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (234, 'dtb_category', 'creator_id', '作成者の管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (235, 'dtb_category', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (236, 'dtb_category', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (237, 'dtb_category', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (238, 'dtb_bat_order_daily', 'total_order', '購入件数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (239, 'dtb_bat_order_daily', 'member', '会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (240, 'dtb_bat_order_daily', 'nonmember', '非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (241, 'dtb_bat_order_daily', 'men', '男性');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (242, 'dtb_bat_order_daily', 'women', '女性');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (243, 'dtb_bat_order_daily', 'men_member', '男性会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (244, 'dtb_bat_order_daily', 'men_nonmember', '男性非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (245, 'dtb_bat_order_daily', 'women_member', '女性会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (246, 'dtb_bat_order_daily', 'women_nonmember', '女性非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (247, 'dtb_bat_order_daily', 'total', '購入合計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (248, 'dtb_bat_order_daily', 'total_average', '購入平均');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (249, 'dtb_bat_order_daily', 'order_date', '購入日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (250, 'dtb_bat_order_daily', 'create_date', '集計日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (251, 'dtb_bat_order_daily', 'year', '年');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (252, 'dtb_bat_order_daily', 'month', '月');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (253, 'dtb_bat_order_daily', 'day', '日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (254, 'dtb_bat_order_daily', 'wday', '曜日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (255, 'dtb_bat_order_daily', 'key_day', '日別用キー');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (256, 'dtb_bat_order_daily', 'key_month', '月別用キー');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (257, 'dtb_bat_order_daily', 'key_year', '年別用キー');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (258, 'dtb_bat_order_daily', 'key_wday', '曜日別用キー');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (259, 'dtb_bat_order_daily_hour', 'total_order', '購入件数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (260, 'dtb_bat_order_daily_hour', 'member', '会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (261, 'dtb_bat_order_daily_hour', 'nonmember', '非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (262, 'dtb_bat_order_daily_hour', 'men', '男性');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (263, 'dtb_bat_order_daily_hour', 'women', '女性');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (264, 'dtb_bat_order_daily_hour', 'men_member', '男性会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (265, 'dtb_bat_order_daily_hour', 'men_nonmember', '男性非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (266, 'dtb_bat_order_daily_hour', 'women_member', '女性会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (267, 'dtb_bat_order_daily_hour', 'women_nonmember', '女性非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (268, 'dtb_bat_order_daily_hour', 'total', '購入合計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (269, 'dtb_bat_order_daily_hour', 'total_average', '購入平均');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (270, 'dtb_bat_order_daily_hour', 'hour', '購入時間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (271, 'dtb_bat_order_daily_hour', 'order_date', '購入日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (272, 'dtb_bat_order_daily_hour', 'create_date', '集計日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (273, 'dtb_recommend_products', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (274, 'dtb_recommend_products', 'recommend_product_id', 'リコメンド商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (275, 'dtb_recommend_products', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (276, 'dtb_recommend_products', 'comment', 'コメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (277, 'dtb_recommend_products', 'status', '0:手動登録、1:自動登録');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (278, 'dtb_recommend_products', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (279, 'dtb_recommend_products', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (280, 'dtb_recommend_products', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (281, 'dtb_review', 'review_id', 'レビューID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (282, 'dtb_review', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (283, 'dtb_review', 'reviewer_name', '投稿者名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (284, 'dtb_review', 'reviewer_url', '投稿者URL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (285, 'dtb_review', 'sex', '男性:1、女性:2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (286, 'dtb_review', 'customer_id', '会員ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (287, 'dtb_review', 'recommend_level', 'おすすめレベル');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (288, 'dtb_review', 'title', '題名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (289, 'dtb_review', 'comment', 'コメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (290, 'dtb_review', 'status', '1:表示 2:非表示 3:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (291, 'dtb_review', 'creator_id', '作成者の管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (292, 'dtb_review', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (293, 'dtb_review', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (294, 'dtb_review', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (295, 'dtb_customer_reading', 'reading_product_id', '閲覧商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (296, 'dtb_customer_reading', 'customer_id', '会員ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (297, 'dtb_customer_reading', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (298, 'dtb_customer_reading', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (299, 'dtb_category_count', 'category_id', '分類ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (300, 'dtb_category_count', 'product_count', '商品数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (301, 'dtb_category_count', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (302, 'dtb_category_total_count', 'category_id', '分類ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (303, 'dtb_category_total_count', 'product_count', '商品数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (304, 'dtb_category_total_count', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (305, 'dtb_news', 'news_id', '新着ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (306, 'dtb_news', 'news_date', '表示日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (307, 'dtb_news', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (308, 'dtb_news', 'news_comment', 'コメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (309, 'dtb_news', 'news_url', 'URL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (310, 'dtb_news', 'link_method', '1:内部リンク 2:外部リンク（別ウィンドウで）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (311, 'dtb_news', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (312, 'dtb_news', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (313, 'dtb_news', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (314, 'dtb_news', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (315, 'dtb_best_products', 'best_id', '注文ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (316, 'dtb_best_products', 'category_id', 'カテゴリID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (317, 'dtb_best_products', 'rank', '順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (318, 'dtb_best_products', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (319, 'dtb_best_products', 'title', '見出し');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (320, 'dtb_best_products', 'comment', 'コメント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (321, 'dtb_best_products', 'creator_id', '作成者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (322, 'dtb_best_products', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (323, 'dtb_best_products', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (324, 'dtb_best_products', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (325, 'dtb_mail_history', 'send_id', '送信ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (326, 'dtb_mail_history', 'order_id', '注文ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (327, 'dtb_mail_history', 'send_date', '送信日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (328, 'dtb_mail_history', 'template_id', 'メールテンプレートID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (329, 'dtb_mail_history', 'creator_id', '管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (330, 'dtb_mail_history', 'subject', '件名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (331, 'dtb_mail_history', 'mail_body', '本文');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (332, 'dtb_customer', 'customer_id', '顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (333, 'dtb_customer', 'name01', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (334, 'dtb_customer', 'name02', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (335, 'dtb_customer', 'kana01', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (336, 'dtb_customer', 'kana02', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (337, 'dtb_customer', 'zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (338, 'dtb_customer', 'zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (339, 'dtb_customer', 'pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (340, 'dtb_customer', 'addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (341, 'dtb_customer', 'addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (342, 'dtb_customer', 'email', 'メール');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (343, 'dtb_customer', 'email_mobile', '携帯メール');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (344, 'dtb_customer', 'tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (345, 'dtb_customer', 'tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (346, 'dtb_customer', 'tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (347, 'dtb_customer', 'fax01', 'FAX1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (348, 'dtb_customer', 'fax02', 'FAX2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (349, 'dtb_customer', 'fax03', 'FAX3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (350, 'dtb_customer', 'sex', '1:男　2:女');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (351, 'dtb_customer', 'job', '職業');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (352, 'dtb_customer', 'birth', '誕生日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (353, 'dtb_customer', 'password', 'パスワード');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (354, 'dtb_customer', 'reminder', 'パスワード質問');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (355, 'dtb_customer', 'reminder_answer', 'パスワード答え');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (356, 'dtb_customer', 'secret_key', '暗号化顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (357, 'dtb_customer', 'first_buy_date', '初回購入');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (358, 'dtb_customer', 'last_buy_date', '最終購入');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (359, 'dtb_customer', 'buy_times', '購入回数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (360, 'dtb_customer', 'buy_total', '購入累計金額');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (361, 'dtb_customer', 'point', 'ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (362, 'dtb_customer', 'note', 'SHOPメモ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (363, 'dtb_customer', 'status', '1:仮登録 2:登録 3:停止 4:非会員');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (364, 'dtb_customer', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (365, 'dtb_customer', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (366, 'dtb_customer', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (367, 'dtb_customer', 'cell01', '携帯電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (368, 'dtb_customer', 'cell02', '携帯電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (369, 'dtb_customer', 'cell03', '携帯電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (370, 'dtb_customer', 'mobile_phone_id', '携帯端末ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (371, 'dtb_customer_mail', 'email', 'メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (372, 'dtb_customer_mail', 'mail_flag', '1:HTML 2:TEXT 3:希望しない 4:仮登録（HTML） 5:仮登録(TEXT) 6:仮登録(希望しない)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (373, 'dtb_customer_mail', 'secret_key', '暗号化顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (374, 'dtb_customer_mail_temp', 'email', 'メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (375, 'dtb_customer_mail_temp', 'mail_flag', '1:HTML 2:TEXT 3:希望しない(停止)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (376, 'dtb_customer_mail_temp', 'temp_id', '一時ＩＤ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (377, 'dtb_customer_mail_temp', 'end_flag', '0:未処理 1:処理済');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (378, 'dtb_customer_mail_temp', 'update_date', '更新日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (379, 'dtb_customer_mail_temp', 'create_data', '作成日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (380, 'dtb_order', 'order_id', '注文ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (381, 'dtb_order', 'order_temp_id', '注文一時ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (382, 'dtb_order', 'customer_id', '顧客ID(非会員の場合は、0)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (383, 'dtb_order', 'message', '要望等');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (384, 'dtb_order', 'order_name01', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (385, 'dtb_order', 'order_name02', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (386, 'dtb_order', 'order_kana01', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (387, 'dtb_order', 'order_kana02', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (388, 'dtb_order', 'order_email', '受注時のメールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (389, 'dtb_order', 'order_tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (390, 'dtb_order', 'order_tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (391, 'dtb_order', 'order_tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (392, 'dtb_order', 'order_fax01', 'FAX1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (393, 'dtb_order', 'order_fax02', 'FAX2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (394, 'dtb_order', 'order_fax03', 'FAX3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (395, 'dtb_order', 'order_zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (396, 'dtb_order', 'order_zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (397, 'dtb_order', 'order_pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (398, 'dtb_order', 'order_addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (399, 'dtb_order', 'order_addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (400, 'dtb_order', 'order_sex', '性別(1:男性、2:女性)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (401, 'dtb_order', 'order_birth', '生年月日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (402, 'dtb_order', 'order_job', '職種');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (403, 'dtb_order', 'deliv_name01', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (404, 'dtb_order', 'deliv_name02', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (405, 'dtb_order', 'deliv_kana01', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (406, 'dtb_order', 'deliv_kana02', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (407, 'dtb_order', 'deliv_tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (408, 'dtb_order', 'deliv_tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (409, 'dtb_order', 'deliv_tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (410, 'dtb_order', 'deliv_fax01', 'FAX1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (411, 'dtb_order', 'deliv_fax02', 'FAX2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (412, 'dtb_order', 'deliv_fax03', 'FAX3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (413, 'dtb_order', 'deliv_zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (414, 'dtb_order', 'deliv_zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (415, 'dtb_order', 'deliv_pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (416, 'dtb_order', 'deliv_addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (417, 'dtb_order', 'deliv_addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (418, 'dtb_order', 'subtotal', '小計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (419, 'dtb_order', 'discount', '値引き');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (420, 'dtb_order', 'deliv_fee', '送料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (421, 'dtb_order', 'charge', '手数料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (422, 'dtb_order', 'use_point', '使用ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (423, 'dtb_order', 'add_point', '加算ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (424, 'dtb_order', 'birth_point', 'お誕生日ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (425, 'dtb_order', 'tax', '税金');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (426, 'dtb_order', 'total', '合計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (427, 'dtb_order', 'payment_total', 'お支払い合計(ポイント差し引き合計)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (428, 'dtb_order', 'payment_id', '支払い方法ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (429, 'dtb_order', 'payment_method', '支払い方法');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (430, 'dtb_order', 'deliv_id', '配送業者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (431, 'dtb_order', 'deliv_time_id', '配送時間ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (432, 'dtb_order', 'deliv_time', '配送時間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (433, 'dtb_order', 'deliv_no', '配送伝票番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (434, 'dtb_order', 'note', 'SHOPメモ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (435, 'dtb_order', 'status', '1:対応中 2:キャンセル 3:取り寄せ中 4:発送済み');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (436, 'dtb_order', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (437, 'dtb_order', 'loan_result', 'ローン受付結果');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (438, 'dtb_order', 'credit_result', 'クレジット受付結果');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (439, 'dtb_order', 'credit_msg', 'クレジット受付メッセージ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (440, 'dtb_order', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (441, 'dtb_order', 'commit_date', '発送済みステータスに変更した日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (442, 'dtb_order', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (443, 'dtb_order', 'deliv_date', '配達日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (444, 'dtb_order', 'conveni_data', 'コンビニ決済情報');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (445, 'dtb_order', 'cell01', '携帯電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (446, 'dtb_order', 'cell02', '携帯電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (447, 'dtb_order', 'cell03', '携帯電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (448, 'dtb_order_temp', 'order_temp_id', '注文一時ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (449, 'dtb_order_temp', 'customer_id', '顧客ID(非会員の場合は、0)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (450, 'dtb_order_temp', 'message', '要望等');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (451, 'dtb_order_temp', 'order_name01', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (452, 'dtb_order_temp', 'order_name02', '顧客名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (453, 'dtb_order_temp', 'order_kana01', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (454, 'dtb_order_temp', 'order_kana02', '顧客名カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (455, 'dtb_order_temp', 'order_email', '受注時のメールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (456, 'dtb_order_temp', 'order_tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (457, 'dtb_order_temp', 'order_tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (458, 'dtb_order_temp', 'order_tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (459, 'dtb_order_temp', 'order_fax01', 'FAX1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (460, 'dtb_order_temp', 'order_fax02', 'FAX2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (461, 'dtb_order_temp', 'order_fax03', 'FAX3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (462, 'dtb_order_temp', 'order_zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (463, 'dtb_order_temp', 'order_zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (464, 'dtb_order_temp', 'order_pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (465, 'dtb_order_temp', 'order_addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (466, 'dtb_order_temp', 'order_addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (467, 'dtb_order_temp', 'order_sex', '性別(1:男性、2:女性)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (468, 'dtb_order_temp', 'order_birth', '生年月日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (469, 'dtb_order_temp', 'order_job', '職種');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (470, 'dtb_order_temp', 'deliv_name01', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (471, 'dtb_order_temp', 'deliv_name02', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (472, 'dtb_order_temp', 'deliv_kana01', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (473, 'dtb_order_temp', 'deliv_kana02', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (474, 'dtb_order_temp', 'deliv_tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (475, 'dtb_order_temp', 'deliv_tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (476, 'dtb_order_temp', 'deliv_tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (477, 'dtb_order_temp', 'deliv_fax01', 'FAX1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (478, 'dtb_order_temp', 'deliv_fax02', 'FAX2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (479, 'dtb_order_temp', 'deliv_fax03', 'FAX3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (480, 'dtb_order_temp', 'deliv_zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (481, 'dtb_order_temp', 'deliv_zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (482, 'dtb_order_temp', 'deliv_pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (483, 'dtb_order_temp', 'deliv_addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (484, 'dtb_order_temp', 'deliv_addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (485, 'dtb_order_temp', 'subtotal', '小計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (486, 'dtb_order_temp', 'discount', '値引き');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (487, 'dtb_order_temp', 'deliv_fee', '送料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (488, 'dtb_order_temp', 'charge', '手数料');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (489, 'dtb_order_temp', 'use_point', '使用ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (490, 'dtb_order_temp', 'add_point', '加算ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (491, 'dtb_order_temp', 'birth_point', 'お誕生日ポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (492, 'dtb_order_temp', 'tax', '税金');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (493, 'dtb_order_temp', 'total', '合計');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (494, 'dtb_order_temp', 'payment_total', 'お支払い合計(ポイント差し引き合計)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (495, 'dtb_order_temp', 'payment_id', '支払い方法ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (496, 'dtb_order_temp', 'payment_method', '支払い方法（文字列）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (497, 'dtb_order_temp', 'deliv_id', '配送業者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (498, 'dtb_order_temp', 'deliv_time_id', '配送時間ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (499, 'dtb_order_temp', 'deliv_time', '配送時間（文字列）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (500, 'dtb_order_temp', 'deliv_no', '配送伝票番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (501, 'dtb_order_temp', 'note', 'SHOPメモ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (502, 'dtb_order_temp', 'mail_flag', 'メルマガ希望');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (503, 'dtb_order_temp', 'status', '1:対応中 2:キャンセル 3:取り寄せ中 4:発送済み');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (504, 'dtb_order_temp', 'deliv_check', '1:別のお届け先を指定している場合');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (505, 'dtb_order_temp', 'point_check', '1:ポイントを使用する。2:ポイントを使用しない。');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (506, 'dtb_order_temp', 'loan_result', 'ローン受付結果');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (507, 'dtb_order_temp', 'credit_result', 'クレジット受付結果');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (508, 'dtb_order_temp', 'credit_msg', 'クレジット受付メッセージ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (509, 'dtb_order_temp', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (510, 'dtb_order_temp', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (511, 'dtb_order_temp', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (512, 'dtb_order_temp', 'deliv_date', '配達日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (513, 'dtb_order_temp', 'conveni_data', 'コンビニ決済情報');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (514, 'dtb_order_temp', 'cell01', '携帯電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (515, 'dtb_order_temp', 'cell02', '携帯電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (516, 'dtb_order_temp', 'cell03', '携帯電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (517, 'dtb_other_deliv', 'other_deliv_id', '別のお届け先ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (518, 'dtb_other_deliv', 'customer_id', '顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (519, 'dtb_other_deliv', 'name01', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (520, 'dtb_other_deliv', 'name02', '配送先名前');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (521, 'dtb_other_deliv', 'kana01', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (522, 'dtb_other_deliv', 'kana02', '配送先カナ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (523, 'dtb_other_deliv', 'zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (524, 'dtb_other_deliv', 'zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (525, 'dtb_other_deliv', 'pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (526, 'dtb_other_deliv', 'addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (527, 'dtb_other_deliv', 'addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (528, 'dtb_other_deliv', 'tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (529, 'dtb_other_deliv', 'tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (530, 'dtb_other_deliv', 'tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (531, 'dtb_order_detail', 'order_id', '注文ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (532, 'dtb_order_detail', 'product_id', '商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (533, 'dtb_order_detail', 'classcategory_id1', '規格分類ID1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (534, 'dtb_order_detail', 'classcategory_id2', '規格分類ID2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (535, 'dtb_order_detail', 'product_name', '商品名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (536, 'dtb_order_detail', 'product_code', '商品コード');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (537, 'dtb_order_detail', 'classcategory_name1', '規格名1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (538, 'dtb_order_detail', 'classcategory_name2', '規格名2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (539, 'dtb_order_detail', 'price', '価格');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (540, 'dtb_order_detail', 'quantity', '個数');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (541, 'dtb_order_detail', 'point_rate', 'ポイント付与率');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (542, 'mtb_pref', 'pref_id', '都道府県ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (543, 'mtb_pref', 'pref_name', '都道府県名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (544, 'mtb_pref', 'rank', '表示順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (545, 'dtb_member', 'member_id', '管理者ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (546, 'dtb_member', 'name', '管理者名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (547, 'dtb_member', 'department', '部門');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (548, 'dtb_member', 'login_id', 'ログインID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (549, 'dtb_member', 'password', 'パスワード');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (550, 'dtb_member', 'authority', '権限');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (551, 'dtb_member', 'rank', '順位');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (552, 'dtb_member', 'work', '稼働:1、非稼働:2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (553, 'dtb_member', 'del_flg', '削除フラグ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (554, 'dtb_member', 'creator_id', '作成者');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (555, 'dtb_member', 'update_date', '更新日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (556, 'dtb_member', 'create_date', '作成日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (557, 'dtb_member', 'login_date', 'ログイン日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (558, 'dtb_question', 'question_id', '質問ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (559, 'dtb_question', 'question_name', '質問名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (560, 'dtb_question', 'question', '質問内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (561, 'dtb_question', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (562, 'dtb_question', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (563, 'dtb_question_result', 'result_id', '結果ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (564, 'dtb_question_result', 'question_id', '質問ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (565, 'dtb_question_result', 'question_date', '質問日');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (566, 'dtb_question_result', 'question_name', '質問名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (567, 'dtb_question_result', 'name01', '回答者名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (568, 'dtb_question_result', 'name02', '回答者名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (569, 'dtb_question_result', 'kana01', '回答者名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (570, 'dtb_question_result', 'kana02', '回答者名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (571, 'dtb_question_result', 'zip01', '郵便番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (572, 'dtb_question_result', 'zip02', '郵便番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (573, 'dtb_question_result', 'pref', '都道府県');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (574, 'dtb_question_result', 'addr01', '住所1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (575, 'dtb_question_result', 'addr02', '住所2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (576, 'dtb_question_result', 'tel01', '電話番号1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (577, 'dtb_question_result', 'tel02', '電話番号2');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (578, 'dtb_question_result', 'tel03', '電話番号3');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (579, 'dtb_question_result', 'mail01', 'メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (580, 'dtb_question_result', 'question01', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (581, 'dtb_question_result', 'question02', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (582, 'dtb_question_result', 'question03', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (583, 'dtb_question_result', 'question04', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (584, 'dtb_question_result', 'question05', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (585, 'dtb_question_result', 'question06', '回答内容');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (586, 'dtb_question_result', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (587, 'dtb_question_result', 'del_flg ', '0:既定、1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (588, 'dtb_bat_relate_products', 'product_id', 'この商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (589, 'dtb_bat_relate_products', 'relate_product_id', 'こんな商品ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (590, 'dtb_bat_relate_products', 'customer_id', '顧客ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (591, 'dtb_bat_relate_products', 'create_date', '集計日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (592, 'dtb_campaign', 'campaign_id', 'キャンペーンID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (593, 'dtb_campaign', 'campaign_name', 'キャンペーン名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (594, 'dtb_campaign', 'campaign_point_rate', 'キャンペーンポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (595, 'dtb_campaign', 'campaign_point_type', 'キャンペーンタイプ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (596, 'dtb_campaign', 'start_date', 'キャンペーン開始期間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (597, 'dtb_campaign', 'end_date', 'キャンペーン終了期間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (598, 'dtb_campaign', 'search_condition', '検索条件');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (599, 'dtb_campaign', 'del_flg', '0:既定　1:削除');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (600, 'dtb_campaign', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (601, 'dtb_campaign', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (602, 'dtb_campaign_detail', 'campaign_id', 'キャンペーンID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (603, 'dtb_campaign_detail', 'product_id', 'キャンペーン名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (604, 'dtb_campaign_detail', 'campaign_point_rate', 'キャンペーンポイント');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (605, 'dtb_pagelayout', 'page_id', 'ページID 0はプレビュー用データ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (606, 'dtb_pagelayout', 'page_name', 'ページ名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (607, 'dtb_pagelayout', 'url', 'ページURL PKEY1');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (608, 'dtb_pagelayout', 'php_dir', 'PHPファイル保存先');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (609, 'dtb_pagelayout', 'tpl_dir', 'テンプレートファイル保存先');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (610, 'dtb_pagelayout', 'filename', 'ファイル名（拡張子なし）');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (611, 'dtb_pagelayout', 'header_chk', 'ヘッダー使用チェック　1：使用、2：未使用');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (612, 'dtb_pagelayout', 'footer_chk', 'フッター使用チェック　1：使用、2：未使用');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (613, 'dtb_pagelayout', 'edit_flg', '削除、ページ名称、URL等を編集可能か否かのFLG　1：編集可、2：編集不可');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (614, 'dtb_pagelayout', 'author', 'メタタグ(SEO管理用)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (615, 'dtb_pagelayout', 'description', 'メタタグ(SEO管理用)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (616, 'dtb_pagelayout', 'keyword', 'メタタグ(SEO管理用)');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (617, 'dtb_pagelayout', 'update_url', 'このデータに対して更新を行ったページ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (618, 'dtb_pagelayout', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (619, 'dtb_pagelayout', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (620, 'dtb_bloc', 'bloc_id', 'ブロックID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (621, 'dtb_bloc', 'bloc_name', 'ブロック名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (622, 'dtb_bloc', 'tpl_path', 'テンプレートファイルのパス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (623, 'dtb_bloc', 'filename', 'ファイル名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (624, 'dtb_bloc', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (625, 'dtb_bloc', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (626, 'dtb_bloc', 'php_path', 'include PHPで使用する場合にはphpのパスも保存する');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (627, 'dtb_blocposition', 'page_id', 'ページID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (628, 'dtb_blocposition', 'target_id', 'ターゲットID 1：レフトナビ 2：ライトナビ 3：イン画面上部 4：メイン画面下部');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (629, 'dtb_blocposition', 'bloc_id', 'ブロックID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (630, 'dtb_blocposition', 'bloc_row', 'ブロックIDを配置する順番');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (631, 'dtb_csv', 'no', '連番');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (632, 'dtb_csv', 'csv_id', 'CSVID　1.商品マスタ、2.顧客マスタ、3.受注');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (633, 'dtb_csv', 'col', '出力カラム');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (634, 'dtb_csv', 'disp_name', '出力名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (635, 'dtb_csv', 'rank', '出力順序');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (636, 'dtb_csv', 'status', '出力有無 1.出力、2.未出力');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (637, 'dtb_csv', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (638, 'dtb_csv', 'update_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (639, 'dtb_csv_sql', 'sql_id', 'SQL番号');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (640, 'dtb_csv_sql', 'name', '名称');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (641, 'dtb_csv_sql', 'sql', 'SQL文');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (642, 'dtb_csv_sql', 'update_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (643, 'dtb_csv_sql', 'create_date', '更新日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (644, 'dtb_mobile_ext_session_id', NULL, 'セッションID管理');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (645, 'dtb_mobile_ext_session_id', 'session_id', 'セッションID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (646, 'dtb_mobile_ext_session_id', 'param_key', 'パラメータ名');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (647, 'dtb_mobile_ext_session_id', 'param_value', 'パラメータ値');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (648, 'dtb_mobile_ext_session_id', 'url', 'URL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (649, 'dtb_mobile_ext_session_id', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (650, 'dtb_mobile_kara_mail', NULL, '空メール管理');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (651, 'dtb_mobile_kara_mail', 'kara_mail_id', '空メール管理ID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (652, 'dtb_mobile_kara_mail', 'session_id', 'セッションID');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (653, 'dtb_mobile_kara_mail', 'token', 'トークン');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (654, 'dtb_mobile_kara_mail', 'next_url', '次ページURL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (655, 'dtb_mobile_kara_mail', 'create_date', '作成日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (656, 'dtb_mobile_kara_mail', 'email', 'メールアドレス');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (657, 'dtb_mobile_kara_mail', 'receive_date', '受信日時');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (658, 'dtb_baseinfo', NULL, '基本情報');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (659, 'dtb_deliv', NULL, '配送業者');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (660, 'dtb_delivtime', NULL, '配送時間');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (661, 'dtb_delivfee', NULL, '配送料金');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (662, 'dtb_payment', NULL, '支払方法');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (663, 'dtb_mailtemplate', NULL, 'ﾒｰﾙﾃﾝﾌﾟﾚｰﾄ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (664, 'dtb_mailmaga_template', NULL, 'ﾒﾙﾏｶﾞﾃﾝﾌﾟﾚｰﾄ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (665, 'dtb_send_history', NULL, '配信履歴');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (666, 'dtb_send_customer', NULL, 'ﾏｶﾞｼﾞﾝ配信対象者');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (667, 'dtb_products', NULL, '商品');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (668, 'dtb_products_class', NULL, '商品規格');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (669, 'dtb_class', NULL, '規格');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (670, 'dtb_classcategory', NULL, '規格分類');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (671, 'dtb_category', NULL, 'ｶﾃｺﾞﾘ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (672, 'dtb_bat_order_daily', NULL, '日次注文情報');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (673, 'dtb_bat_order_daily_age', NULL, '日次注文情報年齢別');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (674, 'dtb_bat_order_daily_hour', NULL, '日次注文情報時間別');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (675, 'dtb_recommend_products', NULL, 'ﾘｺﾒﾝﾄﾞ商品');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (676, 'dtb_review', NULL, 'ﾚﾋﾞｭｰ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (677, 'dtb_customer_reading', NULL, '閲覧履歴');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (678, 'dtb_category_count', NULL, 'ｶﾃｺﾞﾘｶｳﾝﾄ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (679, 'dtb_category_total_count', NULL, 'ｶﾃｺﾞﾘﾄｰﾀﾙｶｳﾝﾄ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (680, 'dtb_news', NULL, '新着情報');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (681, 'dtb_best_products', NULL, 'ベスト商品');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (682, 'dtb_mail_history', NULL, '受注ﾒｰﾙ送信履歴');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (683, 'dtb_customer', NULL, '顧客');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (684, 'dtb_customer_mail', NULL, '顧客ﾒｰﾙ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (685, 'dtb_customer_mail_temp', NULL, 'ﾒﾙﾏｶﾞ_Temp');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (686, 'dtb_order', NULL, '受注');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (687, 'dtb_order_temp', NULL, '受注_Temp');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (688, 'dtb_other_deliv', NULL, '別のお届け先');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (689, 'dtb_order_detail', NULL, '受注商品詳細');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (690, 'mtb_pref', NULL, '都道府県ﾏｽﾀ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (691, 'dtb_member', NULL, '管理者');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (692, 'dtb_question', NULL, '質問');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (693, 'dtb_question_result', NULL, '質問結果');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (694, 'dtb_bat_relate_products', NULL, 'こんな商品も買っています');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (695, 'dtb_campaign', NULL, 'キャンペーン');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (696, 'dtb_campaign_detail', NULL, 'キャンペーン詳細');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (697, 'dtb_pagelayout', NULL, 'ページレイアウト');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (698, 'dtb_bloc', NULL, 'ブロック');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (699, 'dtb_blocposition', NULL, 'ブロック配置');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (700, 'dtb_csv', NULL, 'CSV出力設定');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (701, 'dtb_csv_sql', NULL, 'CSV_SQL');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (702, 'dtb_update', NULL, 'アップデート');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (703, 'dtb_user_regist', NULL, 'ユーザー登録');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (704, 'dtb_kiyaku', NULL, '会員規約');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (705, 'mtb_zip', NULL, '郵便番号マスタ');
INSERT INTO dtb_table_comment (id, table_name, column_name, description) VALUES (706, 'dtb_templates', NULL, 'テンプレート');


--
-- Data for Name: dtb_templates; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO dtb_templates (template_code, template_name, create_date, update_date) VALUES ('default1', 'デフォルト1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


--
-- Data for Name: dtb_trackback; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_update; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: dtb_user_regist; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: mtb_allowed_tag; Type: TABLE DATA; Schema: public; Owner: nanasess
--

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


--
-- Data for Name: mtb_authority; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_authority (id, name, rank) VALUES (0, '管理者', 0);


--
-- Data for Name: mtb_class; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_class (id, name, rank) VALUES (1, '規格無し', 0);
INSERT INTO mtb_class (id, name, rank) VALUES (2, '規格有り', 1);


--
-- Data for Name: mtb_constants; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ECCUBE_VERSION', '"1.5"', 0, 'EC-CUBEのバージョン');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SAMPLE_ADDRESS1', '"市区町村名（例：東京都千代田区神田神保町）"', 1, 'フロント表示関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SAMPLE_ADDRESS2', '"番地・ビル名（例：1-3-5）"', 2, 'フロント表示関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_DIR', '"user_data/"', 3, 'ユーザファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_PATH', 'HTML_PATH . USER_DIR', 4, 'ユーザファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_INC_PATH', 'USER_PATH . "include/"', 5, 'ユーザインクルードファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BLOC_DIR', '"include/bloc/"', 6, 'ブロックファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BLOC_PATH', 'USER_PATH . BLOC_DIR', 7, 'ブロックファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_DIR', '"cp/"', 8, 'キャンペーンファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_URL', 'URL_DIR . CAMPAIGN_DIR', 9, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_PATH', 'HTML_PATH . CAMPAIGN_DIR', 10, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_TEMPLATE_DIR', '"include/campaign/"', 11, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_TEMPLATE_PATH', 'USER_PATH . CAMPAIGN_TEMPLATE_DIR', 12, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_BLOC_DIR', '"bloc/"', 13, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_BLOC_PATH', 'CAMPAIGN_TEMPLATE_PATH . CAMPAIGN_BLOC_DIR', 14, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_TEMPLATE_ACTIVE', '"active/"', 15, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_TEMPLATE_END', '"end/"', 16, 'キャンペーン関連');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_TEMPLATE_DIR', '"templates/"', 17, 'テンプレートファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_TEMPLATE_PATH', 'USER_PATH . USER_TEMPLATE_DIR', 18, 'テンプレートファイル保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_TEMP_DIR', 'HTML_PATH . "upload/temp_template/"', 19, 'テンプレートファイル一時保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('USER_DEF_PHP', 'HTML_PATH . "__default.php"', 20, 'ユーザー作成画面のデフォルトPHPファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEF_LAYOUT', '"products/list.php"', 21, 'その他画面のデフォルトページレイアウト');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_DIR', '"downloads/module/"', 22, 'ダウンロードモジュール保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_PATH', 'DATA_PATH . MODULE_DIR', 23, 'ダウンロードモジュール保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_DIR', '"downloads/update/"', 24, 'HotFix保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_PATH', 'DATA_PATH . UPDATE_DIR', 25, 'HotFix保存ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CLASS_PATH', 'DATA_PATH . "class/"', 26, 'クラスパス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MASTER_DATA_DIR', 'DATA_PATH . "conf/cache/"', 27, 'マスタデータキャッシュディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_HTTP', '"http://www.lockon.co.jp/share/"', 28, 'アップデート管理用ファイル格納場所　');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_CSV_LINE_MAX', '4096', 29, 'アップデート管理用CSV1行辺りの最大文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UPDATE_CSV_COL_MAX', '13', 30, 'アップデート管理用CSVカラム数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MODULE_CSV_COL_MAX', '16', 31, 'モジュール管理用CSVカラム数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('EBIS_TAG_MID', '1', 32, 'エビスタグ機能のモジュールID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AFF_TAG_MID', '3', 33, 'アフィリエイトタグ機能のモジュールID');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AFF_SHOPPING_COMPLETE', '1', 34, '商品購入完了');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AFF_ENTRY_COMPLETE', '2', 35, 'ユーザ登録完了');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_HTTP_DOMAIN', '"http://rcv.ec-cube.net/"', 36, '決済受信用URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_HTTP_ANALYZE_PROGRAM', '"rcv_credit.php"', 37, '決済受信用URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_HTTP_ANALYZE_URL', 'CREDIT_HTTP_DOMAIN . CREDIT_HTTP_ANALYZE_PROGRAM', 38, '決済受信用URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CHAR_CODE', '"UTF-8"', 39, '文字コード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ECCUBE_PAYMENT', '"EC-CUBE"', 41, '決済モジュール付与文言');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PEAR_DB_DEBUG', '9', 42, 'PEAR::DBのデバッグモード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOAD_BATCH_PASS', '3600', 43, 'バッチを実行する最短の間隔(秒)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CLOSE_DAY', '31', 44, '締め日の指定(末日の場合は、31を指定してください。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FAVORITE_ERROR', '13', 45, '一般サイトエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LIB_DIR', 'DATA_PATH . "lib/"', 46, 'ライブラリのパス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TTF_DIR', 'DATA_PATH . "fonts/"', 47, 'フォントのパス');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_DIR', 'HTML_PATH . "upload/graph_image/"', 48, 'グラフ格納ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_URL', 'URL_DIR . "upload/graph_image/"', 49, 'グラフURL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_PIE_MAX', '10', 50, '円グラフ最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('GRAPH_LABEL_MAX', '40', 51, 'グラフのラベルの文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PDF_DIR', 'DATA_PATH . "pdf/"', 52, 'PDF格納ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BAT_ORDER_AGE', '70', 53, '何歳まで集計の対象とするか');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCTS_TOTAL_MAX', '15', 54, '商品集計で何位まで表示するか');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_PRODUCT_DISP', '2', 55, '1:公開 2:非公開');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_FREE_AMOUNT', '0', 56, '送料無料購入個数（0の場合は、何個買っても無料にならない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('INPUT_DELIV_FEE', '1', 57, '配送料の設定画面表示(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_PRODUCT_DELIV_FEE', '0', 58, '商品ごとの送料設定(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_DELIV_FEE', '1', 59, '配送業者ごとの配送料を加算する(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_RECOMMEND', '1', 60, 'おすすめ商品登録(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('OPTION_CLASS_REGIST', '1', 61, '商品規格登録(有効:1 無効:0)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TV_IMAGE_WIDTH', '170', 62, 'TV連動商品画像横');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TV_IMAGE_HEIGHT', '95', 63, 'TV連動商品画像縦');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TV_PRODUCTS_MAX', '10', 64, 'TV連動商品最大登録数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEFAULT_PASSWORD', '"UAhgGR3L"', 65, '会員登録変更(マイページ)パスワード用');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RECOMMEND_PRODUCT_MAX', '6', 66, 'おすすめ商品数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_ADDR_MAX', '20', 67, '別のお届け先最大登録数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_READING_MAX', '30', 68, '閲覧履歴保存数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SSLURL_CHECK', '0', 69, 'SSLURL判定');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_STATUS_MAX', '50', 70, '管理画面ステータス一覧表示件数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('REVIEW_REGIST_MAX', '5', 71, 'フロントレビュー書き込み最大数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DEBUG_MODE', '0', 72, 'デバッグモード(true：sfPrintRやDBのエラーメッセージを出力する、false：出力しない)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_ID', '"1"', 73, '管理ユーザID(メンテナンス用表示されない。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_CONFIRM_MAIL', '0', 74, '会員登録時に仮会員確認メールを送信するか（true:仮会員、false:本会員）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MELMAGA_SEND', '1', 75, 'メルマガ配信抑制(false:OFF、true:ON)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MELMAGA_BATCH_MODE', '0', 76, 'メイルマガジンバッチモード(true:バッチで送信する ※要cron設定、false:リアルタイムで送信する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOGIN_FRAME', '"login_frame.tpl"', 77, 'ログイン画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIN_FRAME', '"main_frame.tpl"', 78, '管理画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_FRAME', '"site_frame.tpl"', 79, '一般サイト画面フレーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CERT_STRING', '"7WDhcBTF"', 80, '認証文字列');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DUMMY_PASS', '"########"', 81, 'ダミーパスワード');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('UNLIMITED', '"++"', 82, '在庫数、販売制限無限を示す。');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BIRTH_YEAR', '1901', 83, '生年月日登録開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RELEASE_YEAR', '2005', 84, '本システムの稼働開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_ADD_YEAR', '10', 85, 'クレジットカードの期限＋何年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PARENT_CAT_MAX', '12', 86, '親カテゴリのカテゴリIDの最大数（これ以下は親カテゴリとする。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NUMBER_MAX', '1000000000', 87, 'GET値変更などのいたずらを防ぐため最大数制限を設ける。');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('POINT_RULE', '2', 88, 'ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('POINT_VALUE', '1', 89, '1ポイント当たりの値段(円)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_MODE', '0', 90, '管理モード 1:有効　0:無効(納品時)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DAILY_BATCH_MODE', '0', 91, '売上集計バッチモード(true:バッチで集計する ※要cron設定、false:リアルタイムで集計する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAX_LOG_QUANTITY', '5', 92, 'ログファイル最大数(ログテーション)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAX_LOG_SIZE', '"1000000"', 93, '1つのログファイルに保存する最大容量(byte)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRANSACTION_ID_NAME', '"transactionid"', 94, 'トランザクションID の名前');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FORGOT_MAIL', '0', 95, 'パスワード忘れの確認メールを送付するか否か。(0:送信しない、1:送信する)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('HTML_TEMPLATE_SUB_MAX', '12', 96, '登録できるサブ商品の数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LINE_LIMIT_SIZE', '60', 97, '文字数が多すぎるときに強制改行するサイズ(半角)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BIRTH_MONTH_POINT', '0', 98, '誕生日月ポイント');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CGI_DIR', 'HTML_PATH . "../cgi-bin/"', 99, 'クレジットカード(ベリトランス) モジュール格納ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CGI_FILE', '"mauthonly.cgi"', 100, 'コアCGI');
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
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_TITLE', '"ECサイト管理ページ"', 134, '管理ページタイトル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SELECT_RGB', '"#ffffdf"', 135, '編集時強調表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DISABLED_RGB', '"#C9C9C9"', 136, '入力項目無効時の表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ERR_COLOR', '"#ffe8e8"', 137, 'エラー時表示色');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CATEGORY_HEAD', '">"', 138, '親カテゴリ表示文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('START_BIRTH_YEAR', '1901', 139, '生年月日選択開始年');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NORMAL_PRICE_TITLE', '"通常価格"', 140, '価格名称');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SALE_PRICE_TITLE', '"販売価格"', 141, '価格名称');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOG_PATH', 'DATA_PATH . "logs/site.log"', 142, 'ログファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_LOG_PATH', 'DATA_PATH . "logs/customer.log"', 143, '会員ログイン ログファイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_ADMIN_DIR', 'DATA_PATH . "Smarty/templates/admin"', 144, 'SMARTYテンプレート');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_DIR', 'DATA_PATH . "Smarty/templates"', 145, 'SMARTYテンプレート');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_ADMIN_DIR', 'DATA_PATH . "Smarty/templates_c/admin"', 146, 'SMARTYコンパイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_DIR', 'DATA_PATH . "Smarty/templates_c"', 147, 'SMARTYコンパイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMPLATE_FTP_DIR', 'USER_PATH . "templates/"', 148, 'SMARTYテンプレート(FTP許可)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COMPILE_FTP_DIR', 'DATA_PATH . "Smarty/templates_c/user_data/"', 149, 'SMARTYコンパイル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_DIR', 'HTML_PATH . "upload/temp_image/"', 150, '画像一時保存');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_DIR', 'HTML_PATH . "upload/save_image/"', 151, '画像保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_URL', 'URL_DIR . "upload/temp_image/"', 152, '画像一時保存URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_URL', 'URL_DIR . "upload/save_image/"', 153, '画像保存先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_TEMP_URL_RSS', 'SITE_URL . "upload/temp_image/"', 154, 'RSS用画像一時保存URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('IMAGE_SAVE_URL_RSS', 'SITE_URL . "upload/save_image/"', 155, 'RSS用画像保存先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CSV_TEMP_DIR', 'HTML_PATH . "upload/csv/"', 156, 'エンコードCSVの一時保存先');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NO_IMAGE_URL', 'URL_DIR . "misc/blank.gif"', 157, '画像がない場合に表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NO_IMAGE_DIR', 'HTML_PATH . "misc/blank.gif"', 158, '画像がない場合に表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SYSTEM_TOP', 'URL_DIR . "admin/system/index.php"', 159, 'システム管理トップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CLASS_REGIST', 'URL_DIR . "admin/products/class.php"', 160, '規格登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_INPUT_ZIP', 'URL_DIR . "input_zip.php"', 161, '郵便番号入力');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_DELIVERY_TOP', 'URL_DIR . "admin/basis/delivery.php"', 162, '配送業者登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_PAYMENT_TOP', 'URL_DIR . "admin/basis/payment.php"', 163, '支払い方法登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CONTROL_TOP', 'URL_DIR . "admin/basis/control.php"', 164, 'サイト管理情報登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_HOME', 'URL_DIR . "admin/home.php"', 165, 'ホーム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LOGIN', 'URL_DIR . "admin/index.php"', 166, 'ログインページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SEARCH_TOP', 'URL_DIR . "admin/products/index.php"', 167, '商品検索ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ORDER_EDIT', 'URL_DIR . "admin/order/edit.php"', 168, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SEARCH_ORDER', 'URL_DIR . "admin/order/index.php"', 169, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ORDER_MAIL', 'URL_DIR . "admin/order/mail.php"', 170, '注文編集ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LOGOUT', 'URL_DIR . "admin/logout.php"', 171, 'ログアウトページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SYSTEM_CSV', 'URL_DIR . "admin/system/member_csv.php"', 172, 'システム管理CSV出力ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ADMIN_CSS', 'URL_DIR . "admin/css/"', 173, '管理ページ用CSS保管ディレクトリ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CAMPAIGN_TOP', 'URL_DIR . "admin/contents/campaign.php"', 174, 'キャンペーン登録ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CAMPAIGN_DESIGN', 'URL_DIR . "admin/contents/campaign_design.php"', 175, 'キャンペーンデザイン設定ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SUCCESS', '0', 176, 'アクセス成功');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LOGIN_ERROR', '1', 177, 'ログイン失敗');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ACCESS_ERROR', '2', 178, 'アクセス失敗（タイムアウト等）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AUTH_ERROR', '3', 179, 'アクセス権限違反');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('INVALID_MOVE_ERRORR', '4', 180, '不正な遷移エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCTS_LIST_MAX', '15', 181, '商品一覧表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MEMBER_PMAX', '10', 182, 'メンバー管理ページ表示行数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEARCH_PMAX', '10', 183, '検索ページ表示行数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('NAVI_PMAX', '5', 184, 'ページ番号の最大表示個数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCTSUB_MAX', '5', 185, '商品サブ情報最大数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIVTIME_MAX', '16', 186, '配送時間の最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIVFEE_MAX', '47', 187, '配送料金の最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('STEXT_LEN', '50', 188, '短い項目の文字数（名前など)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SMTEXT_LEN', '100', 189, '');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MTEXT_LEN', '200', 190, '長い項目の文字数（住所など）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MLTEXT_LEN', '1000', 191, '長中文の文字数（問い合わせなど）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LTEXT_LEN', '3000', 192, '長文の文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LLTEXT_LEN', '99999', 193, '超長文の文字数（メルマガなど）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_LEN', '300', 194, 'URLの文字長');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ID_MAX_LEN', '15', 195, 'ID・パスワードの文字数制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ID_MIN_LEN', '4', 196, 'ID・パスワードの文字数制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRICE_LEN', '8', 197, '金額桁数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PERCENTAGE_LEN', '3', 198, '率桁数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('AMOUNT_LEN', '6', 199, '在庫数、販売制限数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ZIP01_LEN', '3', 200, '郵便番号1');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ZIP02_LEN', '4', 201, '郵便番号2');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEL_ITEM_LEN', '6', 202, '電話番号各項目制限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEL_LEN', '12', 203, '電話番号総数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PASSWORD_LEN1', '4', 204, 'パスワード1');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PASSWORD_LEN2', '10', 205, 'パスワード2');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('INT_LEN', '8', 206, '検査数値用桁数(INT)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREDIT_NO_LEN', '4', 207, 'クレジットカードの文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEARCH_CATEGORY_LEN', '18', 208, '検索カテゴリ最大表示文字数(byte)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FILE_NAME_LEN', '10', 209, 'ファイル名表示文字数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SALE_LIMIT_MAX', '10', 210, '購入制限なしの場合の最大購入個数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_TITLE', '"ＥＣ-ＣＵＢＥ  テストサイト"', 211, 'HTMLタイトル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('COOKIE_EXPIRE', '365', 212, 'クッキー保持期限(日)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PRODUCT_NOT_FOUND', '1', 213, '指定商品ページがない');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CART_EMPTY', '2', 214, 'カート内が空');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PAGE_ERROR', '3', 215, 'ページ推移エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CART_ADD_ERROR', '4', 216, '購入処理中のカート商品追加エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CANCEL_PURCHASE', '5', 217, '他にも購入手続きが行われた場合');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CATEGORY_NOT_FOUND', '6', 218, '指定カテゴリページがない');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_LOGIN_ERROR', '7', 219, 'ログインに失敗');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CUSTOMER_ERROR', '8', 220, '会員専用ページへのアクセスエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SOLD_OUT', '9', 221, '購入時の売り切れエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CART_NOT_FOUND', '10', 222, 'カート内商品の読込エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LACK_POINT', '11', 223, 'ポイントの不足');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TEMP_LOGIN_ERROR', '12', 224, '仮登録者がログインに失敗');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ERROR', '13', 225, 'URLエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('EXTRACT_ERROR', '14', 226, 'ファイル解凍エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FTP_DOWNLOAD_ERROR', '15', 227, 'FTPダウンロードエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FTP_LOGIN_ERROR', '16', 228, 'FTPログインエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FTP_CONNECT_ERROR', '17', 229, 'FTP接続エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CREATE_DB_ERROR', '18', 230, 'DB作成エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DB_IMPORT_ERROR', '19', 231, 'DBインポートエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FILE_NOT_FOUND', '20', 232, '設定ファイル存在エラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('WRITE_FILE_ERROR', '21', 233, '書き込みエラー');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('FREE_ERROR_MSG', '999', 234, 'フリーメッセージ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEPA_CATNAVI', '" > "', 235, 'カテゴリ区切り文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SEPA_CATLIST', '" | "', 236, 'カテゴリ区切り文字');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_TOP', 'SSL_URL . "shopping/index.php"', 237, '会員情報入力');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_ENTRY_TOP', 'SSL_URL . "entry/index.php"', 238, '会員登録ページTOP');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SITE_TOP', 'URL_DIR . "index.php"', 239, 'サイトトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_CART_TOP', 'URL_DIR . "cart/index.php"', 240, 'カートトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_DELIV_TOP', 'URL_DIR . "shopping/deliv.php"', 241, '配送時間設定');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_MYPAGE_TOP', 'SSL_URL . "mypage/login.php"', 242, 'Myページトップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CONFIRM', 'URL_DIR . "shopping/confirm.php"', 243, '購入確認ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_PAYMENT', 'URL_DIR . "shopping/payment.php"', 244, 'お支払い方法選択ページ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_COMPLETE', 'URL_DIR . "shopping/complete.php"', 245, '購入完了画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CREDIT', 'URL_DIR . "shopping/card.php"', 246, 'カード決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_LOAN', 'URL_DIR . "shopping/loan.php"', 247, 'ローン決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_CONVENIENCE', 'URL_DIR . "shopping/convenience.php"', 248, 'コンビニ決済画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_SHOP_MODULE', 'URL_DIR . "shopping/load_payment_module.php"', 249, 'モジュール追加用画面');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('URL_PRODUCTS_TOP', 'URL_DIR . "products/top.php"', 250, '商品トップ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LIST_P_HTML', 'URL_DIR . "products/list-p"', 251, '商品一覧(HTML出力)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('LIST_C_HTML', 'URL_DIR . "products/list.php?mode=search&category_id="', 252, '商品一覧(HTML出力)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DETAIL_P_HTML', 'URL_DIR . "products/detail.php?product_id="', 253, '商品詳細(HTML出力)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MYPAGE_DELIVADDR_URL', 'URL_DIR . "mypage/delivery.php"', 254, 'マイページお届け先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIL_TYPE_PC', '1', 255, 'メールアドレス種別');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('MAIL_TYPE_MOBILE', '2', 256, 'メールアドレス種別');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_NEW', '1', 257, '新規注文');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_PAY_WAIT', '2', 258, '入金待ち');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_PRE_END', '6', 259, '入金済み');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_CANCEL', '3', 260, 'キャンセル');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_BACK_ORDER', '4', 261, '取り寄せ中');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ORDER_DELIV', '5', 262, '発送済み');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ODERSTATUS_COMMIT', 'ORDER_DELIV', 263, '受注ステータス変更の際にポイント等を加算するステータス番号（発送済み）');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ADMIN_NEWS_STARTYEAR', '2005', 264, '新着情報管理画面 開始年(西暦) ');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_CUSTOMER_TEMP_SUBJECT', '"会員仮登録が完了いたしました。"', 265, '会員登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_CUSTOMER_REGIST_SUBJECT', '"本会員登録が完了いたしました。"', 266, '会員登録');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('ENTRY_LIMIT_HOUR', '1', 267, '再入会制限時間（単位: 時間)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RECOMMEND_NUM', '8', 268, 'オススメ商品表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BEST_MAX', '5', 269, 'ベスト商品の最大登録数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('BEST_MIN', '3', 270, 'ベスト商品の最小登録数（登録数が満たない場合は表示しない。)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('DELIV_DATE_END_MAX', '21', 271, '配達可能な日付以降のプルダウン表示最大日数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('PURCHASE_CUSTOMER_REGIST', '0', 272, '購入時強制会員登録(1:有効　0:無効)');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('RELATED_PRODUCTS_MAX', '3', 273, 'この商品を買った人はこんな商品も買っています　表示件数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CV_PAYMENT_LIMIT', '14', 274, '支払期限');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('CAMPAIGN_REGIST_MAX', '20', 275, 'キャンペーン登録最大数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('REVIEW_ALLOW_URL', '0', 276, '商品レビューでURL書き込みを許可するか否か');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_VIEW', '1', 277, 'トラックバック 表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_NOT_VIEW', '2', 278, 'トラックバック 非表示');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_STATUS_SPAM', '3', 279, 'トラックバック スパム');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_VIEW_MAX', '10', 280, 'フロント最大表示数');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('TRACKBACK_TO_URL', 'SITE_URL . "tb/index.php?pid="', 281, 'トラックバック先URL');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_CONTROL_TRACKBACK', '1', 282, 'サイト管理 トラックバック');
INSERT INTO mtb_constants (id, name, rank, remarks) VALUES ('SITE_CONTROL_AFFILIATE', '2', 283, 'サイト管理 アフィリエイト');


--
-- Data for Name: mtb_conveni_message; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_conveni_message (id, name, rank) VALUES (1, '上記URLから振込票を印刷、もしくは振込票番号を紙に控えて、全国のセブンイレブンにてお支払いください。', 0);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (2, '企業コード、受付番号を紙などに控えて、全国のファミリーマートにお支払いください。', 1);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (3, '上記URLから振込票を印刷、もしくはケータイ決済番号を紙などに控えて、全国のサークルKサンクスにてお支払ください。', 2);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (4, '振込票番号を紙に控えて、全国のローソンまたはセイコーマートにてお支払いください。', 3);
INSERT INTO mtb_conveni_message (id, name, rank) VALUES (5, '上記URLから振込票を印刷し、全国のミニストップ・デイリーヤマザキ・ヤマザキデイリーストアにてお支払いください。', 4);


--
-- Data for Name: mtb_convenience; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_convenience (id, name, rank) VALUES (1, 'セブンイレブン', 0);
INSERT INTO mtb_convenience (id, name, rank) VALUES (2, 'ファミリーマート', 1);
INSERT INTO mtb_convenience (id, name, rank) VALUES (3, 'サークルKサンクス', 2);
INSERT INTO mtb_convenience (id, name, rank) VALUES (4, 'ローソン・セイコーマート', 3);
INSERT INTO mtb_convenience (id, name, rank) VALUES (5, 'ミニストップ・デイリーヤマザキ・ヤマザキデイリーストア', 4);


--
-- Data for Name: mtb_db; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_db (id, name, rank) VALUES (1, 'PostgreSQL', 0);
INSERT INTO mtb_db (id, name, rank) VALUES (2, 'MySQL', 1);


--
-- Data for Name: mtb_delivery_date; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_delivery_date (id, name, rank) VALUES (1, '即日', 0);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (2, '1〜2日後', 1);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (3, '3〜4日後', 2);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (4, '1週間以降', 3);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (5, '2週間以降', 4);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (6, '3週間以降', 5);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (7, '1ヶ月以降', 6);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (8, '2ヶ月以降', 7);
INSERT INTO mtb_delivery_date (id, name, rank) VALUES (9, 'お取り寄せ(商品入荷後)', 8);


--
-- Data for Name: mtb_disable_logout; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_disable_logout (id, name, rank) VALUES (1, '/shopping/deliv.php', 0);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (2, '/shopping/payment.php', 1);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (3, '/shopping/confirm.php', 2);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (4, '/shopping/card.php', 3);
INSERT INTO mtb_disable_logout (id, name, rank) VALUES (5, '/shopping/loan.php', 4);


--
-- Data for Name: mtb_disp; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_disp (id, name, rank) VALUES (1, '公開', 0);
INSERT INTO mtb_disp (id, name, rank) VALUES (2, '非公開', 1);


--
-- Data for Name: mtb_job; Type: TABLE DATA; Schema: public; Owner: nanasess
--

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


--
-- Data for Name: mtb_magazine_type; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_magazine_type (id, name, rank) VALUES (1, 'HTML', 0);
INSERT INTO mtb_magazine_type (id, name, rank) VALUES (2, 'テキスト', 1);
INSERT INTO mtb_magazine_type (id, name, rank) VALUES (3, 'HTMLテンプレート', 2);


--
-- Data for Name: mtb_mail_magazine_type; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (1, 'HTMLメール', 0);
INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (2, 'テキストメール', 1);
INSERT INTO mtb_mail_magazine_type (id, name, rank) VALUES (3, '希望しない', 2);


--
-- Data for Name: mtb_mail_template; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_mail_template (id, name, rank) VALUES (1, '注文受付メール', 0);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (2, '注文キャンセル受付メール', 1);
INSERT INTO mtb_mail_template (id, name, rank) VALUES (3, '取り寄せ確認メール', 2);


--
-- Data for Name: mtb_mail_tpl_path; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (1, 'mail_templates/order_mail.tpl', 0);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (2, 'mail_templates/order_mail.tpl', 1);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (3, 'mail_templates/order_mail.tpl', 2);
INSERT INTO mtb_mail_tpl_path (id, name, rank) VALUES (4, 'mail_templates/contact_mail.tpl', 3);


--
-- Data for Name: mtb_mail_type; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_mail_type (id, name, rank) VALUES (1, 'パソコン用アドレス', 0);
INSERT INTO mtb_mail_type (id, name, rank) VALUES (2, '携帯用アドレス', 1);


--
-- Data for Name: mtb_order_status; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_order_status (id, name, rank) VALUES (1, '新規受付', 0);
INSERT INTO mtb_order_status (id, name, rank) VALUES (2, '入金待ち', 1);
INSERT INTO mtb_order_status (id, name, rank) VALUES (6, '入金済み', 2);
INSERT INTO mtb_order_status (id, name, rank) VALUES (3, 'キャンセル', 3);
INSERT INTO mtb_order_status (id, name, rank) VALUES (4, '取り寄せ中', 4);
INSERT INTO mtb_order_status (id, name, rank) VALUES (5, '発送済み', 5);


--
-- Data for Name: mtb_order_status_color; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_order_status_color (id, name, rank) VALUES (1, '新規受付', 0);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (2, '入金待ち', 1);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (6, '入金済み', 2);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (3, 'キャンセル', 3);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (4, '取り寄せ中', 4);
INSERT INTO mtb_order_status_color (id, name, rank) VALUES (5, '発送済み', 5);


--
-- Data for Name: mtb_page_max; Type: TABLE DATA; Schema: public; Owner: nanasess
--

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


--
-- Data for Name: mtb_page_rows; Type: TABLE DATA; Schema: public; Owner: nanasess
--

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


--
-- Data for Name: mtb_permission; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: mtb_pref; Type: TABLE DATA; Schema: public; Owner: nanasess
--

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


--
-- Data for Name: mtb_product_list_max; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_product_list_max (id, name, rank) VALUES (15, '15件', 0);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (30, '30件', 1);
INSERT INTO mtb_product_list_max (id, name, rank) VALUES (50, '50件', 2);


--
-- Data for Name: mtb_product_status_color; Type: TABLE DATA; Schema: public; Owner: nanasess
--



--
-- Data for Name: mtb_recommend; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_recommend (id, name, rank) VALUES (5, '★★★★★', 0);
INSERT INTO mtb_recommend (id, name, rank) VALUES (4, '★★★★', 1);
INSERT INTO mtb_recommend (id, name, rank) VALUES (3, '★★★', 2);
INSERT INTO mtb_recommend (id, name, rank) VALUES (2, '★★', 3);
INSERT INTO mtb_recommend (id, name, rank) VALUES (1, '★', 4);


--
-- Data for Name: mtb_reminder; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_reminder (id, name, rank) VALUES (1, '母親の旧姓は？', 0);
INSERT INTO mtb_reminder (id, name, rank) VALUES (2, 'お気に入りのマンガは？', 1);
INSERT INTO mtb_reminder (id, name, rank) VALUES (3, '大好きなペットの名前は？', 2);
INSERT INTO mtb_reminder (id, name, rank) VALUES (4, '初恋の人の名前は？', 3);
INSERT INTO mtb_reminder (id, name, rank) VALUES (5, '面白かった映画は？', 4);
INSERT INTO mtb_reminder (id, name, rank) VALUES (6, '尊敬していた先生の名前は？', 5);
INSERT INTO mtb_reminder (id, name, rank) VALUES (7, '好きな食べ物は？', 6);


--
-- Data for Name: mtb_review_deny_url; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (0, 'http://', 0);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (1, 'https://', 1);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (2, 'ttp://', 2);
INSERT INTO mtb_review_deny_url (id, name, rank) VALUES (3, 'ttps://', 3);


--
-- Data for Name: mtb_sex; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_sex (id, name, rank) VALUES (1, '男性', 0);
INSERT INTO mtb_sex (id, name, rank) VALUES (2, '女性', 1);


--
-- Data for Name: mtb_site_control_affiliate; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_site_control_affiliate (id, name, rank) VALUES (1, '有効', 0);
INSERT INTO mtb_site_control_affiliate (id, name, rank) VALUES (2, '無効', 1);


--
-- Data for Name: mtb_site_control_track_back; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_site_control_track_back (id, name, rank) VALUES (1, '有効', 0);
INSERT INTO mtb_site_control_track_back (id, name, rank) VALUES (2, '無効', 1);


--
-- Data for Name: mtb_srank; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_srank (id, name, rank) VALUES (1, '1', 0);
INSERT INTO mtb_srank (id, name, rank) VALUES (2, '2', 1);
INSERT INTO mtb_srank (id, name, rank) VALUES (3, '3', 2);
INSERT INTO mtb_srank (id, name, rank) VALUES (4, '4', 3);
INSERT INTO mtb_srank (id, name, rank) VALUES (5, '5', 4);


--
-- Data for Name: mtb_status; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_status (id, name, rank) VALUES (1, 'NEW', 0);
INSERT INTO mtb_status (id, name, rank) VALUES (2, '残りわずか', 1);
INSERT INTO mtb_status (id, name, rank) VALUES (3, 'ポイント２倍', 2);
INSERT INTO mtb_status (id, name, rank) VALUES (4, 'オススメ', 3);
INSERT INTO mtb_status (id, name, rank) VALUES (5, '限定品', 4);


--
-- Data for Name: mtb_status_image; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_status_image (id, name, rank) VALUES (1, '/img/right_product/icon01.gif', 0);
INSERT INTO mtb_status_image (id, name, rank) VALUES (2, '/img/right_product/icon02.gif', 1);
INSERT INTO mtb_status_image (id, name, rank) VALUES (3, '/img/right_product/icon03.gif', 2);
INSERT INTO mtb_status_image (id, name, rank) VALUES (4, '/img/right_product/icon04.gif', 3);
INSERT INTO mtb_status_image (id, name, rank) VALUES (5, '/img/right_product/icon05.gif', 4);


--
-- Data for Name: mtb_target; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_target (id, name, rank) VALUES (1, 'LeftNavi', 0);
INSERT INTO mtb_target (id, name, rank) VALUES (2, 'MainHead', 1);
INSERT INTO mtb_target (id, name, rank) VALUES (3, 'RightNavi', 2);
INSERT INTO mtb_target (id, name, rank) VALUES (4, 'MainFoot', 3);
INSERT INTO mtb_target (id, name, rank) VALUES (5, 'Unused', 4);


--
-- Data for Name: mtb_taxrule; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_taxrule (id, name, rank) VALUES (1, '四捨五入', 0);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (2, '切り捨て', 1);
INSERT INTO mtb_taxrule (id, name, rank) VALUES (3, '切り上げ', 2);


--
-- Data for Name: mtb_track_back_status; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_track_back_status (id, name, rank) VALUES (1, '表示', 0);
INSERT INTO mtb_track_back_status (id, name, rank) VALUES (2, '非表示', 1);
INSERT INTO mtb_track_back_status (id, name, rank) VALUES (3, 'スパム', 2);


--
-- Data for Name: mtb_wday; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_wday (id, name, rank) VALUES (0, '日', 0);
INSERT INTO mtb_wday (id, name, rank) VALUES (1, '月', 1);
INSERT INTO mtb_wday (id, name, rank) VALUES (2, '火', 2);
INSERT INTO mtb_wday (id, name, rank) VALUES (3, '水', 3);
INSERT INTO mtb_wday (id, name, rank) VALUES (4, '木', 4);
INSERT INTO mtb_wday (id, name, rank) VALUES (5, '金', 5);
INSERT INTO mtb_wday (id, name, rank) VALUES (6, '土', 6);


--
-- Data for Name: mtb_work; Type: TABLE DATA; Schema: public; Owner: nanasess
--

INSERT INTO mtb_work (id, name, rank) VALUES (0, '非稼働', 0);
INSERT INTO mtb_work (id, name, rank) VALUES (1, '稼働', 1);


--



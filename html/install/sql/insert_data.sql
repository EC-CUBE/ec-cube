CREATE INDEX dtb_products_category_id_key ON dtb_products(category_id);
CREATE INDEX dtb_products_class_product_id_key ON dtb_products_class(product_id);
CREATE INDEX dtb_order_detail_product_id_key ON dtb_order_detail(product_id);
CREATE INDEX dtb_category_category_id_key ON dtb_category(category_id);

INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, del_flg, create_date, update_date) 
VALUES ('dummy','dummy',' ',0,0,1,1, now(), now());

insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('���ƥ���',	'include/bloc/category.tpl', 'category','frontparts/bloc/category.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('���ѥ�����','include/bloc/guide.tpl', 'guide','', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('��������',	'include/bloc/cart.tpl', 'cart','frontparts/bloc/cart.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('���ʸ���',	'include/bloc/search_products.tpl', 'search_products','frontparts/bloc/search_products.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('�������',	'include/bloc/news.tpl', 'news','frontparts/bloc/news.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('������',	'include/bloc/login.tpl', 'login','frontparts/bloc/login.php', 1, now(), now());
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path, del_flg, create_date, update_date ) values ('�������ᾦ��','include/bloc/best5.tpl', 'best5','frontparts/bloc/best5.php', 1, now(), now());

insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values('TOP�ڡ���','index.php',' ','user_data/templates/','top',2,now(),now());
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values('���ʰ����ڡ���','products/list.php',' ','user_data/templates/','list',2,now(),now());
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values('���ʾܺ٥ڡ���','products/detail.php',' ','user_data/templates/','detail',2,now(),now());
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg, create_date, update_date)values('MY�ڡ���','mypage/index.php',' ','','',2,now(),now());

insert into dtb_pagelayout (page_id,page_name,url, create_date, update_date)values(0, '�ץ�ӥ塼�ǡ���',' ',now(),now());
update dtb_pagelayout set page_id = 0 where page_id = 5;

INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,1,2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,2,3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,1,3,1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,3,4,2,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,4,5,1,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,3,6,1,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(1,4,7,2,'best5');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,1,2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,2,3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,1,3,1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,4,0,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,5,0,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,6,0,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(2,5,7,0,'best5');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,1,2,'category');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,2,3,'guide');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,1,3,1,'cart');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,4,0,'search_products');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,5,0,'news');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,6,0,'login');
INSERT INTO dtb_blocposition (page_id,target_id,bloc_id,bloc_row,filename)values(3,5,7,0,'best5');

insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_id','����ID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_class_id','����ID',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'classcategory_id1','����̾1',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'classcategory_id2','����̾2',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'name','����̾',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'status','�����ե饰',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_flag','���ʥ��ơ�����',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'product_code','���ʥ�����',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'price01','�̾����',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'price02','�������',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'stock','�߸˿�',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'deliv_fee','����',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'point_rate','�ݥ������ͿΨ',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sale_limit','��������',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'comment1','�᡼����URL',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'comment3','�������',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_list_comment','����-�ᥤ�󥳥���',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_list_image','����-�ᥤ�����',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_comment','�ܺ�-�ᥤ�󥳥���',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_image','�ܺ�-�ᥤ�����',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'main_large_image','�ܺ�-�ᥤ�������� ',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'file1','���顼��Ӳ���',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'file2','���ʾܺ٥ե�����    ',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title1','�ܺ�-���֥����ȥ��1��',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment1','�ܺ�-���֥����ȡ�1��',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image1','�ܺ�-���ֲ�����1��',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image1','�ܺ�-���ֳ��������1��',27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title2','�ܺ�-���֥����ȥ��2��',28,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment2','�ܺ�-���֥����ȡ�2��',29,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image2','�ܺ�-���ֲ�����2��',30,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image2','�ܺ�-���ֳ��������2��',31,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title3','�ܺ�-���֥����ȥ��3��',32,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment3','�ܺ�-���֥����ȡ�3��',33,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image3','�ܺ�-���ֲ�����3��',34,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image3','�ܺ�-���ֳ��������3��',35,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title4','�ܺ�-���֥����ȥ��4��',36,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment4','�ܺ�-���֥����ȡ�4��',37,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image4','�ܺ�-���ֲ�����4��',38,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image4','�ܺ�-���ֳ��������4��',39,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_title5','�ܺ�-���֥����ȥ��5��',40,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_comment5','�ܺ�-���֥����ȡ�5��',41,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_image5','�ܺ�-���ֲ�����5��',42,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'sub_large_image5','�ܺ�-���ֳ��������5��',43,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'deliv_date_id','ȯ�����ܰ�',44,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_product_id1','�������ᾦ��(1)',45,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_comment1','�������ᥳ����(1)',46,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_product_id2','�������ᾦ��(2)',47,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_comment2','�������ᥳ����(2)',48,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_product_id3','�������ᾦ��(3)',49,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_comment3','�������ᥳ����(3)',50,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_product_id4','�������ᾦ��(4)',51,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_comment4','�������ᥳ����(4)',52,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 4) AS recommend_product_id5','�������ᾦ��(5)',53,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 4) AS recommend_comment5','�������ᥳ����(5)',54,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 5) AS recommend_product_id6','�������ᾦ��(6)',55,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'(SELECT comment FROM dtb_recommend_products WHERE prdcls.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 5) AS recommend_comment6','�������ᥳ����(6)',56,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(1,'category_id','���ƥ���ID',57,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'customer_id','�ܵ�ID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'name01','̾��1',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'name02','̾��2',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'kana01','�եꥬ��1',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'kana02','�եꥬ��2',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'zip01', '͹���ֹ�1',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'zip02', '͹���ֹ�2',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'pref', '��ƻ�ܸ�',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'addr01', '����1',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'addr02', '����2',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'email', 'E-MAIL',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel01', 'TEL1',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel02', 'TEL2',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'tel03', 'TEL3',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax01', 'FAX1',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax02', 'FAX2',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'fax03', 'FAX3',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'sex', '����',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'job', '����',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'birth', '������',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'first_buy_date', '��������',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'last_buy_date', '�ǽ�������',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'buy_times', '�������',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'point', '�ݥ���ȻĹ�',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'note', '����',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'create_date','��Ͽ��',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(2,'update_date','������',   27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_id','��ʸID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'customer_id','�ܵ�ID',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'message','��˾��',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_name01','�ܵ�̾1',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_name02','�ܵ�̾2',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_kana01','�ܵ�̾����1',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_kana02','�ܵ�̾����2',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_email','�᡼�륢�ɥ쥹',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel01','�����ֹ�1',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel02','�����ֹ�2',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_tel03','�����ֹ�3',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax01','FAX1',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax02','FAX2',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_fax03','FAX3',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_zip01','͹���ֹ�1',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_zip02','͹���ֹ�2',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_pref','��ƻ�ܸ�',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_addr01','����1',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_addr02','����2',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_sex','����',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_birth','��ǯ����',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'order_job','����',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_name01','������̾��',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_name02','������̾��',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_kana01','�����襫��',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_kana02','�����襫��',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel01','�����ֹ�1',27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel02','�����ֹ�2',28,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_tel03','�����ֹ�3',29,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax01','FAX1',30,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax02','FAX2',31,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fax03','FAX3',32,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_zip01','͹���ֹ�1',33,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_zip02','͹���ֹ�2',34,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_pref','��ƻ�ܸ�',35,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_addr01','����1',36,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_addr02','����2',37,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'subtotal','����',38,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'discount','�Ͱ���',39,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_fee','����',40,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'charge','�����',41,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'use_point','���ѥݥ����',42,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'add_point','�û��ݥ����',43,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'tax','�Ƕ�',44,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'total','���',45,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'payment_total','����ʧ�����',46,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'payment_method','��ʧ����ˡ',47,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_time','��������',48,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'deliv_no','������ɼ�ֹ�',49,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'note','SHOP���',50,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'status','�б�����',51,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'create_date','��ʸ����',52,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(3,'update_date','��������',53,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_id','��ʸID',1,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'campaign_id','�����ڡ���ID',2,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'customer_id','�ܵ�ID',3,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'message','��˾��',4,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_name01','�ܵ�̾1',5,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_name02','�ܵ�̾2',6,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_kana01','�ܵ�̾����1',7,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_kana02','�ܵ�̾����2',8,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_email','�᡼�륢�ɥ쥹',9,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_tel01','�����ֹ�1',10,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_tel02','�����ֹ�2',11,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_tel03','�����ֹ�3',12,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_fax01','FAX1',13,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_fax02','FAX2',14,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_fax03','FAX3',15,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_zip01','͹���ֹ�1',16,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_zip02','͹���ֹ�2',17,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_pref','��ƻ�ܸ�',18,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_addr01','����1',19,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_addr02','����2',20,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_sex','����',21,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_birth','��ǯ����',22,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'order_job','����',23,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_name01','������̾��',24,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_name02','������̾��',25,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_kana01','�����襫��',26,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_kana02','�����襫��',27,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_tel01','�����ֹ�1',28,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_tel02','�����ֹ�2',29,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_tel03','�����ֹ�3',30,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_fax01','FAX1',31,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_fax02','FAX2',32,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_fax03','FAX3',33,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_zip01','͹���ֹ�1',34,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_zip02','͹���ֹ�2',35,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_pref','��ƻ�ܸ�',36,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_addr01','����1',37,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'deliv_addr02','����2',38,now(),now());
insert into dtb_csv(csv_id,col,disp_name,rank,create_date,update_date)values(4,'payment_total','����ʧ�����',39,now(),now());

INSERT INTO dtb_templates (template_code, template_name, create_date, update_date) VALUES('default1','�ǥե����1', now(), now());

insert into dtb_mailtemplate (template_id, subject, body, creator_id, update_date, create_date, send_type, template_name) values (
0,
'����ʸ���꤬�Ȥ��������ޤ���',
'{name}��

�����٤Ϥ���ʸ������������ͭ�񤦤������ޤ���
��������ʸ���Ƥˤ��ְ㤨���ʤ�������ǧ��������

{order}

���Υ�å������Ϥ����ͤؤΤ��Τ餻���ѤǤ��Τǡ�
���Υ�å������ؤ��ֿ��Ȥ��Ƥ���������ꤤ�������Ƥ�����Ǥ��ޤ���
��λ������������

������䤴�����������������ޤ����顢�����餫�餪�ꤤ�������ޤ���',
0,
Now(),
Now(),
0,
'����λ�ƥ�ץ졼��(PC��)');

insert into dtb_mailtemplate (template_id, subject, body, creator_id, update_date, create_date, send_type, template_name) values (
1,
'����ʸ���꤬�Ȥ��������ޤ���',
'{name}��

�����٤Ϥ���ʸ������������ͭ�񤦤������ޤ���
��������ʸ���Ƥˤ��ְ㤨���ʤ�������ǧ��������

{order}

���Υ�å������Ϥ����ͤؤΤ��Τ餻���ѤǤ��Τǡ�
���Υ�å������ؤ��ֿ��Ȥ��Ƥ���������ꤤ�������Ƥ�����Ǥ��ޤ���
��λ������������

������䤴�����������������ޤ����顢�����餫�餪�ꤤ�������ޤ���',
0,
Now(),
Now(),
1,
'����λ�ƥ�ץ졼��(������)');

insert into dtb_news (news_date,rank, news_title, news_comment, creator_id, create_date, update_date) 
values(now(),1,'�����ȥ����ץ󤤤����ޤ���!','�����餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ��������餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ��������餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ���',1, now(), now());

INSERT INTO dtb_deliv (name,service_name,confirm_url,rank,status,del_flg,creator_id,create_date,update_date)
VALUES ('����ץ�ȼ�', '����ץ�ȼ�', '', 1, 1, 0, 2, now(), now());

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

INSERT INTO dtb_delivtime (deliv_id, deliv_time) VALUES (1, '����');
INSERT INTO dtb_delivtime (deliv_id, deliv_time) VALUES (1, '���');

INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('͹�ؿ���', 0, NULL, 1, 4, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('�����α', 0, NULL, 1, 3, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('��Կ���', 0, NULL, 1, 2, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);
INSERT INTO dtb_payment (payment_method,charge,rule,deliv_id,rank,note,fix,status,del_flg,creator_id,create_date,update_date,payment_image,upper_rule) VALUES ('������', 0, NULL, 1, 1, NULL, 2, 1, 0, 1, now(), now(), NULL, NULL);

INSERT INTO dtb_products (name,deliv_fee,sale_limit,sale_unlimited,category_id,rank,status,product_flag,point_rate,comment1,comment2,comment3,comment4,comment5,comment6,file1,file2,file3,file4,file5,file6,main_list_comment,main_list_image,main_comment,main_image,main_large_image,sub_title1,sub_comment1,sub_image1,sub_large_image1,sub_title2,sub_comment2,sub_image2,sub_large_image2,sub_title3,sub_comment3,sub_image3,sub_large_image3,sub_title4,sub_comment4,sub_image4,sub_large_image4,sub_title5,sub_comment5,sub_image5,sub_large_image5,sub_title6,sub_comment6,sub_image6,sub_large_image6,del_flg,creator_id,create_date,update_date,deliv_date_id) 
VALUES ('���������꡼��', NULL, NULL, 1, 5, 1, 1, '10010', 10, NULL, NULL, '������,�Х˥�,���祳,����', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '�뤤�Ƥˤɤ�����', '08311201_44f65122ee5fe.jpg', '�䤿����ΤϤ������Ǥ�����', '08311202_44f6515906a41.jpg', '08311203_44f651959bcb5.jpg', NULL, '<b>����������<b>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, now(), now(), 2);
INSERT INTO dtb_products (name,deliv_fee,sale_limit,sale_unlimited,category_id,rank,status,product_flag,point_rate,comment1,comment2,comment3,comment4,comment5,comment6,file1,file2,file3,file4,file5,file6,main_list_comment,main_list_image,main_comment,main_image,main_large_image,sub_title1,sub_comment1,sub_image1,sub_large_image1,sub_title2,sub_comment2,sub_image2,sub_large_image2,sub_title3,sub_comment3,sub_image3,sub_large_image3,sub_title4,sub_comment4,sub_image4,sub_large_image4,sub_title5,sub_comment5,sub_image5,sub_large_image5,sub_title6,sub_comment6,sub_image6,sub_large_image6,del_flg,creator_id,create_date,update_date,deliv_date_id)
VALUES ('���ʤ�', NULL, 5, NULL, 4, 1, 1, '11001', 5, NULL, NULL, '��,�ʤ�,�ʥ�', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '����Ѥ��餢��ޤ���', '08311311_44f661811fec0.jpg', '���ޤˤ���Ǥ�ɤ��Ǥ��礦��', '08311313_44f661dc649fb.jpg', '08311313_44f661e5698a6.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, now(), now(), 3);

INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
DELETE FROM dtb_products_class;
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date) 
VALUES (1, 3, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 3, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 2, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 6, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 5, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (1, 1, 4, 'ice-01', NULL, 1, NULL, 150, 120, NULL, 2, now(), now());
INSERT INTO dtb_products_class (product_id,classcategory_id1,classcategory_id2,product_code,stock,stock_unlimited,sale_limit,price01,price02,status,creator_id,create_date,update_date)
VALUES (2, 0, 0, 'nabe-01', 100, NULL, NULL, 1700, 1650, NULL, 2, now(), now());

INSERT INTO dtb_recommend_products (product_id,rank,comment,status,creator_id,create_date,update_date) VALUES (2, 4, '����ľ���ˡ�', 0, 2, now(), now());

INSERT INTO dtb_class (name,status,rank,creator_id,create_date,update_date,del_flg,product_id) VALUES ('̣', NULL, 1, 2, now(), now(), 0, NULL);
INSERT INTO dtb_class (name,status,rank,creator_id,create_date,update_date,del_flg,product_id) VALUES ('�礭��', NULL, 2, 2, now(), now(), 0, NULL);

INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('�Х˥�', 1, NULL, 1, 2, now(), now(), 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('���祳', 1, NULL, 2, 2, now(), now(), 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('����', 1, NULL, 3, 2, now(), now(), 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('L', 2, NULL, 1, 2, now(), now(), 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('M', 2, NULL, 2, 2, now(), now(), 0);
INSERT INTO dtb_classcategory (name,class_id,status,rank,creator_id,create_date,update_date,del_flg) VALUES ('S', 2, NULL, 3, 2, now(), now(), 0);

INSERT INTO dtb_classcategory (classcategory_id, class_id, rank, creator_id, create_date, update_date) 
VALUES (0, 0, 0, 0, now(), now());
UPDATE dtb_classcategory SET classcategory_id = 0 WHERE class_id = 0;

INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('����', 0, 1, 4, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('����', 0, 1, 5, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('���ۻ�', 1, 2, 2, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('�ʤ�', 1, 2, 3, 2, now(), now(), 0);
INSERT INTO dtb_category (category_name,parent_category_id,level,rank,creator_id,create_date,update_date,del_flg) VALUES ('������', 3, 3, 1, 2, now(), now(), 0);

INSERT INTO dtb_category_count VALUES (4, 1, now());
INSERT INTO dtb_category_count VALUES (5, 1, now());

INSERT INTO dtb_category_total_count VALUES (3, 1, now());
INSERT INTO dtb_category_total_count VALUES (1, 2, now());
INSERT INTO dtb_category_total_count VALUES (2, NULL, now());
INSERT INTO dtb_category_total_count VALUES (5, 1, now());
INSERT INTO dtb_category_total_count VALUES (4, 1, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��1��ʲ����','1. �ֲ���פȤϡ����Ҥ������³�˽����ܵ����Ʊ�դξ塢����ο������ߤ�Ԥ��Ŀͤ򤤤��ޤ���
2. �ֲ������פȤϡ���������Ҥ˳������������°���˴ؤ�����󤪤�Ӳ���μ���˴ؤ����������ξ���򤤤��ޤ���
3. �ܵ���ϡ����٤Ƥβ����Ŭ�Ѥ��졢��Ͽ��³���������Ͽ��ˤ���ꤤ����������Ǥ���',
12,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��2�����Ͽ��','1. ������
�ܵ����Ʊ�դξ塢��������񿽹��ߤ򤵤줿�����ͤϡ��������Ͽ��³��λ��˲���Ȥ��Ƥλ�ʤ�ͭ���ޤ��������Ͽ��³�ϡ�����Ȥʤ뤴�ܿͤ��ԤäƤ��������������ˤ����Ͽ�ϰ���ǧ����ޤ��󡣤ʤ������˲����ʤ����ä��줿���䤽��¾���Ҥ���������ʤ���Ƚ�Ǥ���������β�������Ϥ��Ǥꤹ���礬����ޤ���

2. ������������
�����Ͽ��³�κݤˤϡ����Ͼ����դ�褯�ɤߡ���������ϥե������ɬ�׻�������Τ����Ϥ��Ƥ�������������������Ͽ�ˤ����ơ��ü쵭�桦����������޿����ʤɤϤ����Ѥˤʤ�ޤ��󡣤�����ʸ������Ͽ���줿�������Ҥˤ��ѹ��פ��ޤ���

3. �ѥ���ɤδ���
(1)�ѥ���ɤϲ���ܿͤΤߤ����ѤǤ����ΤȤ����軰�Ԥ˾��ϡ���Ϳ�Ǥ��ʤ���ΤȤ��ޤ���
(2)�ѥ���ɤϡ�¾�ͤ��Τ��뤳�Ȥ��ʤ��褦���Ū���ѹ�������������ܿͤ���Ǥ���äƴ������Ƥ���������
(3)�ѥ���ɤ��Ѥ������Ҥ��Ф��ƹԤ�줿�ջ�ɽ���ϡ�����ܿͤΰջ�ɽ���Ȥߤʤ������Τ�����������ʧ���Ϥ��٤Ʋ������Ǥ�Ȥʤ�ޤ���',
11,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��3����ѹ���','1. ����ϡ���̾������ʤ����Ҥ��Ϥ��Ф�������ѹ������ä����ˤϡ�®�䤫�����Ҥ�Ϣ�����ΤȤ��ޤ���
2. �ѹ���Ͽ���ʤ���ʤ��ä����Ȥˤ��������»���ˤĤ��ơ����Ҥϰ�����Ǥ���餤�ޤ��󡣤ޤ����ѹ���Ͽ���ʤ��줿���Ǥ⡢�ѹ���Ͽ���ˤ��Ǥ˼�³���ʤ��줿����ϡ��ѹ���Ͽ���ξ���˴�Ť��ƹԤ��ޤ��ΤǤ���դ���������',
10,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��4�������','����������˾������ˤϡ�����ܿͤ�����³����ԤäƤ������������������³�ν�λ��ˡ����Ȥʤ�ޤ���',
9,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��5��ʲ����ʤ��Ӽ��ڤ������̳��','1. ������������ʼ��������κݤ˵����ο���򤷤��Ȥ����̿�����ˤ������ʧ��̳���դä��Ȥ�������¾���Ҥ�����Ȥ�����Ŭ����ǧ����ͳ������Ȥ��ϡ����Ҥϡ������ʤ���ä����Ȥ��Ǥ��뤳�ȤȤ��ޤ���
2. ��������ʲ��γƹ������԰٤򤷤��Ȥ��ϡ�����ˤ�����Ҥ���ä�»�������������Ǥ���餤�ޤ���
(1)����ֹ桢�ѥ���ɤ����������Ѥ��뤳��
(2)���ۡ���ڡ����˥����������ƾ��������󤷤��ꡢ���ۡ���ڡ�����ͭ���ʥ���ԥ塼���ץ�������������ʤɤ��ơ����ҤαĶȤ�˸�����뤳��
(3)���Ҥ��������ʤ���Ū��ͭ���򿯳�����԰٤򤹤뤳��
(4)����¾���������ѵ����ȿ����԰٤򤹤뤳��',
8,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��6��ʲ������μ谷����','1. ���Ҥϡ���§�Ȥ��Ʋ����������λ�����Ʊ�դʤ��軰�Ԥ��Ф��Ƴ������뤳�ȤϤ���ޤ��󡣤����������γƹ�ξ��ˤϡ�����λ�����Ʊ�դʤ������Ҥϲ�����󤽤�¾�Τ����;���򳫼��Ǥ����ΤȤ��ޤ���
(1)ˡ��˴�Ť����������줿���
(2)���Ҥθ��������ס�̾�������ݸ�뤿���ɬ�פǤ�������Ҥ�Ƚ�Ǥ������
2. �������ˤĤ��ޤ��Ƥϡ����ҤΡָĿ;����ݸ�ؤμ��Ȥߡפ˽��������Ҥ��������ޤ������Ҥϡ��������򡢲���ؤΥ����ӥ��󶡡������ӥ����Ƥθ��塢�����ӥ�������¥�ʡ�����ӥ����ӥ��η������ı߳�ʱ��Ĥγ��ݤ�ޤ���Ū�Τ���ˡ����Ҥ��������Ѥ��뤳�Ȥ��Ǥ����ΤȤ��ޤ���
3. ���Ҥϡ�������Ф��ơ��᡼��ޥ����󤽤�¾����ˡ�ˤ������󶡡ʹ����ޤߤޤ��ˤ�Ԥ����Ȥ��Ǥ����ΤȤ��ޤ�������������󶡤��˾���ʤ����ϡ����ҽ������ˡ�˽��������λݤ����Τ���ĺ����С������󶡤���ߤ��ޤ������������ܥ����ӥ����Ĥ�ɬ�פʾ����󶡤ˤĤ��ޤ��Ƥϡ�����δ�˾�ˤ����ߤ򤹤뤳�ȤϤǤ��ޤ���',
7,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��7��ʶػ߻����','�ܥ����ӥ������Ѥ˺ݤ��ơ�������Ф����γƹ�ι԰٤�Ԥ����Ȥ�ػߤ��ޤ���

1. ˡ��ޤ����ܵ����ܥ����ӥ������Ѿ�Τ���ա��ܥ����ӥ��ǤΤ��㤤ʪ��Τ���դ���¾���ܵ������˰�ȿ���뤳��
2. ���ҡ�����Ӥ���¾���軰�Ԥθ��������ס�̾������»�ͤ뤳��
3. �ľ�ǯ�ο��Ȥ˰��ƶ���ڤܤ����줬����԰١�����¾������¯��ȿ����԰٤�Ԥ�����
4. ¾�����ѼԤ���¾���軰�Ԥ����ǤȤʤ�԰٤��Բ�������������԰٤�Ԥ�����
5. �����ξ�������Ϥ��뤳��
6. ͭ���ʥ���ԥ塼���ץ���ࡢ�᡼�����������ޤ��Ͻ񤭹��ळ��
7. ���ҤΥ����Ф���¾�Υ���ԥ塼���������˥����������뤳��
8. �ѥ���ɤ��軰�Ԥ���Ϳ�����Ϥ��뤳�ȡ��ޤ����軰�Ԥȶ��Ѥ��뤳��
9. ����¾���Ҥ���Ŭ�ڤ�Ƚ�Ǥ��뤳��',
6,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��8��ʥ����ӥ������ǡ��������','1. ���Ҥϡ��ܥ����ӥ��β�ư���֤��ɹ����ݤĤ���ˡ����γƹ�ΰ�˳��������硢ͽ��ʤ��ˡ��ܥ����ӥ��������Ƥ��뤤�ϰ�������ߤ��뤳�Ȥ�����ޤ���
(1)�����ƥ������ݼ餪��Ӷ۵��ݼ�Τ����ɬ�פʾ��
(2)�����ƥ����٤����椷�����
(3)�кҡ����š��軰�Ԥˤ��˸���԰٤ʤɤˤ�ꥷ���ƥ�α��Ѥ�����ˤʤä����
(4)����¾���ߤ�����������ƥ����ߤ�ɬ�פ����Ҥ�Ƚ�Ǥ������',
5,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��9��ʥ����ӥ����ѹ����ѻߡ�','���Ҥϡ�����Ƚ�Ǥˤ�ꥵ���ӥ��������ޤ��ϰ�������������Τʤ���Ŭ���ѹ����ѻߤǤ����ΤȤ��ޤ���',
4,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��10������ա�','1. �̿������䥳��ԥ塼���ʤɤξ㳲�ˤ�륷���ƥ�����ǡ����ڡ���ߡ��ǡ����ξü����ǡ����ؤ��������������ˤ��������»��������¾���ҤΥ����ӥ��˴ؤ��Ʋ����������»���ˤĤ��ơ����Ҥϰ�����Ǥ�����ʤ���ΤȤ��ޤ���
2. ���Ҥϡ����ҤΥ����֥ڡ����������С��ɥᥤ��ʤɤ���������᡼�롦����ƥ�Ĥˡ�����ԥ塼���������륹�ʤɤ�ͭ���ʤ�Τ��ޤޤ�Ƥ��ʤ����Ȥ��ݾڤ������ޤ���
3. ������ܵ������˰�ȿ�������Ȥˤ�ä�������»���ˤĤ��Ƥϡ����Ҥϰ�����Ǥ���餤�ޤ���',
3,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��11����ܵ���β����','���Ҥϡ��ܵ����Ǥ�դ˲���Ǥ����ΤȤ����ޤ������Ҥˤ������ܵ�����佼���뵬��ʰʲ����佼����פȤ����ޤ��ˤ����뤳�Ȥ��Ǥ��ޤ����ܵ���β���ޤ����佼�ϡ��������ܵ���ޤ����佼��������ҽ���Υ����Ȥ˷Ǽ������Ȥ��ˤ��θ��Ϥ��������ΤȤ��ޤ������ξ�硢����ϡ������ε��󤪤���佼����˽�����Τ��פ��ޤ���',
2,0,Now(),0, now());

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, del_flg, create_date) 
VALUES ('��12��ʽ��ˡ���ɳ��Ƚ���','�ܵ���˴ؤ���ʶ�褬��������硢������Ź����Ϥ�ɳ���������Ƚ�����쿳����°Ū��մɳ��Ƚ��Ȥ��ޤ��� ',
1,0,Now(),0, now());
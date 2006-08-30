
CREATE INDEX dtb_products_category_id_key ON dtb_products(category_id);
CREATE INDEX dtb_products_class_product_id_key ON dtb_products_class(product_id);
CREATE INDEX dtb_order_detail_product_id_key ON dtb_order_detail(product_id);
CREATE INDEX dtb_category_category_id_key ON dtb_category(category_id);

INSERT INTO dtb_classcategory (classcategory_id, class_id, rank, creator_id, create_date) 
VALUES (0, 0, 0, 0, now());

INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, delete) 
VALUES ('dummy','dummy','',0,0,1,1);

INSERT INTO dtb_member (name, login_id, password, creator_id, authority, work, delete) 
VALUES ('admin','admin','$1$JPUS3lIX$B0FJNs4Q0lv9i.UYBP7do0',0,0,1,0);

-- �֥�å��ǡ���
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('���ƥ���',	'html/user_data/include/bloc/category.tpl',			'category','html/frontparts/bloc/category.php');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('���ѥ�����',	'html/user_data/include/bloc/guide.tpl',			'guide','');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('��������',	'html/user_data/include/bloc/cart.tpl',				'cart','html/frontparts/bloc/cart.php');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('���ʸ���',	'html/user_data/include/bloc/search_products.tpl',	'search_products','html/frontparts/bloc/search_products.php');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('�������',	'html/user_data/include/bloc/news.tpl',				'news','html/frontparts/bloc/news.php');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('������',	'html/user_data/include/bloc/login.tpl',			'login','');
insert into dtb_bloc ( bloc_name, tpl_path, filename, php_path ) values ('�������ᾦ��','html/user_data/include/bloc/best5.tpl',			'best5','html/frontparts/bloc/best5.php');

-- �ڡ����ǡ���
insert into dtb_pagelayout (page_id,page_name,url)values(0, '�ץ�ӥ塼�ǡ���','');
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg)values('TOP�ڡ���','index.php',' ','/html/user_data/templates/','top',2);
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg)values('���ʰ����ڡ���','products/list.php',' ','/html/user_data/templates/','list',2);
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg)values('���ʾܺ�','products/detail.php',' ','/html/user_data/templates/','detail',2);
insert into dtb_pagelayout (page_name,url,php_dir,tpl_dir,filename,edit_flg)values('MY�ڡ���','mypage/index.php',' ','','',2);

-- �֥�å����֥ǡ���
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

-- CSV�ǡ���
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'product_id','����ID',1);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'product_class_id','����ID',2);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'classcategory_id1','����̾1',3);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'classcategory_id2','����̾2',4);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'name','����̾',5);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'status','�����ե饰',6);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'product_flag','���ʥ��ơ�����',7);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'product_code','���ʥ�����',8);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'price01','���ͻԾ����',9);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'price02','����',10);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'stock','�߸˿�',11);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'deliv_fee','����',12);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'point_rate','�ݥ������ͿΨ',13);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sale_limit','��������',14);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'comment1','�᡼����URL',15);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'comment3','�������',16);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'main_list_comment','����-�ᥤ�󥳥���',17);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'main_list_image','����-�ᥤ�����',18);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'main_comment','�ܺ�-�ᥤ�󥳥���',19);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'main_image','�ܺ�-�ᥤ�����',20);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'main_large_image','�ܺ�-�ᥤ�������� ',21);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'file1','���顼��Ӳ���',22);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'file2','���ʾܺ٥ե�����    ',23);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_title1','�ܺ�-���֥����ȥ��1��',24);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_comment1','�ܺ�-���֥����ȡ�1��',25);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_image1','�ܺ�-���ֲ�����1��',26);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_large_image1','�ܺ�-���ֳ��������1��',27);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_title2','�ܺ�-���֥����ȥ��2��',28);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_comment2','�ܺ�-���֥����ȡ�2��',29);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_image2','�ܺ�-���ֲ�����2��',30);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_large_image2','�ܺ�-���ֳ��������2��',31);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_title3','�ܺ�-���֥����ȥ��3��',32);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_comment3','�ܺ�-���֥����ȡ�3��',33);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_image3','�ܺ�-���ֲ�����3��',34);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_large_image3','�ܺ�-���ֳ��������3��',35);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_title4','�ܺ�-���֥����ȥ��4��',36);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_comment4','�ܺ�-���֥����ȡ�4��',37);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_image4','�ܺ�-���ֲ�����4��',38);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_large_image4','�ܺ�-���ֳ��������4��',39);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_title5','�ܺ�-���֥����ȥ��5��',40);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_comment5','�ܺ�-���֥����ȡ�5��',41);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_image5','�ܺ�-���ֲ�����5��',42);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'sub_large_image5','�ܺ�-���ֳ��������5��',43);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'deliv_date_id','ȯ�����ܰ�',44);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_product_id1','�������ᾦ��(1)',45);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1) AS recommend_comment1','�������ᥳ����(1)',46);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_product_id2','�������ᾦ��(2)',47);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 1) AS recommend_comment2','�������ᥳ����(2)',48);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_product_id3','�������ᾦ��(3)',49);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 2) AS recommend_comment3','�������ᥳ����(3)',50);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT recommend_product_id FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_product_id4','�������ᾦ��(4)',51);
insert into dtb_csv(csv_id,col,disp_name,rank)values(1,'(SELECT comment FROM dtb_recommend_products WHERE vw_product_class.product_id = dtb_recommend_products.product_id ORDER BY update_date DESC limit 1 offset 3) AS recommend_comment4','�������ᥳ����(4)',52);

insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'customer_id','�ܵ�ID',1);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'name01','̾��1',2);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'name02','̾��2',3);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'kana01','�եꥬ��1',4);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'kana02','�եꥬ��2',5);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'zip01', '͹���ֹ�1',6);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'zip02', '͹���ֹ�2',7);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'pref', '��ƻ�ܸ�',8);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'addr01', '����1',9);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'addr02', '����2',10);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'email', 'E-MAIL',11);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'tel01', 'TEL1',12);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'tel02', 'TEL2',13);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'tel03', 'TEL3',14);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'fax01', 'FAX1',15);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'fax02', 'FAX2',16);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'fax03', 'FAX3',17);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'sex', '����',18);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'job', '����',19);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'birth', '������',20);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'first_buy_date', '��������',21);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'last_buy_date', '�ǽ�������',22);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'buy_times', '�������',23);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'point', '�ݥ���ȻĹ�',24);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'note', '����',25);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'create_date','��Ͽ��',26);
insert into dtb_csv(csv_id,col,disp_name,rank)values(2,'update_date','������',   27);

insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_id','��ʸID',1);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'customer_id','�ܵ�ID',2);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'message','��˾��',3);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_name01','�ܵ�̾1',4);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_name02','�ܵ�̾2',5);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_kana01','�ܵ�̾����1',6);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_kana02','�ܵ�̾����2',7);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_email','�᡼�륢�ɥ쥹',8);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_tel01','�����ֹ�1',9);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_tel02','�����ֹ�2',10);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_tel03','�����ֹ�3',11);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_fax01','FAX1',12);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_fax02','FAX2',13);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_fax03','FAX3',14);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_zip01','͹���ֹ�1',15);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_zip02','͹���ֹ�2',16);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_pref','��ƻ�ܸ�',17);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_addr01','����1',18);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_addr02','����2',19);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_sex','����',20);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_birth','��ǯ����',21);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'order_job','����',22);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_name01','������̾��',23);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_name02','������̾��',24);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_kana01','�����襫��',25);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_kana02','�����襫��',26);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_tel01','�����ֹ�1',27);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_tel02','�����ֹ�2',28);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_tel03','�����ֹ�3',29);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_fax01','FAX1',30);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_fax02','FAX2',31);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_fax03','FAX3',32);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_zip01','͹���ֹ�1',33);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_zip02','͹���ֹ�2',34);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_pref','��ƻ�ܸ�',35);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_addr01','����1',36);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_addr02','����2',37);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'subtotal','����',38);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'discount','�Ͱ���',39);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_fee','����',40);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'charge','�����',41);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'use_point','���ѥݥ����',42);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'add_point','�û��ݥ����',43);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'tax','�Ƕ�',44);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'total','���',45);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'payment_total','����ʧ�����',46);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'payment_method','��ʧ����ˡ',47);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_time','��������',48);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'deliv_no','������ɼ�ֹ�',49);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'note','SHOP���',50);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'status','�б�����',51);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'create_date','��ʸ����',52);
insert into dtb_csv(csv_id,col,disp_name,rank)values(3,'update_date','��������',53);

-- ��ʧ��ˡ�ǡ���
insert into dtb_payment(payment_method,rank,fix,creator_id)values('������',1,2,1);
insert into dtb_payment(payment_method,rank,fix,creator_id)values('��Կ���',2,2,1);
insert into dtb_payment(payment_method,rank,fix,creator_id)values('�����α',3,2,1);
insert into dtb_payment(payment_method,rank,fix,creator_id)values('͹�ؿ���',4,2,1);

-- ��ʸ��λ�᡼��
update dtb_mailtemplate set 
subject = '����ʸ���꤬�Ȥ��������ޤ���',
header = 
'�����٤Ϥ���ʸ������������ͭ�񤦤������ޤ���
��������ʸ���Ƥˤ��ְ㤨���ʤ�������ǧ��������

',
footer = 
'

==============================================================��
���Υ�å������Ϥ����ͤؤΤ��Τ餻���ѤǤ��Τǡ�
���Υ�å������ؤ��ֿ��Ȥ��Ƥ���������ꤤ�������Ƥ�����Ǥ��ޤ���
��λ������������

������䤴�����������������ޤ����顢�����餫�餪�ꤤ�������ޤ���
http://------.co.jp

'
where template_id = 1;

-- �������ǡ���
insert into dtb_news (news_title, news_comment, creator_id) 
values('�����ȥ����ץ󤤤����ޤ���!','�����餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ��������餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ��������餷���饪�ե����ʤɤ��ޤ��ޤʥ������ ���ʤ�������򥵥ݡ��Ȥ��륰�å��򤴲���ؤ��Ϥ����ޤ���',1);

-- ���ʥǡ���
insert into dtb_products (name,sale_unlimited,category_id,rank,status,product_flag,point_rate,main_list_comment,main_list_image,main_comment,main_image,main_large_image, creator_id)
values('�²ۻ�3�����å�(����)','1','1','1','1','0','100','����-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ���','08281319_44f26ebbf2435.jpg','�ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ���','08281319_44f26ec47f8c7.jpg','08281319_44f26eca33e2f.jpg', 1);
insert into dtb_products (name,sale_unlimited,category_id,rank,status,product_flag,point_rate,main_list_comment,main_list_image,main_comment,main_image,main_large_image, creator_id)
values('�²ۻ�3�����å�(����)','1','1','1','1','10','100','����-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ�������-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ���','08281159_44f25c1bb46e4.jpg','�ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ����ܺ�-�ᥤ�󥳥��ȥ��ߡ��ƥ����ȤǤ���','08281200_44f25c4be7e1a.jpg','08281201_44f25c6daaf90.jpg', 1);

-- ���ʥ��饹�ǡ���
insert into dtb_products_class(product_id,classcategory_id1,classcategory_id2,product_code,stock_unlimited,price02,creator_id)
values('1','0','0','a-001','1','1500','1');
insert into dtb_products_class(product_id,classcategory_id1,classcategory_id2,product_code,stock_unlimited,price02,creator_id)
values('2','0','0','a-002','1','1500','1');

-- �������ᾦ��
insert into dtb_best_products (rank,product_id, comment, category_id, creator_id)
values(1,1,'���ߡ��������ᥳ���ȡ�',1,1);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裱��ʲ����','1. �ֲ���פȤϡ����Ҥ������³�˽����ܵ����Ʊ�դξ塢����ο������ߤ�Ԥ��Ŀͤ򤤤��ޤ���
2. �ֲ������פȤϡ���������Ҥ˳������������°���˴ؤ�����󤪤�Ӳ���μ���˴ؤ����������ξ���򤤤��ޤ���
3. �ܵ���ϡ����٤Ƥβ����Ŭ�Ѥ��졢��Ͽ��³���������Ͽ��ˤ���ꤤ����������Ǥ���',
12,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裲�����Ͽ��','1. ������
�ܵ����Ʊ�դξ塢��������񿽹��ߤ򤵤줿�����ͤϡ��������Ͽ��³��λ��˲���Ȥ��Ƥλ�ʤ�ͭ���ޤ��������Ͽ��³�ϡ�����Ȥʤ뤴�ܿͤ��ԤäƤ��������������ˤ����Ͽ�ϰ���ǧ����ޤ��󡣤ʤ������˲����ʤ����ä��줿���䤽��¾���Ҥ���������ʤ���Ƚ�Ǥ���������β�������Ϥ��Ǥꤹ���礬����ޤ���

2. ������������
�����Ͽ��³�κݤˤϡ����Ͼ����դ�褯�ɤߡ���������ϥե������ɬ�׻�������Τ����Ϥ��Ƥ�������������������Ͽ�ˤ����ơ��ü쵭�桦����������޿����ʤɤϤ����Ѥˤʤ�ޤ��󡣤�����ʸ������Ͽ���줿�������Ҥˤ��ѹ��פ��ޤ���

3. �ѥ���ɤδ���
(1)�ѥ���ɤϲ���ܿͤΤߤ����ѤǤ����ΤȤ����軰�Ԥ˾��ϡ���Ϳ�Ǥ��ʤ���ΤȤ��ޤ���
(2)�ѥ���ɤϡ�¾�ͤ��Τ��뤳�Ȥ��ʤ��褦���Ū���ѹ�������������ܿͤ���Ǥ���äƴ������Ƥ���������
(3)�ѥ���ɤ��Ѥ������Ҥ��Ф��ƹԤ�줿�ջ�ɽ���ϡ�����ܿͤΰջ�ɽ���Ȥߤʤ������Τ�����������ʧ���Ϥ��٤Ʋ������Ǥ�Ȥʤ�ޤ���',
11,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裳����ѹ���','1. ����ϡ���̾������ʤ����Ҥ��Ϥ��Ф�������ѹ������ä����ˤϡ�®�䤫�����Ҥ�Ϣ�����ΤȤ��ޤ���
2. �ѹ���Ͽ���ʤ���ʤ��ä����Ȥˤ��������»���ˤĤ��ơ����Ҥϰ�����Ǥ���餤�ޤ��󡣤ޤ����ѹ���Ͽ���ʤ��줿���Ǥ⡢�ѹ���Ͽ���ˤ��Ǥ˼�³���ʤ��줿����ϡ��ѹ���Ͽ���ξ���˴�Ť��ƹԤ��ޤ��ΤǤ���դ���������',
10,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裴�������','����������˾������ˤϡ�����ܿͤ�����³����ԤäƤ������������������³�ν�λ��ˡ����Ȥʤ�ޤ���',
9,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裵��ʲ����ʤ��Ӽ��ڤ������̳��','1. ������������ʼ��������κݤ˵����ο���򤷤��Ȥ����̿�����ˤ������ʧ��̳���դä��Ȥ�������¾���Ҥ�����Ȥ�����Ŭ����ǧ����ͳ������Ȥ��ϡ����Ҥϡ������ʤ���ä����Ȥ��Ǥ��뤳�ȤȤ��ޤ���
2. ��������ʲ��γƹ������԰٤򤷤��Ȥ��ϡ�����ˤ�����Ҥ���ä�»�������������Ǥ���餤�ޤ���
(1)����ֹ桢�ѥ���ɤ����������Ѥ��뤳��
(2)���ۡ���ڡ����˥����������ƾ��������󤷤��ꡢ���ۡ���ڡ�����ͭ���ʥ���ԥ塼���ץ�������������ʤɤ��ơ����ҤαĶȤ�˸�����뤳��
(3)���Ҥ��������ʤ���Ū��ͭ���򿯳�����԰٤򤹤뤳��
(4)����¾���������ѵ����ȿ����԰٤򤹤뤳��',
8,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裶��ʲ������μ谷����','1. ���Ҥϡ���§�Ȥ��Ʋ����������λ�����Ʊ�դʤ��軰�Ԥ��Ф��Ƴ������뤳�ȤϤ���ޤ��󡣤����������γƹ�ξ��ˤϡ�����λ�����Ʊ�դʤ������Ҥϲ�����󤽤�¾�Τ����;���򳫼��Ǥ����ΤȤ��ޤ���
(1)ˡ��˴�Ť����������줿���
(2)���Ҥθ��������ס�̾�������ݸ�뤿���ɬ�פǤ�������Ҥ�Ƚ�Ǥ������
2. �������ˤĤ��ޤ��Ƥϡ����ҤΡָĿ;����ݸ�ؤμ��Ȥߡפ˽��������Ҥ��������ޤ������Ҥϡ��������򡢲���ؤΥ����ӥ��󶡡������ӥ����Ƥθ��塢�����ӥ�������¥�ʡ�����ӥ����ӥ��η������ı߳�ʱ��Ĥγ��ݤ�ޤ���Ū�Τ���ˡ����Ҥ��������Ѥ��뤳�Ȥ��Ǥ����ΤȤ��ޤ���
3. ���Ҥϡ�������Ф��ơ��᡼��ޥ����󤽤�¾����ˡ�ˤ������󶡡ʹ����ޤߤޤ��ˤ�Ԥ����Ȥ��Ǥ����ΤȤ��ޤ�������������󶡤��˾���ʤ����ϡ����ҽ������ˡ�˽��������λݤ����Τ���ĺ����С������󶡤���ߤ��ޤ������������ܥ����ӥ����Ĥ�ɬ�פʾ����󶡤ˤĤ��ޤ��Ƥϡ�����δ�˾�ˤ����ߤ򤹤뤳�ȤϤǤ��ޤ���',
7,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裷��ʶػ߻����','�ܥ����ӥ������Ѥ˺ݤ��ơ�������Ф����γƹ�ι԰٤�Ԥ����Ȥ�ػߤ��ޤ���

1. ˡ��ޤ����ܵ����ܥ����ӥ������Ѿ�Τ���ա��ܥ����ӥ��ǤΤ��㤤ʪ��Τ���դ���¾���ܵ������˰�ȿ���뤳��
2. ���ҡ�����Ӥ���¾���軰�Ԥθ��������ס�̾������»�ͤ뤳��
3. �ľ�ǯ�ο��Ȥ˰��ƶ���ڤܤ����줬����԰١�����¾������¯��ȿ����԰٤�Ԥ�����
4. ¾�����ѼԤ���¾���軰�Ԥ����ǤȤʤ�԰٤��Բ�������������԰٤�Ԥ�����
5. �����ξ�������Ϥ��뤳��
6. ͭ���ʥ���ԥ塼���ץ���ࡢ�᡼�����������ޤ��Ͻ񤭹��ळ��
7. ���ҤΥ����Ф���¾�Υ���ԥ塼���������˥����������뤳��
8. �ѥ���ɤ��軰�Ԥ���Ϳ�����Ϥ��뤳�ȡ��ޤ����軰�Ԥȶ��Ѥ��뤳��
9. ����¾���Ҥ���Ŭ�ڤ�Ƚ�Ǥ��뤳��',
6,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裸��ʥ����ӥ������ǡ��������','1. ���Ҥϡ��ܥ����ӥ��β�ư���֤��ɹ����ݤĤ���ˡ����γƹ�ΰ�˳��������硢ͽ��ʤ��ˡ��ܥ����ӥ��������Ƥ��뤤�ϰ�������ߤ��뤳�Ȥ�����ޤ���
(1)�����ƥ������ݼ餪��Ӷ۵��ݼ�Τ����ɬ�פʾ��
(2)�����ƥ����٤����椷�����
(3)�кҡ����š��軰�Ԥˤ��˸���԰٤ʤɤˤ�ꥷ���ƥ�α��Ѥ�����ˤʤä����
(4)����¾���ߤ�����������ƥ����ߤ�ɬ�פ����Ҥ�Ƚ�Ǥ������',
5,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('�裹��ʥ����ӥ����ѹ����ѻߡ�','���Ҥϡ�����Ƚ�Ǥˤ�ꥵ���ӥ��������ޤ��ϰ�������������Τʤ���Ŭ���ѹ����ѻߤǤ����ΤȤ��ޤ���',
4,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('��10������ա�','1. �̿������䥳��ԥ塼���ʤɤξ㳲�ˤ�륷���ƥ�����ǡ����ڡ���ߡ��ǡ����ξü����ǡ����ؤ��������������ˤ��������»��������¾���ҤΥ����ӥ��˴ؤ��Ʋ����������»���ˤĤ��ơ����Ҥϰ�����Ǥ�����ʤ���ΤȤ��ޤ���
2. ���Ҥϡ����ҤΥ����֥ڡ����������С��ɥᥤ��ʤɤ���������᡼�롦����ƥ�Ĥˡ�����ԥ塼���������륹�ʤɤ�ͭ���ʤ�Τ��ޤޤ�Ƥ��ʤ����Ȥ��ݾڤ������ޤ���
3. ������ܵ������˰�ȿ�������Ȥˤ�ä�������»���ˤĤ��Ƥϡ����Ҥϰ�����Ǥ���餤�ޤ���',
3,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('��11����ܵ���β����','���Ҥϡ��ܵ����Ǥ�դ˲���Ǥ����ΤȤ����ޤ������Ҥˤ������ܵ�����佼���뵬��ʰʲ����佼����פȤ����ޤ��ˤ����뤳�Ȥ��Ǥ��ޤ����ܵ���β���ޤ����佼�ϡ��������ܵ���ޤ����佼��������ҽ���Υ����Ȥ˷Ǽ������Ȥ��ˤ��θ��Ϥ��������ΤȤ��ޤ������ξ�硢����ϡ������ε��󤪤���佼����˽�����Τ��פ��ޤ���',
2,0,Now(),0);

INSERT INTO dtb_kiyaku (kiyaku_title, kiyaku_text, rank, creator_id, update_date, delete) 
VALUES ('��12��ʽ��ˡ���ɳ��Ƚ���','�ܵ���˴ؤ���ʶ�褬��������硢������Ź����Ϥ�ɳ���������Ƚ�����쿳����°Ū��մɳ��Ƚ��Ȥ��ޤ��� ',
1,0,Now(),0);


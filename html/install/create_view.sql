CREATE VIEW vw_cross_class as 
SELECT T1.class_id AS class_id1, T2.class_id AS class_id2, T1.classcategory_id AS classcategory_id1, T2.classcategory_id AS classcategory_id2, T1.name AS name1, T2.name AS name2, T1.rank AS rank1, T2.rank AS rank2
FROM dtb_classcategory AS T1, dtb_classcategory AS T2;

CREATE VIEW vw_cross_products_class AS
SELECT T1.class_id1, T1.class_id2, T1.classcategory_id1, T1.classcategory_id2, T2.product_id,
T1.name1, T1.name2, T2.product_code, T2.stock, T2.price01, T2.price02, T1.rank1, T1.rank2
FROM vw_cross_class AS T1 LEFT JOIN dtb_products_class AS T2 
ON T1.classcategory_id1 = T2.classcategory_id1 AND T1.classcategory_id2 = T2.classcategory_id2;

CREATE VIEW vw_products_nonclass AS 
SELECT * FROM dtb_products AS T1 LEFT JOIN 
(SELECT
product_id AS product_id_sub,
product_code,
price01,
price02,
stock,
stock_unlimited,
classcategory_id1,
classcategory_id2
FROM dtb_products_class WHERE classcategory_id1 = 0 AND classcategory_id2 = 0) 
AS T2
ON T1.product_id = T2.product_id_sub;

CREATE VIEW vw_products_allclass AS 
SELECT product_id,product_code_min,product_code_max,price01_min,price01_max,price02_min,price02_max,stock_min,stock_max,stock_unlimited_min,stock_unlimited_max,
category_rank,category_id,delete,status,name,comment1,comment2,comment3,rank,
main_list_comment,main_image,main_list_image,product_flag,deliv_date_id,sale_limit,point_rate,sale_unlimited,create_date,deliv_fee
FROM (dtb_products AS T1 RIGHT JOIN 
(SELECT 
product_id AS product_id_sub,
MIN(product_code) AS product_code_min,
MAX(product_code) AS product_code_max,
MIN(price01) AS price01_min,
MAX(price01) AS price01_max,
MIN(price02) AS price02_min,
MAX(price02) AS price02_max,
MIN(stock) AS stock_min,
MAX(stock) AS stock_max,
MIN(stock_unlimited) AS stock_unlimited_min,
MAX(stock_unlimited) AS stock_unlimited_max
FROM dtb_products_class GROUP BY product_id) AS T2
ON T1.product_id = T2.product_id_sub) AS T3 LEFT JOIN (SELECT rank AS category_rank, category_id AS sub_category_id FROM dtb_category) AS T4
ON T3.category_id = T4.sub_category_id;

CREATE VIEW vw_products_allclass_detail AS 
SELECT product_id,price01_min,price01_max,price02_min,price02_max,stock_min,stock_max,stock_unlimited_min,stock_unlimited_max,
delete,status,name,comment1,comment2,comment3,deliv_fee,main_comment,main_image,main_large_image,
sub_title1,sub_comment1,sub_image1,sub_large_image1,
sub_title2,sub_comment2,sub_image2,sub_large_image2,
sub_title3,sub_comment3,sub_image3,sub_large_image3,
sub_title4,sub_comment4,sub_image4,sub_large_image4,
sub_title5,sub_comment5,sub_image5,sub_large_image5,
product_flag,deliv_date_id,sale_limit,point_rate,sale_unlimited,file1,file2,category_id
FROM (dtb_products AS T1 RIGHT JOIN 
(SELECT 
product_id AS product_id_sub,
MIN(price01) AS price01_min,
MAX(price01) AS price01_max,
MIN(price02) AS price02_min,
MAX(price02) AS price02_max,
MIN(stock) AS stock_min,
MAX(stock) AS stock_max,
MIN(stock_unlimited) AS stock_unlimited_min,
MAX(stock_unlimited) AS stock_unlimited_max
FROM dtb_products_class GROUP BY product_id) AS T2
ON T1.product_id = T2.product_id_sub) AS T3 LEFT JOIN (SELECT rank AS category_rank, category_id AS sub_category_id FROM dtb_category) AS T4
ON T3.category_id = T4.sub_category_id;

CREATE VIEW vw_product_class AS
SELECT * FROM 
(SELECT T3.product_class_id, T3.product_id AS product_id_sub, classcategory_id1, classcategory_id2, 
T3.rank AS rank1, T4.rank AS rank2, T3.class_id AS class_id1, T4.class_id AS class_id2,
stock, price01, price02, stock_unlimited, product_code
FROM (dtb_products_class AS T1 LEFT JOIN dtb_classcategory AS T2
ON T1.classcategory_id1 = T2.classcategory_id)
AS T3 LEFT JOIN dtb_classcategory AS T4
ON T3.classcategory_id2 = T4.classcategory_id) AS T5 LEFT JOIN dtb_products AS T6
ON product_id_sub = T6.product_id;

CREATE VIEW vw_category_count AS
SELECT T1.category_id, T1.category_name, T1.parent_category_id, T1.level, T1.rank, T2.product_count
FROM dtb_category AS T1 LEFT JOIN dtb_category_total_count AS T2
ON T1.category_id = T2.category_id


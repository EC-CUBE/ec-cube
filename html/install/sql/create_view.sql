CREATE VIEW vw_cross_class as
    SELECT
        T1.class_id AS class_id1,
        T2.class_id AS class_id2,
        T1.classcategory_id AS classcategory_id1,
        T2.classcategory_id AS classcategory_id2,
        T1.name AS name1,
        T2.name AS name2,
        T1.rank AS rank1,
        T2.rank AS rank2
    FROM
        dtb_classcategory AS T1,
        dtb_classcategory AS T2
;

CREATE VIEW vw_cross_products_class AS
    SELECT
        T1.class_id1,
        T1.class_id2,
        T1.classcategory_id1,
        T1.classcategory_id2,
        T2.product_id,
        T1.name1,
        T1.name2,
        T2.product_code,
        T2.stock,
        T2.price01,
        T2.price02,
        T1.rank1,
        T1.rank2
    FROM
        vw_cross_class AS T1
        LEFT JOIN dtb_products_class AS T2
            ON T1.classcategory_id1 = T2.classcategory_id1
            AND T1.classcategory_id2 = T2.classcategory_id2
;

CREATE VIEW vw_products_nonclass AS
    SELECT *
    FROM
        dtb_products AS T1
        LEFT JOIN
        (
            SELECT
                product_id AS product_id_sub,
                product_code,
                price01,
                price02,
                stock,
                stock_unlimited,
                classcategory_id1,
                classcategory_id2
            FROM dtb_products_class
            WHERE
                classcategory_id1 = 0
                AND classcategory_id2 = 0
        ) AS T2
        ON T1.product_id = T2.product_id_sub
;

CREATE VIEW vw_products_allclass_detail AS
    SELECT
        dtb_products.product_id,
        dtb_products."name",
        dtb_products.deliv_fee,
        dtb_products.sale_limit,
        dtb_products.maker_id,
        dtb_products.rank,
        dtb_products.status,
        dtb_products.product_flag,
        dtb_products.point_rate,
        dtb_products.comment1,
        dtb_products.comment2,
        dtb_products.comment3,
        dtb_products.comment4,
        dtb_products.comment5,
        dtb_products.comment6,
        dtb_products.note,
        dtb_products.file1,
        dtb_products.file2,
        dtb_products.file3,
        dtb_products.file4,
        dtb_products.file5,
        dtb_products.file6,
        dtb_products.main_list_comment,
        dtb_products.main_list_image,
        dtb_products.main_comment,
        dtb_products.main_image,
        dtb_products.main_large_image,
        dtb_products.sub_title1,
        dtb_products.sub_comment1,
        dtb_products.sub_image1,
        dtb_products.sub_large_image1,
        dtb_products.sub_title2,
        dtb_products.sub_comment2,
        dtb_products.sub_image2,
        dtb_products.sub_large_image2,
        dtb_products.sub_title3,
        dtb_products.sub_comment3,
        dtb_products.sub_image3,
        dtb_products.sub_large_image3,
        dtb_products.sub_title4,
        dtb_products.sub_comment4,
        dtb_products.sub_image4,
        dtb_products.sub_large_image4,
        dtb_products.sub_title5,
        dtb_products.sub_comment5,
        dtb_products.sub_image5,
        dtb_products.sub_large_image5,
        dtb_products.sub_title6,
        dtb_products.sub_comment6,
        dtb_products.sub_image6,
        dtb_products.sub_large_image6,
        dtb_products.del_flg,
        dtb_products.creator_id,
        dtb_products.create_date,
        dtb_products.update_date,
        dtb_products.deliv_date_id,
        T4.product_code_min,
        T4.product_code_max,
        T4.price01_min,
        T4.price01_max,
        T4.price02_min,
        T4.price02_max,
        T4.stock_min,
        T4.stock_max,
        T4.stock_unlimited_min,
        T4.stock_unlimited_max,
        T4.class_count
    FROM
        dtb_products
        LEFT JOIN
            (
                SELECT
                    product_id,
                    MIN(product_code) AS product_code_min,
                    MAX(product_code) AS product_code_max,
                    MIN(price01) AS price01_min,
                    MAX(price01) AS price01_max,
                    MIN(price02) AS price02_min,
                    MAX(price02) AS price02_max,
                    MIN(stock) AS stock_min,
                    MAX(stock) AS stock_max,
                    MIN(stock_unlimited) AS stock_unlimited_min,
                    MAX(stock_unlimited) AS stock_unlimited_max,
                    COUNT(*) as class_count
                FROM dtb_products_class
                GROUP BY product_id
            ) AS T4
            ON dtb_products.product_id = T4.product_id
;

CREATE VIEW vw_products_allclass AS
    SELECT
        alldtl.*,
        dtb_category.rank AS category_rank,
        T2.category_id,
        T2.rank AS product_rank
    FROM
        vw_products_allclass_detail AS alldtl
        LEFT JOIN
            dtb_product_categories AS T2
            ON alldtl.product_id = T2.product_id
        LEFT JOIN
            dtb_category
            ON T2.category_id = dtb_category.category_id
;

CREATE VIEW vw_product_class AS
     SELECT *
       FROM
      (SELECT T3.product_class_id,
              T3.product_id AS product_id_sub,
              classcategory_id1,
              classcategory_id2,
              T3.rank AS rank1,
              T4.rank AS rank2,
              T3.class_id AS class_id1,
              T4.class_id AS class_id2,
              stock,
              price01,
              price02,
              stock_unlimited,
              product_code
         FROM (dtb_products_class AS T1 
    LEFT JOIN dtb_classcategory AS T2
           ON T1.classcategory_id1 = T2.classcategory_id) AS T3 
  LEFT JOIN dtb_classcategory AS T4
         ON T3.classcategory_id2 = T4.classcategory_id) AS T5 
  LEFT JOIN dtb_products AS T6
         ON product_id_sub = T6.product_id;

CREATE VIEW vw_category_count AS
     SELECT T1.category_id,
            T1.category_name,
            T1.parent_category_id,
            T1.level,
            T1.rank,
            T2.product_count
       FROM dtb_category AS T1 
  LEFT JOIN dtb_category_total_count AS T2
         ON T1.category_id = T2.category_id
;

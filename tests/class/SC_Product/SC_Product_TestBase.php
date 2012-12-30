<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Product_TestBase extends Common_TestCase {
    protected function setUp() {
        parent::setUp();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * DBに商品クラス情報を設定します.
     */
    protected function setUpProductClass() {
        $product_class = array(
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'product_class_id' => '1001',
                'product_id' => '1001',
                'product_type_id' => '1001',
                'product_code' => 'code1001',
                'classcategory_id1' => '1001',
                'classcategory_id2' => '1002',
                'price01' => '1500',
                'price02' => '1500',
                'creator_id' => '1',
                'del_flg' => '0'
                  ),
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'product_class_id' => '1002',
                'product_id' => '1002',
                'product_type_id' => '1002',
                'price02' => '2500',
                'creator_id' => '1',
                'del_flg' => '0'
                  )
                               );

        $this->objQuery->delete('dtb_products_class');
        foreach ($product_class as $key => $item) {
            $this->objQuery->insert('dtb_products_class', $item);
        }
        $this->setUpClassCategory();
        $this->setUpProducts();
    }

    /**
     * DBに製品カテゴリ情報を登録します.
     */
    protected function setUpClassCategory() {
        $class_category = array(
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'classcategory_id' => '1001',
                'class_id' => '1',
                'creator_id' => '1',
                'name' => 'cat1001'
                  ),
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'classcategory_id' => '1002',
                'class_id' => '1',
                'creator_id' => '1',
                'name' => 'cat1002'
                  )
                                );

        $this->objQuery->delete('dtb_classcategory');
        foreach ($class_category as $key => $item) {
            $this->objQuery->insert('dtb_classcategory', $item);
        }
    }

    /** 
     * DBに製品情報を登録します.
     */
    protected function setUpProducts() {
        $products = array(
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'product_id' => '1001',
                'name' => '製品名1001',
                'del_flg' => '0',
                'creator_id' => '1',
                'status' => '1'
                  ),
            array(
                'update_date' => 'CURRENT_TIMESTAMP',
                'product_id' => '1002',
                'name' => '製品名1002',
                'del_flg' => '0',
                'creator_id' => '1',
                'status' => '2'
                  )
                          );

        $this->objQuery->delete('dtb_products');
        foreach ($products as $key => $item) {
            $this->objQuery->insert('dtb_products', $item);
        }
    }
}
<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Page\Admin;

class ProductClassEditPage extends AbstractAdminPageStyleGuide
{
    public static $登録完了メッセージ = ['css' => '#page_admin_product_product_class > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3'];

    public static $初期化ボタン = ['css' => '#page_admin_product_product_class > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > div > div.card-header > div > div.col-4.text-right > button'];

    public static $規格一覧 = ['css' => '#page_admin_product_product_class > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table'];

    /**
     * ProductReProductClassEditPagegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new ProductClassEditPage($I);
        $page->atPage('商品規格登録商品管理');

        return $page;
    }

    public function 規格設定()
    {
        $this->tester->click(['css' => 'div.c-contentsArea form button']);

        return $this;
    }

    public function 入力_規格1($value)
    {
        $this->tester->selectOption(['id' => 'product_class_matrix_class_name1'], $value);

        return $this;
    }

    public function 入力_在庫数無制限($rowNum)
    {
        --$rowNum;
        $this->tester->checkOption(['id' => "product_class_matrix_product_classes_${rowNum}_stock_unlimited"]);

        return $this;
    }

    public function 入力_販売価格($rowNum, $value)
    {
        --$rowNum;
        $this->tester->fillField(['id' => "product_class_matrix_product_classes_${rowNum}_price02"], $value);

        return $this;
    }

    public function 選択($rowNum)
    {
        --$rowNum;
        $this->tester->checkOption(['id' => "product_class_matrix_product_classes_${rowNum}_checked"]);

        return $this;
    }

    public function 規格初期化()
    {
        $this->tester->click(self::$初期化ボタン);
        $this->tester->waitForElement(['css' => '#initializationConfirm > div > div > div.modal-footer > form > button']);
        $this->tester->wait(1);
        $this->tester->click(['css' => '#initializationConfirm > div > div > div.modal-footer > form > button']);

        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['css' => 'button[name="product_class_matrix[save]"]']);

        return $this;
    }
}

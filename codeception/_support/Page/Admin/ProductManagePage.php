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

/**
 * 商品管理/商品一覧
 */
class ProductManagePage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/product';

    public static $検索条件_プロダクト = ['id' => 'admin_search_product_id'];
    public static $検索ボタン = '#search_form .c-outsideBlock__contents button';
    public static $詳細検索ボタン = '//*[@id="search_form"]/div[1]/div/div/div[2]/a/span';
    public static $検索条件_在庫あり = ['id' => 'admin_search_product_stock_0'];
    public static $検索条件_在庫なし = ['id' => 'admin_search_product_stock_1'];
    public static $検索条件_入金済み = ['id' => 'admin_search_order_status_6'];
    public static $検索条件_新規受付 = ['id' => 'admin_search_order_status_1'];
    public static $検索条件_対応中 = ['id' => 'admin_search_order_status_4'];
    public static $検索結果_メッセージ = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
    public static $検索結果_結果なしメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索結果_エラーメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索結果_一覧 = '#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody';
    public static $検索結果_1行目_商品名 = ['css' => '#form_bulk table tbody > tr:nth-child(1) > td:nth-child(4)'];
    public static $一括削除エラー = ['id' => 'bulkErrors'];
    public static $アラートメッセージ = ['css' => '.c-contentsArea > .alert'];

    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * ProductListPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new ProductManagePage($I);

        return $page->goPage(self::$URL, '商品一覧商品管理');
    }

    /**
     * @param $second
     *
     * @return $this
     */
    public function wait($second = 3)
    {
        $this->tester->wait($second);

        return $this;
    }

    /**
     * 指定した商品名/ID/コードで検索する。
     *
     * @param string $product 商品名/ID/コード
     *
     * @return $this
     */
    public function 検索($product = '')
    {
        $this->tester->fillField(self::$検索条件_プロダクト, $product);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('商品一覧商品管理', '.c-pageTitle');

        return $this;
    }

    public function 詳細検索_ステータス($value)
    {
        $this->tester->click(self::$詳細検索ボタン);
        $this->tester->wait(1);
        $this->tester->checkOption(['id' => 'admin_search_product_status_'.$value]);
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('商品一覧商品管理', '.c-pageTitle');

        return $this;
    }

    public function 検索_入力_フリー検索($value)
    {
        $this->tester->fillField(self::$検索条件_プロダクト, $value);

        return $this;
    }

    public function 詳細検索_入力_タグ($value)
    {
        $this->tester->selectOption(['id' => 'admin_search_product_tag_id'], 1);

        return $this;
    }

    public function 詳細検索ボタンをクリック()
    {
        $this->tester->click(self::$詳細検索ボタン);

        return $this;
    }

    public function 検索を実行()
    {
        $this->tester->click(self::$検索ボタン);
        $this->tester->see('商品一覧商品管理', '.c-pageTitle');

        return $this;
    }

    /**
     * 検索結果の指定した行の規格設定に遷移。
     *
     * @param int $rowNum 検索結果の行番号(1から始まる)
     *
     * @return $this
     */
    public function 検索結果_規格設定($rowNum)
    {
        $this->tester->click("#main #result_list__list > div > div:nth-child(${rowNum}) > div:nth-child(4) > div > ul > li:nth-child(1) > a");
    }

    /**
     * 検索結果の指定した行の複製。
     *
     * @param int $rowNum 検索結果の行番号(1から始まる)
     *
     * @return $this
     */
    public function 検索結果_複製($rowNum)
    {
        $this->tester->click("#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(2) > a");

        return $this;
    }

    /**
     * 検索結果の指定した行の確認。
     *
     * @param int $rowNum 検索結果の行番号(1から始まる)
     *
     * @return $this
     */
    public function 検索結果_確認($rowNum)
    {
        $this->tester->click("#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(1) > a");

        return $this;
    }

    /**
     * 検索結果の指定した行を選択。
     *
     * @param int $rowNum 検索結果の行番号(1から始まる)
     *
     * @return $this
     */
    public function 検索結果_選択($rowNum)
    {
        $this->tester->click("#form_bulk > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(4) > a");

        return $this;
    }

    /**
     * Btn class product, list product search
     *
     * @param int $rowNum 検索結果の行番号(1から始まる)
     *
     * @return $this
     */
    public function 規格確認ボタンをクリック($rowNum)
    {
        $this->tester->click(['css' => "#ex-product-${rowNum} > td:nth-child(7) > button"]);
        $this->tester->waitForElementVisible(['id' => 'productClassesModal']);
        $this->tester->wait(1);

        return $this;
    }

    /**
     * Btn class product, list product search
     *
     * @return $this
     */
    public function 規格確認をキャンセル()
    {
        $this->tester->click('#page_admin_product div#productClassesModal .modal-footer button.btn-v-sub');
        $this->tester->waitForElementNotVisible(['id' => 'productClassesModal']);

        return $this;
    }

    /**
     * @return $this
     */
    public function 規格編集画面に遷移()
    {
        $this->tester->click('#page_admin_product div#productClassesModal .modal-footer a.btn-ec-conversion');

        return $this;
    }

    public function 検索結果_チェックボックスON($rowNum)
    {
        $this->tester->checkOption(['css' => "#form_bulk table tbody tr:nth-child($rowNum) input[type=checkbox]"]);
        $this->tester->waitForElementVisible('#btn_bulk');

        return $this;
    }

    public function 検索結果_削除()
    {
        $this->tester->click(['css' => '#btn_bulk button[data-target="#bulkDeleteModal"]']);
        $this->tester->wait(1);

        return $this;
    }

    public function 検索結果_廃止()
    {
        $this->tester->click(['css' => '#btn_bulk > button:nth-of-type(1)']);

        return $this;
    }

    public function Accept_複製する($rowNum)
    {
        $modalCssSelector = "#page_admin_product > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(2) div.modal";
        $this->tester->waitForElementVisible(['css' => $modalCssSelector]);
        $this->tester->click($modalCssSelector.' div.modal-footer a.btn-ec-conversion');

        return $this;
    }

    public function Accept_削除()
    {
        $this->tester->click('#bulkDelete');
        $this->tester->wait(3);

        return $this;
    }

    public function Cancel_削除($rowNum)
    {
        $this->tester->click('#bulkDeleteModal > div > div > div.modal-footer > button.btn.btn-ec-sub');

        return $this;
    }

    public function CSVダウンロード()
    {
        $this->tester->click('.c-contentsArea__cols .row div:nth-child(2) div:nth-child(2) a:nth-child(1)');

        return $this;
    }

    public function CSV出力項目設定()
    {
        $this->tester->click('.c-contentsArea__cols .row div:nth-child(2) div:nth-child(2) a:nth-child(2)');

        return $this;
    }

    public function CSVヘッダ取得()
    {
        $this->tester->wait(5);
        $csv = $this->tester->getLastDownloadFile('/^product_\d{14}\.csv$/');

        return mb_convert_encoding(file($csv)[0], 'UTF-8', 'SJIS-win');
    }

    public function すべて選択()
    {
        $this->tester->checkOption(['id' => 'trigger_check_all']);

        return $this;
    }

    public function 完全に削除()
    {
        $this->tester->click(['css' => '#form_bulk button.btn-ec-delete']);
        $this->tester->waitForElementVisible(['id' => 'bulkDelete']);
        $this->tester->click(['id' => 'bulkDelete']);
        $this->tester->waitForElementVisible(['id' => 'bulkDeleteDone'], 30);

        return $this;
    }

    public function 一括削除完了()
    {
        $this->tester->click(['id' => 'bulkDeleteDone']);

        return $this;
    }

    public function assertSortedList($index, $order)
    {
        $values = $this->tester->grabMultiple('.c-contentsArea__primaryCol tr > td:nth-child('.$index.')');

        $expect = $values;
        if ($order === 'asc') {
            sort($expect);
        } else {
            rsort($expect);
        }

        $this->tester->assertEquals($expect, $values);
    }
}

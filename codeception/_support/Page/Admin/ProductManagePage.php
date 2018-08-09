<?php

namespace Page\Admin;

/**
 * 商品管理/商品一覧
 * @package Page\Admin
 */
class ProductManagePage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/product';

    public static $検索条件_プロダクト = ['id' => 'admin_search_product_id'];
    public static $検索ボタン = '#search_form .c-outsideBlock__contents button';
    public static $詳細検索ボタン = '//*[@id="search_form"]/div[1]/div/div/div[2]/a/span';
    public static $検索結果_メッセージ = '#search_form > div.c-outsideBlock__contents.mb-5 > span';
    public static $検索結果_結果なしメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索結果_エラーメッセージ = '.c-contentsArea .c-contentsArea__cols div.text-center.h5';
    public static $検索結果_一覧 = "#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody";
    public static $一括削除エラー = ['id' => 'bulkErrors'];

    /** @var \AcceptanceTester $tester */
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
     * @return $this
     */
    public function wait($second = 3)
    {
        $this->tester->wait($second);
        return $this;
    }

    /**
     * 指定した商品名/ID/コードで検索する。
     * @param string $product 商品名/ID/コード
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
    /**
     * 検索結果の指定した行の規格設定に遷移。
     * @param int $rowNum 検索結果の行番号(1から始まる)
     * @return $this
     */
    public function 検索結果_規格設定($rowNum)
    {
        $this->tester->click("#main #result_list__list > div > div:nth-child(${rowNum}) > div:nth-child(4) > div > ul > li:nth-child(1) > a");
    }

    /**
     * 検索結果の指定した行の複製。
     * @param int $rowNum 検索結果の行番号(1から始まる)
     * @return $this
     */
    public function 検索結果_複製($rowNum)
    {
        $this->tester->click("#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(2) > a");
        return $this;
    }

    /**
     * 検索結果の指定した行の確認。
     * @param int $rowNum 検索結果の行番号(1から始まる)
     * @return $this
     */
    public function 検索結果_確認($rowNum)
    {
        $this->tester->click("#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(1) > a");
        return $this;
    }

    /**
     * 検索結果の指定した行を選択。
     * @param int $rowNum 検索結果の行番号(1から始まる)
     * @return $this
     */
    public function 検索結果_選択($rowNum)
    {
        $this->tester->click("#page_admin_product > div > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td:nth-child(4) > a");
        return $this;
    }

    /**
     * Btn class product, list product search
     * @param int $rowNum 検索結果の行番号(1から始まる)
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
     * @return $this
     */
    public function 規格確認をキャンセル()
    {
        $this->tester->click("#page_admin_product div#productClassesModal .modal-footer button.btn-v-sub");
        $this->tester->waitForElementNotVisible(['id' => 'productClassesModal']);
        return $this;
    }

    /**
     * @return $this
     */
    public function 規格編集画面に遷移()
    {
        $this->tester->click("#page_admin_product div#productClassesModal .modal-footer a.btn-ec-conversion");
        return $this;
    }

    /**
     * 検索結果の指定した行を削除。
     * @param int $rowNum 検索結果の行番号(1から始まる)
     * @return $this
     */
    public function 検索結果_削除($rowNum)
    {
        $this->tester->click("#page_admin_product > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(3) > a");
        return $this;
    }

    public function Accept_重複する($rowNum)
    {
        $modalCssSelector = "#page_admin_product > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(2) div.modal";
        $this->tester->waitForElementVisible(['css' => $modalCssSelector]);
        $this->tester->click($modalCssSelector." div.modal-footer a.btn-ec-delete");
        return $this;
    }

    public function Accept_削除($rowNum)
    {
        $this->tester->click("#page_admin_product > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(3) div.modal div.modal-footer a.btn-ec-delete");
        return $this;
    }

    public function Cancel_削除($rowNum)
    {
        $this->tester->click("#page_admin_product > div.c-container > div.c-contentsArea > div.c-contentsArea__cols > div > div > form > div.card.rounded.border-0.mb-4 > div.card-body.p-0 > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3 > div > div:nth-child(3) button.btn.btn-ec-sub");
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
        $this->tester->waitForElementVisible(['id' => 'bulkDeleteDone']);
        return $this;
    }

    public function 一括削除完了()
    {
        $this->tester->click(['id' => 'bulkDeleteDone']);
        return $this;
    }
}

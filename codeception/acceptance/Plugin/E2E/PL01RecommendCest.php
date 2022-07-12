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

namespace Plugin\E2E;

use AcceptanceTester;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL01RecommendCest
{
    /**
     * ⓪ 管理側にログイン
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * ① 初期状態ではおすすめ商品ブロックがフロントに表示されていないこと
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_1(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->dontSeeInSource('▼おすすめ商品');
    }

    /**
     * ② おすすめ商品ブロックをレイアウト編集画面で追加でき、フロントに表示されること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_2(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->dontSeeInSource('▼おすすめ商品');
        // Change setting of osusume plugin on layout area
        $I->amOnPage('/admin/content/layout/1/edit');
        // @todo: Improve this
        $I->see('おすすめ商品');
        $I->dragAndDrop('#detail_box__layout_item--21', '#position_7');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->see('保存しました');
        // Check HomePage
        $I->amOnPage('/');
        $I->seeInSource('▼おすすめ商品');
    }

    /**
     * ③ おすすめ商品ブロックを別の位置に移動させ、フロント画面に位置が反映されること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_3(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->dontSee('オススメ', '.ec-layoutRole__contentBottom');
        $I->see('オススメ', '.ec-layoutRole__contents');
        $I->amOnPage('/admin/content/layout/1/edit');
        $I->dragAndDrop('#detail_box__layout_item--21', '#position_9');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->see('保存しました');
        $I->amOnPage('/');
        $I->dontSee('オススメ', '.ec-layoutRole__contents');
        $I->see('オススメ', '.ec-layoutRole__contentBottom');
    }

    /**
     * ④ おすすめ商品を未使用ブロックに移動させ、フロント画面に表示されなくなること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_4(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('オススメ', '.ec-layoutRole__contentBottom');
        $I->amOnPage('/admin/content/layout/1/edit');
        $I->dragAndDrop('#detail_box__layout_item--21', '#position_0');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->see('保存しました');
        $I->amOnPage('/');
        $I->dontSee('オススメ');
        $I->dontSeeInSource('▼おすすめ商品');
    }

    /**
     * ⑤ おすすめ商品を登録でき、商品がフロント画面に反映されること
     * ⑥ おすすめ商品に説明文を登録でき、説明文がフロント画面に反映されること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_5_6(AcceptanceTester $I)
    {
        $this->recommend_2($I);
        $this->recommend_3($I);
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '.ec-layoutRole__footer');
        $I->amOnPage('/admin/plugin/recommend');
        // おすすめ商品を新規登録
        $I->clickWithLeftButton('.btn.btn-ec-regular.pl-4.pr-4');
        $I->see('おすすめ商品情報');
        $I->clickWithLeftButton('#showSearchProductModal');
        $I->see('商品検索');
        $I->fillField('#admin_search_product_id', 'チェリーアイスサンド');
        $I->clickWithLeftButton('#searchProductModalButton');
        $I->wait(5);
        $I->see('sand-01');
        $I->clickWithLeftButton('.btn.btn-default.btn-sm');
        $I->fillField('#recommend_product_comment', '説明分テスト');
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('おすすめ商品を登録しました。');
        $I->see('sand-01');
        $I->see('説明分テスト');
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('チェリーアイスサンド', '.ec-layoutRole__contentBottom');
        $I->see('説明分テスト', '.ec-layoutRole__contentBottom');
    }

    /**
     * ⑦ おすすめ商品の並び替えができ、並び順がフロント画面に反映されること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_7(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->see('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[2]');
        // おすすめ商品順番更新
        $I->amOnPage('/admin/plugin/recommend');
        $I->see('チェリーアイスサンド');
        $I->see('彩のジェラートCUBE');
        $I->dragAndDropByXPath('(//li[@class="list-group-item sortable-item ui-sortable-handle"])[1]', 174, 250, 5);
        $I->wait(5);
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        $I->see('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑧ おすすめ商品を削除でき、フロント画面に反映されること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_8(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        // おすすめ商品削除
        $I->amOnPage('/admin/plugin/recommend');
        $I->clickWithLeftButton('(//i[@class="fa fa-close fa-lg text-secondary"])[2]');
        $I->wait(5);
        $I->see('削除', '.modal-footer');
        $I->clickWithLeftButton('(//a[@class="btn btn-ec-delete"])[2]');
        $I->see(' おすすめ商品を削除しました。');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        $I->see('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑨ おすすめ商品のリンクが遷移できること
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_9(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        // おすすめ商品の修正
        $I->amOnPage('/admin/plugin/recommend');
        $I->see('彩のジェラートCUBE');
        $I->clickWithLeftButton('(//a[@class="btn btn-ec-actionIcon mr-3 action-edit"])[1]');
        $I->see('おすすめ商品情報');
        $I->clickWithLeftButton('#showSearchProductModal');
        $I->see('商品検索');
        $I->fillField('#admin_search_product_id', 'チェリーアイスサンド');
        $I->clickWithLeftButton('#searchProductModalButton');
        $I->wait(5);
        $I->see('sand-01');
        $I->clickWithLeftButton('.btn.btn-default.btn-sm');
        $I->see('sand-01');
        $I->dontSee('cube-01 ～ cube-09');
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('おすすめ商品を修正しました。');
        $I->see('チェリーアイスサンド');
        $I->dontSee('彩のジェラートCUBE');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->see('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑩ 無効化できる
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_10(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->see('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->see('おすすめ商品管理プラグイン', '(//tbody//tr)[1]');
        $I->see('有効', '(//tbody//tr)[1]');
        $I->clickWithLeftButton('(//i[@class="fa fa-pause fa-lg text-secondary"])[1]');
        $I->see('「おすすめ商品管理プラグイン」を無効にしました。');
        $I->see('おすすめ商品管理プラグイン', '(//tbody//tr)[1]');
        $I->see('無効', '(//tbody//tr)[1]');
        // プラグインのおすすめ商品管理リンクが消えているかどうかをチェック
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[5]');
        $I->wait(2);
        $I->dontSee('おすすめ管理', '(//li[@class="c-mainNavArea__navItem"])[5]');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑪ 有効化できる
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_11(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $I->see('おすすめ商品管理プラグイン', '(//tbody//tr)[1]');
        $I->see('無効', '(//tbody//tr)[1]');
        $I->clickWithLeftButton('(//i[@class="fa fa-play fa-lg text-secondary"])[1]');
        $I->see('「おすすめ商品管理プラグイン」を有効にしました。');
        $I->see('おすすめ商品管理プラグイン', '(//tbody//tr)[1]');
        $I->see('有効', '(//tbody//tr)[1]');
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[5]');
        $I->wait(2);
        $I->see('おすすめ管理', '(//li[@class="c-mainNavArea__navItem"])[5]');
    }

    /**
     * ⑫ アンインストールできる
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function recommend_12(AcceptanceTester $I)
    {
        // プラグインを無効する
        $this->recommend_10($I);
        // プラグイン削除
        $I->see('おすすめ商品管理プラグイン', '(//tbody//tr)[1]');
        $I->see('無効', '(//tbody//tr)[1]');
        $I->clickWithLeftButton('(//i[@class="fa fa-close fa-lg text-secondary"])[1]');
        $I->wait(2);
        $I->see('プラグインの削除を確認する');
        $I->clickWithLeftButton('#officialPluginDeleteButton');
        $I->wait(2);
        $I->performOn('#deleteLogPane', ['click' => '(//button[@class="btn btn-ec-sub"])[2]'], 300);
    }
}

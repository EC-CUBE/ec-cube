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
use Codeception\Util\Fixtures;
use Codeception\Util\Locator;
use Doctrine\ORM\EntityManager;
use Plugin\Recommend42\Entity\RecommendProduct;

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
     * @group install
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function recommend_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('おすすめ商品管理プラグイン', true)) {
            $I->wantToUninstallPlugin('おすすめ商品管理プラグイン');
            $I->seePluginIsNotInstalled('おすすめ商品管理プラグイン');
        }
        $I->wantToInstallPlugin('おすすめ商品管理プラグイン');
        $I->seePluginIsInstalled('おすすめ商品管理プラグイン');
    }

    /**
     * ⑪ 有効化できる
     *
     * @param AcceptanceTester $I
     * @group install
     * @return void
     */
    public function recommend_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'おすすめ商品');
        $I->seeInSource('おすすめ商品管理プラグイン', $recommendPluginRow);
        $I->seeInSource('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'おすすめ商品')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->seeInSource('「おすすめ商品管理プラグイン」を有効にしました。');
        $I->seeInSource('おすすめ商品管理プラグイン', $recommendPluginRow);
        $I->seeInSource('有効', $recommendPluginRow);
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[5]');
        $I->wait(2);
        $I->seeInSource('おすすめ管理', '(//li[@class="c-mainNavArea__navItem"])[5]');
    }

    /**
     * ① 初期状態ではおすすめ商品ブロックがフロントに表示されていないこと
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_03(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->dontSeeInSource('▼おすすめ商品');
    }

    /**
     * ② おすすめ商品ブロックをレイアウト編集画面で追加でき、フロントに表示されること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_04(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->dontSeeInSource('▼おすすめ商品');
        // Change setting of osusume plugin on layout area
        $I->amOnPage('/admin/content/layout/1/edit');
        // @todo: Improve this
        $I->seeInSource('おすすめ商品');
        $recommendBlock = Locator::contains('.block.sort.border.border-ec-gray.bg-ec-lightGray.p-2.mb-2.ui-sortable-handle', 'おすすめ商品');
        $I->dragAndDrop($recommendBlock, '#position_7');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->seeInSource('保存しました');
        // Check HomePage
        $I->amOnPage('/');
        $I->seeInSource('▼おすすめ商品');
    }

    /**
     * ③ おすすめ商品ブロックを別の位置に移動させ、フロント画面に位置が反映されること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_05(AcceptanceTester $I)
    {
        if (count($I->grabEntitiesFromRepository(RecommendProduct::class)) === 0) {
            /**
             * @var EntityManager $em
             */
            $entityManager = Fixtures::get('entityManager');
            $recommendProduct = new RecommendProduct();
            $recommendProduct->setProduct($entityManager->getRepository('Eccube\Entity\Product')->find(1));
            $recommendProduct->setComment('オススメ');
            $recommendProduct->setSortno(1);
            $recommendProduct->setVisible(1);
            $entityManager->persist($recommendProduct);
            $entityManager->flush();
        }
        $I->amOnPage('/');
        $I->dontSee('オススメ', '.ec-layoutRole__contentBottom');
        $I->seeInSource('オススメ', '.ec-layoutRole__contents');
        $I->amOnPage('/admin/content/layout/1/edit');
        // @todo: Register recommend item
        $recommendBlock = Locator::contains('.block.sort.border.border-ec-gray.bg-ec-lightGray.p-2.mb-2.ui-sortable-handle', 'おすすめ商品');
        $I->dragAndDrop($recommendBlock, '#position_9');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->seeInSource('保存しました');
        $I->amOnPage('/');
        $I->dontSee('オススメ', '.ec-layoutRole__contents');
        $I->seeInSource('オススメ', '.ec-layoutRole__contentBottom');
    }

    /**
     * ④ おすすめ商品を未使用ブロックに移動させ、フロント画面に表示されなくなること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_06(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeInSource('オススメ', '.ec-layoutRole__contentBottom');
        $I->amOnPage('/admin/content/layout/1/edit');
        $recommendBlock = Locator::contains('.block.sort.border.border-ec-gray.bg-ec-lightGray.p-2.mb-2.ui-sortable-handle', 'おすすめ商品');
        $I->dragAndDrop($recommendBlock, '#position_0');
        $I->clickWithLeftButton('#ex-conversion-action button');
        $I->seeInSource('保存しました');
        $I->amOnPage('/');
        $I->dontSee('オススメ');
        $I->dontSeeInSource('▼おすすめ商品');
    }

    /**
     * ⑤ おすすめ商品を登録でき、商品がフロント画面に反映されること
     * ⑥ おすすめ商品に説明文を登録でき、説明文がフロント画面に反映されること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_07(AcceptanceTester $I)
    {
        $this->recommend_04($I);
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '.ec-layoutRole__footer');
        $I->amOnPage('/admin/plugin/recommend');
        // おすすめ商品を新規登録
        $I->clickWithLeftButton('.btn.btn-ec-regular.pl-4.pr-4');
        $I->seeInSource('おすすめ商品情報');
        $I->clickWithLeftButton('#showSearchProductModal');
        $I->seeInSource('商品検索');
        $I->fillField('#admin_search_product_id', 'チェリーアイスサンド');
        $I->clickWithLeftButton('#searchProductModalButton');
        $I->wait(5);
        $I->seeInSource('sand-01');
        $I->clickWithLeftButton('.btn.btn-default.btn-sm');
        $I->fillField('#recommend_product_comment', '説明分テスト');
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->retrySee('おすすめ商品を登録しました。');
        $I->seeInSource('sand-01');
        $I->seeInSource('説明分テスト');
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('チェリーアイスサンド', '.ec-layoutRole__mainBottom');
        $I->seeInSource('説明分テスト', '.ec-layoutRole__mainBottom');
    }

    /**
     * ⑦ おすすめ商品の並び替えができ、並び順がフロント画面に反映されること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_08(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->seeInSource('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[2]');
        // おすすめ商品順番更新
        $I->amOnPage('/admin/plugin/recommend');
        $I->seeInSource('チェリーアイスサンド');
        $I->seeInSource('彩のジェラートCUBE');
        $I->dragAndDropByXPath('(//li[@class="list-group-item sortable-item ui-sortable-handle"])[1]', 174, 250, 5);
        $I->wait(5);
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        $I->seeInSource('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑧ おすすめ商品を削除でき、フロント画面に反映されること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_09(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        // おすすめ商品削除
        $I->amOnPage('/admin/plugin/recommend');
        $I->clickWithLeftButton('(//i[@class="fa fa-close fa-lg text-secondary"])[2]');
        $I->wait(5);
        $I->seeInSource('削除', '.modal-footer');
        $I->clickWithLeftButton('(//a[@class="btn btn-ec-delete"])[2]');
        $I->seeInSource(' おすすめ商品を削除しました。');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[2]');
        $I->seeInSource('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑨ おすすめ商品のリンクが遷移できること
     *
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function recommend_10(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        // おすすめ商品の修正
        $I->amOnPage('/admin/plugin/recommend');
        $I->seeInSource('彩のジェラートCUBE');
        $I->clickWithLeftButton('(//a[@class="btn btn-ec-actionIcon me-3 action-edit"])[1]');
        $I->seeInSource('おすすめ商品情報');
        $I->clickWithLeftButton('#showSearchProductModal');
        $I->seeInSource('商品検索');
        $I->fillField('#admin_search_product_id', 'チェリーアイスサンド');
        $I->clickWithLeftButton('#searchProductModalButton');
        $I->wait(5);
        $I->seeInSource('sand-01');
        $I->clickWithLeftButton('.btn.btn-default.btn-sm');
        $I->seeInSource('sand-01');
        $I->dontSee('cube-01 ～ cube-09');
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->seeInSource('おすすめ商品を修正しました。');
        $I->seeInSource('チェリーアイスサンド');
        $I->dontSee('彩のジェラートCUBE');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('彩のジェラートCUBE', '(//li[@class="ec-shelfGrid__item"])[1]');
        $I->seeInSource('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑩ 無効化できる
     *
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function recommend_11(AcceptanceTester $I)
    {
        // フロント側チェック
        $I->amOnPage('/');
        $I->seeInSource('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
        // 無効処理
        $I->wantToDisablePlugin('おすすめ商品管理プラグイン');
        // プラグインのおすすめ商品管理リンクが消えているかどうかをチェック
        $I->clickWithLeftButton('(//li[@class="c-mainNavArea__navItem"])[5]');
        $I->wait(2);
        $I->dontSee('おすすめ管理', '(//li[@class="c-mainNavArea__navItem"])[5]');
        // フロント側チェック
        $I->amOnPage('/');
        $I->dontSee('チェリーアイスサンド', '(//li[@class="ec-shelfGrid__item"])[1]');
    }

    /**
     * ⑫ アンインストールできる
     * @group uninstall
     * @param AcceptanceTester $I
     * @return void
     */
    public function recommend_12(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallPlugin('おすすめ商品管理プラグイン');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', 'おすすめ商品管理プラグイン');
        $I->seeInSource('インストール', $xpath);
    }
}

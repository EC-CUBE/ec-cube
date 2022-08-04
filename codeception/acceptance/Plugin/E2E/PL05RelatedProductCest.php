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
use Eccube\Entity\Product;
use function PHPUnit\Framework\assertStringContainsString;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL05RelatedProductCest
{
    private Product $product;
    private Product $relatedProduct;

    public function _before(AcceptanceTester $I)
    {
        // Delete all cache as doctrine metadata is always in the way on plugin install.
        $I->willHardDeleteCache();

        $I->loginAsAdmin();
    }

    public function _after(AcceptanceTester $I)
    {
        // Delete all cache as doctrine metadata is always in the way on plugin install.
        $I->willHardDeleteCache();
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     *
     * @return void
     */
    public function related_01(AcceptanceTester $I)
    {
        if ($I->seePluginIsInstalled('関連商品プラグイン', true)) {
            $I->wantToUninstallPlugin('関連商品プラグイン');
            $I->seePluginIsNotInstalled('関連商品プラグイン');
        }
        $I->wantToInstallPlugin('関連商品プラグイン');
        $I->seePluginIsInstalled('関連商品プラグイン');
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     */
    public function related_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $couponRow = Locator::contains('//tr', '関連商品プラグイン');
        $I->see('関連商品プラグイン', $couponRow);
        $I->see('無効', $couponRow);
        $I->clickWithLeftButton("(//tr[contains(.,'関連商品プラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「関連商品プラグイン」を有効にしました。');
        $I->see('関連商品プラグイン', $couponRow);
        $I->see('有効', $couponRow);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function related_03(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $Product = $this->registerBaseProductDetails();
        $RelatedProduct = $this->registerBaseProductDetails();
        $this->product = $Product;
        $this->relatedProduct = $RelatedProduct;
        $I->amOnPage(sprintf('/admin/product/product/%s/edit', $Product->getId()));
        $I->see('関連商品');
        // Wait for javascript to load.
        $I->wait(10);
        $I->clickWithLeftButton('#RelatedProduct-search0');
        $I->retryFillField('#admin_search_product_id', $RelatedProduct->getId());
        $I->clickWithLeftButton('#RelatedProductSearchButton');
        $I->retrySee($RelatedProduct->getName());
        $I->clickWithLeftButton(Locator::contains('//tr', $RelatedProduct->getName()).'//button');
        $I->retrySee($RelatedProduct->getName());
        $I->wait(5);
        $I->retryClickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('保存しました');
        $I->see($RelatedProduct->getName());
        // フロント側
        $I->amOnPage(sprintf('/products/detail/%s', $Product->getId()));
        $I->see($Product->getName());
        $I->see($RelatedProduct->getName());
        $I->seeInSource($RelatedProduct->getMainListImage());
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function related_04(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $Product = $this->registerBaseProductDetails();
        $RelatedProduct = $this->registerBaseProductDetails();
        $identityID = bin2hex(random_bytes(20));
        $I->amOnPage(sprintf('/admin/product/product/%s/edit', $Product->getId()));
        $I->see('関連商品');
        // Wait for javascript to load.
        $I->wait(10);
        $I->clickWithLeftButton('#RelatedProduct-search0');
        $I->retryFillField('#admin_search_product_id', $RelatedProduct->getId());
        $I->clickWithLeftButton('#RelatedProductSearchButton');
        $I->retrySee($RelatedProduct->getName());
        $I->clickWithLeftButton(Locator::contains('//tr', $RelatedProduct->getName()).'//button');
        $I->retrySee($RelatedProduct->getName());
        $I->wait(5);
        $I->fillField('#admin_product_RelatedProducts_0_content', $identityID);
        $I->retryClickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('保存しました');
        $I->see($RelatedProduct->getName());
        // フロント側
        $I->amOnPage(sprintf('/products/detail/%s', $Product->getId()));
        $I->see($Product->getName());
        $I->see($RelatedProduct->getName());
        $I->see($identityID);
        $I->seeInSource($RelatedProduct->getMainListImage());
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function related_05(AcceptanceTester $I)
    {
        $I->retry(5, 200);
        $Product = $this->registerBaseProductDetails();
        $RelatedProducts = [];
        $RelatedProductsDescriptionIDs = [];
        for ($i = 0; $i < 6; $i++) {
            $RelatedProducts[] = $this->registerBaseProductDetails();
            $RelatedProductsDescriptionIDs[] = bin2hex(random_bytes(20));
        }

        $I->amOnPage(sprintf('/admin/product/product/%s/edit', $Product->getId()));
        $I->see('関連商品');
        // Wait for javascript to load.
        $I->wait(10);

        foreach ($RelatedProducts as $key => $relatedProduct) {
            $I->retryScrollTo('#RelatedProduct-search'.$key);
            $I->wait(10);
            $I->retryClickWithLeftButton('#RelatedProduct-search'.$key);
            $I->wait(10);
            $I->retryFillField('#admin_search_product_id', $relatedProduct->getId());
            $I->wait(10);
            $I->clickWithLeftButton('#RelatedProductSearchButton');
            $I->wait(10);
            $I->retrySee($relatedProduct->getName());
            $I->wait(10);
            $I->clickWithLeftButton(Locator::contains('//tr', $relatedProduct->getName()).'//button');
            $I->wait(10);
            $I->retrySee($relatedProduct->getName());
            $I->wait(10);
            $I->fillField('#admin_product_RelatedProducts_'.$key.'_content', $RelatedProductsDescriptionIDs[$key]);
        }
        $I->retryClickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('保存しました');
        foreach ($RelatedProducts as $key => $relatedProduct) {
            $I->see($relatedProduct->getName());
            $I->see($RelatedProductsDescriptionIDs[$key]);
        }
        // フロント側
        $I->amOnPage(sprintf('/products/detail/%s', $Product->getId()));
        $I->see($Product->getName());
        foreach ($RelatedProducts as $key => $relatedProduct) {
            $I->see($relatedProduct->getName());
            $I->see($RelatedProductsDescriptionIDs[$key]);
            $I->seeInSource($relatedProduct->getMainListImage());
        }
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function related_06(AcceptanceTester $I)
    {

        $this->related_03($I);
        $I->amOnPage(sprintf('/admin/product/product/%s/edit', $this->product->getId()));
        $I->see('関連商品');
        // Wait for javascript to load.
        $I->wait(10);
        $I->see($this->relatedProduct->getName());
        $I->clickWithLeftButton('#RelatedProduct-delete0');
        $I->wait(1);
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->dontSee($this->relatedProduct->getName());
        // フロント側
        $I->amOnPage(sprintf('/products/detail/%s', $this->product->getId()));
        $I->dontSee($this->relatedProduct->getName());
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function related_07(AcceptanceTester $I)
    {
        $this->related_03($I);
        $I->clickWithLeftButton('(//div[@id="RelatedProduct-product_area"]//a)[1]');
        $I->wait(5);
        assertStringContainsString(sprintf('/products/detail/%s', $this->relatedProduct->getId()), $I->grabFromCurrentUrl());
        $I->see($this->relatedProduct->getName());
        $I->seeInSource($this->relatedProduct->getMainListImage());
        $I->see($this->relatedProduct->getDescriptionDetail());
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function related_08(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '関連商品プラグイン');
        $I->see('関連商品プラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'関連商品プラグイン')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「関連商品プラグイン」を無効にしました。');
        $I->see('関連商品プラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     * @throws \Exception
     */
    public function related_09(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallPlugin('関連商品プラグイン');
        // プラグインの状態を確認する
        $xpath = Locator::contains('tr', '関連商品プラグイン');
        $I->see('インストール', $xpath);
    }

    /**
     * @param AcceptanceTester $I
     *
     * @return Product
     */
    private function registerBaseProductDetails()
    {
        $createProduct = Fixtures::get('createProduct');
        /* @var Product $product */
        return $createProduct();
    }
}

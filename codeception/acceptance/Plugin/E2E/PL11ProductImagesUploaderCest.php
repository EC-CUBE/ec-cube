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
use Codeception\Util\Locator;
use function PHPUnit\Framework\assertFileExists;

/**
 * @group plugin
 * @group e2e_plugin
 */
class PL11ProductImagesUploaderCest
{
    /**
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     */
    public function images_uploader_01(AcceptanceTester $I)
    {
        // Not available on the owners store list so get from githubs latest release.
        $I->wantToInstallPluginLocally('product_images.tar.gz');
        $I->seePluginIsInstalled('商品画像一括アップロードプラグイン');
    }

    /**
     * @param AcceptanceTester $I
     * @group install
     * @return void
     */
    public function images_uploader_02(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '商品画像一括アップロードプラグイン');
        $I->see('商品画像一括アップロードプラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'商品画像一括アップロードプラグイン')]//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「商品画像一括アップロードプラグイン」を有効にしました。');
        $I->see('商品画像一括アップロードプラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     * @group main
     * @return void
     */
    public function images_uploader_03(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/product_images_uploader/config');
        $I->see('商品画像一括アップロードプラグイン');
        $I->see('商品画像');
        $I->attachFile('#config_image_file', 'plugins/e2e/PL11ProductImagesUploaderCest/cakes.zip');
        $I->clickWithLeftButton('.btn.btn-ec-conversion.px-5.ladda-button');
        $I->see('ファイルをアップロードしました。');
        assertFileExists(__DIR__.'/../../../../html/upload/save_image/muffin.jpg');
        assertFileExists(__DIR__.'/../../../../html/upload/save_image/blueberry.jpg');
        assertFileExists(__DIR__.'/../../../../html/upload/save_image/tart.jpg');
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function images_uploader_04(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', '商品画像一括アップロードプラグイン');
        $I->see('商品画像一括アップロードプラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(//tr[contains(.,'商品画像一括アップロードプラグイン')]//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「商品画像一括アップロードプラグイン」を無効にしました。');
        $I->see('商品画像一括アップロードプラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    /**
     * @param AcceptanceTester $I
     * @group uninstall
     * @return void
     */
    public function images_uploader_05(AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallLocalPlugin('商品画像一括アップロードプラグイン');
        $I->dontSee('商品画像一括アップロードプラグイン');
    }
}

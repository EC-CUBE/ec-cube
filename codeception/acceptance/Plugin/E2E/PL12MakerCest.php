<?php

namespace acceptance\Plugin\E2E;

use AcceptanceTester;
use Codeception\Util\Locator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Plugin\Maker42\Entity\Maker;

class PL12MakerCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * @group install
     * @param \AcceptanceTester $I
     * @return void
     */
    public function maker_01(\AcceptanceTester $I)
    {
        // Not available on the owners store list so get from githubs latest release.
        $I->wantToInstallPluginLocally('maker.tar.gz');
        $I->seePluginIsInstalled('Maker42');
    }

    /**
     * @group install
     * @param \AcceptanceTester $I
     * @return void
     */
    public function maker_02(\AcceptanceTester $I)
    {
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メーカー管理プラグイン');
        $I->see('メーカー管理プラグイン');
        $I->see('無効', $recommendPluginRow);
        $I->clickWithLeftButton("(" . $recommendPluginRow . "//i[@class='fa fa-play fa-lg text-secondary'])[1]");
        $I->see('「メーカー管理プラグイン」を有効にしました。');
        $I->see('メーカー管理プラグイン', $recommendPluginRow);
        $I->see('有効', $recommendPluginRow);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_03(AcceptanceTester $I)
    {
        $faker = \Faker\Factory::create('ja_JP');
        $brandName = $faker->company . bin2hex(random_bytes(10));;
        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName);
        $I->waitForText('メーカーを保存しました。', 20);
        $I->see($brandName);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_04(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $brandName = $faker->unique()->company .  bin2hex(random_bytes(10));
        $newBrandName = $faker->unique()->company .  bin2hex(random_bytes(10));

        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName);
        $I->waitForText('メーカーを保存しました。', 20);
        $makerRow = Locator::contains('//li', $brandName);
        $I->click($makerRow . '//i[@class="fa fa-pencil fa-lg text-secondary"]');
        $I->retrySeeInField($makerRow . '//input', $brandName);

        $I->retryFillField($makerRow . '//input[@type="text"]', $newBrandName);
        $I->click(Locator::contains($makerRow . '//button', '保存'));
        $I->waitForText('メーカーを保存しました。', 20);
        $I->see($newBrandName);

        $I->seeInRepository('Plugin\Maker42\Entity\Maker', ['name' => $newBrandName]);
        $I->dontSeeInRepository('Plugin\Maker42\Entity\Maker', ['name' => $brandName]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_05(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $brandName = $faker->unique()->company .  bin2hex(random_bytes(10));

        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName);
        $I->waitForText('メーカーを保存しました。', 20);
        $makerRow = Locator::contains('//li', $brandName);
        $I->click($makerRow . '//i[@class="fa fa-close fa-lg text-secondary"]');
        $activeModal = '//div[@class="modal fade show"]';
        $I->retrySee('メーカー情報を削除してもよろしいですか？');
        $I->click($activeModal . Locator::contains('//a', '削除'));

        $I->retrySee('メーカーを削除しました。');
        $I->dontSee($brandName);
        $I->dontSeeInRepository('Plugin\Maker42\Entity\Maker', ['name' => $brandName]);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_06(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $brandName1 = $faker->unique()->company .  bin2hex(random_bytes(10));
        $brandName2 = $faker->unique()->company .  bin2hex(random_bytes(10));
        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName1);
        $this->registerMaker($I, $brandName2);
        $makerRow = Locator::contains('//li', $brandName1);
        $firstElement = Locator::firstElement('//li[@class="list-group-item sortable-item ui-sortable-handle"]');
        $I->see($brandName2, $firstElement);
        $I->see($brandName1);

        $I->dragAndDropByXPath($makerRow, 0, -20, 6);
        $I->wait(10);
        $I->amOnPage('admin/maker');
        $firstElement = Locator::firstElement('//li[@class="list-group-item sortable-item ui-sortable-handle"]');
        $I->see($brandName1, $firstElement);
        $I->see($brandName2);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_07(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $brandName = $faker->company . bin2hex(random_bytes(10));;
        $brandUrl = $faker->unique()->url;

        // Register

        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName);
        $I->waitForText('メーカーを保存しました。', 20);
        $I->see($brandName);

        // Back

        $I->amOnPage('/admin/product/product/1/edit');
        $I->see('商品登録');
        $I->selectOption('#admin_product_Maker', $brandName);
        $I->fillField('#admin_product_maker_url', $brandUrl);
        $I->click(Locator::contains('//button', '登録'));
        $I->retrySee('保存しました');
        $I->seeOptionIsSelected('#admin_product_Maker', $brandName);
        $I->seeInField('#admin_product_maker_url', $brandUrl);

        // Front

        $I->amOnPage('products/detail/1');
        $I->see($brandName);
        $I->seeInSource($brandUrl);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function maker_08(AcceptanceTester $I)
    {
        $I->retry(7, 400);
        $faker = \Faker\Factory::create('ja_JP');
        $brandName = $faker->company . bin2hex(random_bytes(10));;
        $brandUrl = $faker->unique()->url;

        // Register
        $I->amOnPage('admin/maker');
        $this->registerMaker($I, $brandName);
        $I->waitForText('メーカーを保存しました。', 20);
        $I->see($brandName);

        // Back
        $I->amOnPage('/admin/product/product/1/edit');
        $I->see('商品登録');
        $I->selectOption('#admin_product_Maker', "");
        $I->fillField('#admin_product_maker_url', "");
        $I->click(Locator::contains('//button', '登録'));
        $I->retrySee('保存しました');
        $I->seeOptionIsSelected('#admin_product_Maker', "");
        $I->seeInField('#admin_product_maker_url', "");

        // Front

        $I->amOnPage('products/detail/1');
        $I->dontSee($brandName);
        $I->dontSeeInSource($brandUrl);
    }

    /**
     * @group main
     * @param AcceptanceTester $I
     * @return void
     */
    public function maker_09(\AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $recommendPluginRow = Locator::contains('//tr', 'メーカー管理プラグイン');
        $I->see('メーカー管理プラグイン', "//tr[contains(.,'メーカー管理プラグイン')]");
        $I->see('有効', $recommendPluginRow);
        $I->clickWithLeftButton("(" . $recommendPluginRow . "//i[@class='fa fa-pause fa-lg text-secondary'])[1]");
        $I->see('「メーカー管理プラグイン」を無効にしました。');
        $I->see('メーカー管理プラグイン', $recommendPluginRow);
        $I->see('無効', $recommendPluginRow);
    }

    /**
     * @group main
     * @param \AcceptanceTester $I
     * @return void
     */
    public function maker_10(\AcceptanceTester $I)
    {
        // 無効処理
        $I->amOnPage('/admin/store/plugin');
        $I->retry(10, 1000);
        $I->wantToUninstallLocalPlugin('メーカー管理プラグイン');
        $I->dontSee('メーカー管理プラグイン');
    }

    private function registerMaker(AcceptanceTester $I, string $brandName)
    {
        $I->see('メーカー管理');
        $I->fillField('#maker_name', $brandName);
        $I->click(Locator::contains('//button', '新規作成'));
    }

}

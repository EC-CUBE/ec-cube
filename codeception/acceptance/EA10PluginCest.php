<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Page\Admin\PluginLocalInstallPage;
use Page\Admin\PluginManagePage;
use Page\Admin\PluginSearchPage;

class EA10PluginCest
{
    /** @var EntityManager */
    private $em;

    /** @var \Doctrine\DBAL\Connection */
    private $conn;

    /** @var PluginRepository */
    private $pluginRepository;

    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsAdmin();

        $this->em = Fixtures::get('entityManager');
        $this->conn = $this->em->getConnection();
        $this->pluginRepository = $this->em->getRepository(Plugin::class);
    }

    public function installFromStore(\AcceptanceTester $I)
    {
        /*
         * インストール
         */

        $ManagePage = PluginSearchPage::go($I)
            ->入手する('SamplePayment')
            ->インストール();

        $I->assertFalse($this->tableExists('plg_sample_payment_config'));
        $I->assertFalse($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $Plugin = $this->pluginRepository->findByCode('SamplePayment');
        $I->assertFalse($Plugin->isInitialized(), '初期化されていない');
        $I->assertFalse($Plugin->isEnabled(), '有効化されていない');

        /*
         * 有効化
         */
        $ManagePage->ストアプラグイン_有効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を有効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 無効化
         */
        $ManagePage->ストアプラグイン_無効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を無効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 再度有効化
         */
        $ManagePage->ストアプラグイン_有効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を有効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 再度無効化
         */
        $ManagePage->ストアプラグイン_無効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を無効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 削除
         */
        $ManagePage->ストアプラグイン_削除('SamplePayment');

        $I->assertFalse($this->tableExists('plg_sample_payment_config'));
        $I->assertFalse($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $Plugin = $this->pluginRepository->findByCode('SamplePayment');
        $I->assertNull($Plugin);
    }

    public function installFromLocal(\AcceptanceTester $I)
    {
        /*
         * インストール
         */
        $ManagePage = PluginLocalInstallPage::go($I)
            ->アップロード('SamplePayment-1.0.0-beta-1.tgz');

        $I->see('プラグインをインストールしました。', PluginManagePage::完了メーッセージ);

        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $Plugin = $this->pluginRepository->findByCode('SamplePayment');
        $I->assertTrue($Plugin->isInitialized(), '初期化されていない');
        $I->assertFalse($Plugin->isEnabled(), '有効化されていない');

        /*
         * 有効化
         */
        $ManagePage->独自プラグイン_有効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を有効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 無効化
         */
        $ManagePage->独自プラグイン_無効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を無効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 再度有効化
        */
        $ManagePage->独自プラグイン_有効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を有効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertTrue($Plugin->isEnabled(), '有効化されている');

        /*
         * 再度無効化
         */
        $ManagePage->独自プラグイン_無効化('SamplePayment');

        $I->see('「EC-CUBE Payment Sample Plugin」を無効にしました。', PluginManagePage::完了メーッセージ);
        $I->assertTrue($this->tableExists('plg_sample_payment_config'));
        $I->assertTrue($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $I->assertTrue($Plugin->isInitialized(), '初期化されている');
        $I->assertFalse($Plugin->isEnabled(), '無効化されている');

        /*
         * 削除
         */
        $ManagePage->独自プラグイン_削除('SamplePayment');

        $I->see('プラグインを削除しました。', PluginManagePage::完了メーッセージ);

        $I->assertFalse($this->tableExists('plg_sample_payment_config'));
        $I->assertFalse($this->columnExists('dtb_customer', 'sample_payment_cards'));

        $this->em->refresh($Plugin);
        $Plugin = $this->pluginRepository->findByCode('SamplePayment');
        $I->assertNull($Plugin);
    }

    private function tableExists($tableName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}';")->fetch()['count'] > 0;
    }

    private function columnExists($tableName, $columnName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
    }
}

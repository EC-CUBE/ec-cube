<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

use Codeception\Util\Fixtures;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Page\Admin\PluginManagePage;
use Page\Admin\PluginSearchPage;

class EA10OwnersStoreCest
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

    public function インストール(\AcceptanceTester $I) {

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

    private function tableExists($tableName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}';")->fetch()['count'] > 0;
    }

    private function columnExists($tableName, $columnName)
    {
        return $this->conn->executeQuery("SELECT count(*) AS count FROM information_schema.columns WHERE table_name = '${tableName}' AND column_name = '${columnName}';")->fetch()['count'] == 1;
    }
}
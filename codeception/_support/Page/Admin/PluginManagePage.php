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

namespace Page\Admin;

class PluginManagePage extends AbstractAdminPageStyleGuide
{
    const 完了メーッセージ = '#page_admin_store_plugin > div.c-container > div.c-contentsArea > div.alert.alert-dismissible.fade.show.m-3 > span';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function at($I)
    {
        $page = new self($I);

        return $page->atPage('インストールプラグイン一覧オーナーズストア');
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     */
    public function ストアプラグイン_有効化($pluginCode, $message = '有効にしました。')
    {
        $this->ストアプラグイン_ボタンクリック($pluginCode, '有効化');
        $this->tester->see($message, self::完了メーッセージ);

        return $this;
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     */
    public function ストアプラグイン_無効化($pluginCode, $message = '無効にしました。')
    {
        $this->ストアプラグイン_ボタンクリック($pluginCode, '無効化');
        $this->tester->see($message, self::完了メーッセージ);

        return $this;
    }

    /**
     * @param $pluginCode
     * @param string $message
     *
     * @return PluginManagePage
     *
     * @throws \Exception
     */
    public function ストアプラグイン_削除($pluginCode, $message = '削除が完了しました。')
    {
        $this->ストアプラグイン_ボタンクリック($pluginCode, '削除');
        $this->tester->waitForElementVisible(['id' => 'officialPluginDeleteButton']);
        $this->tester->click(['id' => 'officialPluginDeleteButton']);
        $this->tester->waitForElementVisible(['css' => '#officialPluginDeleteModal > div > div > div.modal-footer > button:nth-child(3)'], 30);
        $this->tester->see($message, ['css' => '#officialPluginDeleteModal > div > div > div.modal-body.text-left > p']);
        $this->tester->click(['css' => '#officialPluginDeleteModal > div > div > div.modal-footer > button:nth-child(3)']);

        return $this;
    }

    /**
     * @param $pluginCode
     *
     * @return PluginStoreUpgradePage
     */
    public function ストアプラグイン_アップデート($pluginCode)
    {
        echo $this->tester->grabTextFrom(['xpath' => '//*[@id="page_admin_store_plugin"]']);
        $this->tester->click(['xpath' => $this->ストアプラグイン_セレクタ($pluginCode).'/../../td[5]/a']);

        return PluginStoreUpgradePage::at($this->tester);
    }

    private function ストアプラグイン_ボタンクリック($pluginCode, $label)
    {
        $xpath = ['xpath' => $this->ストアプラグイン_セレクタ($pluginCode).'/../../td[6]//i[@data-original-title="'.$label.'"]/parent::node()'];
        $this->tester->click($xpath);

        return $this;
    }

    public function ストアプラグイン_セレクタ($pluginCode)
    {
        return '//*[@id="page_admin_store_plugin"]//div/h5[contains(text(), "オーナーズストアのプラグイン")]/../..//table/tbody//td[3]/p[contains(text(), "'.$pluginCode.'")]';
    }

    public function 独自プラグイン_有効化($pluginCode)
    {
        $this->独自プラグイン_ボタンクリック($pluginCode, '有効化');
        $this->tester->see('有効にしました。', self::完了メーッセージ);

        return $this;
    }

    public function 独自プラグイン_無効化($pluginCode)
    {
        $this->独自プラグイン_ボタンクリック($pluginCode, '無効化');
        $this->tester->see('無効にしました。', self::完了メーッセージ);

        return $this;
    }

    public function 独自プラグイン_削除($pluginCode)
    {
        $this->独自プラグイン_ボタンクリック($pluginCode, '削除');
        $this->tester->waitForElementVisible(['css' => '#localPluginDeleteModal .modal-footer a']);
        $this->tester->click(['css' => '#localPluginDeleteModal .modal-footer a']);

        return $this;
    }

    public function 独自プラグイン_アップデート($pluginCode, $fileName)
    {
        $this->tester->attachFile(['xpath' => $this->独自プラグイン_セレクタ($pluginCode).'/../td[5]//input[@type="file"]'], $fileName);
        $this->tester->click(['xpath' => $this->独自プラグイン_セレクタ($pluginCode).'/../td[5]//button']);
        $this->tester->see('アップデートしました。', self::完了メーッセージ);

        return $this;
    }

    private function 独自プラグイン_ボタンクリック($pluginCode, $label)
    {
        $xpath = ['xpath' => $this->独自プラグイン_セレクタ($pluginCode).'/../td[6]//i[@data-original-title="'.$label.'"]/parent::node()'];
        $this->tester->click($xpath);

        return $this;
    }

    private function 独自プラグイン_セレクタ($pluginCode)
    {
        return '//*[@id="page_admin_store_plugin"]//div/h5[contains(text(), "ユーザー独自プラグイン")]/../..//table/tbody//td[3][contains(text(), "'.$pluginCode.'")]/';
    }
}

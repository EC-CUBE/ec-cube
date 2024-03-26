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

class OwnersPluginListPage extends AbstractAdminNewPage
{
    public static $完了メッセージ = '#main .container-fluid div:nth-child(1) .alert-success';

    /**
     * OwnersPluginPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);

        return $page->goPage('/store/plugin', 'インストールプラグイン一覧');
    }
    public function install($code)
    {
        $this->tester->click(['xpath' => '//span[contains(text(),"'.$code.'")]/ancestor::tr/td/a[contains(text(),"インストール")]']);
        $this->tester->waitForText('以下のプラグインをインストールします');
        $this->tester->click('インストール');
        $this->tester->waitForElement('#installModal');
        $this->tester->seeElement('#installModal');
        $this->tester->waitForText('をインストールしますか？', 20, '#installModal .modal-body');
        $this->tester->click('インストール', '#installModal');
        $this->tester->waitForText('インストールが完了しました。', 60, '#installModal .modal-body');
        $this->tester->click('完了','#installModal .modal-footer');

        return $this;
    }

    public function enable($code)
    {
        $this->tester->click(['xpath' => '//p[contains(text(),"'.$code.'")]/ancestor::tr/td/div/div/a//i[@data-bs-original-title="有効化"]']);
        $this->tester->waitForText('「'.$code.'」を有効にしました。', 20);
        return $this;
    }

    public function disable($code)
    {
        $this->tester->click(['xpath' => '//p[contains(text(),"'.$code.'")]/ancestor::tr/td/div/div/a//i[@data-bs-original-title="無効化"]']);
        $this->tester->waitForText('「'.$code.'」を無効にしました。', 20);
        return $this;
    }

    public function uninstall($code)
    {
        $this->tester->click(['xpath' => '//p[contains(text(),"'.$code.'")]/ancestor::tr/td/div/div/a//i[@data-bs-original-title="削除"]']);
        $this->tester->waitForText('このプラグインを削除してもよろしいですか？', 20, "#officialPluginDeleteModal");

        $this->tester->click("削除","#officialPluginDeleteModal");
        $this->tester->waitForText("削除が完了しました。", 20, "#officialPluginDeleteModal");
        $this->tester->click("完了","#officialPluginDeleteModal");

        return $this;
    }

    public function authenByKey($key)
    {
        $this->tester->click("認証キー設定",".c-mainNavArea nav");

        $this->tester->expect('認証キーの入力を行います。');
        $this->tester->fillField(['id' => 'admin_authentication_authentication_key'], $key);

        $this->tester->expect('認証キーの登録ボタンをクリックします。');
        $this->tester->click(['css' => '.btn-ec-conversion']);
        $this->tester->waitForText('保存しました');
    }

}

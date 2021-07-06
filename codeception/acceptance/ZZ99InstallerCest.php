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

use Page\Install\InstallPage;

/**
 * @group installer
 */
class ZZ99InstallerCest
{
    protected $writableFiles = [
        'app/Plugin',
        'app/PluginData',
        'app/proxy',
        'app/template',
        'html',
        'var',
        'vendor',
        'composer.json',
        'composer.lock',
    ];

    /**
     * 権限チェックのテスト.
     *
     * @param AcceptanceTester $I
     */
    public function installer_CheckPermission(AcceptanceTester $I)
    {
        $I->wantTo('ZZ99 インストーラ 権限チェックのテスト');

        // step1
        $page = InstallPage::go($I);
        $I->see('ようこそ', InstallPage::$STEP1_タイトル);

        // 次へ
        $page->step1_次へボタンをクリック();

        // step2
        $I->see('権限チェック', InstallPage::$STEP2_タイトル);
        $I->see('アクセス権限は正常です', InstallPage::$STEP2_テキストエリア);

        $rootDir = __DIR__.'/../../';

        foreach ($this->writableFiles as $file) {
            $path = $rootDir.$file;
            $origin = octdec(substr(sprintf('%o', fileperms($path)), -4));

            // 書き込み権限を外す
            chmod($path, 0555);

            // リロードしてエラーが表示されることを確認.
            $page->step2_リロード();
            $I->see('以下のファイルまたはディレクトリに書き込み権限を付与してください', InstallPage::$STEP2_テキストエリア);
            $I->see($file, InstallPage::$STEP2_テキストエリア);

            // 権限を戻す.
            chmod($path, $origin);
        }

        // 対象外のディレクトリ・ファイルの確認
        $externalDir = $rootDir.'externalDir';
        mkdir($externalDir, 0555, true);

        $externalFile = $rootDir.'externalFile.txt';
        touch($externalFile);
        chmod($externalFile, 0555);

        $page->step2_リロード();
        $I->see('アクセス権限は正常です', InstallPage::$STEP2_テキストエリア);

        chmod($externalDir, 0777);
        chmod($externalFile, 0777);
        rmdir($externalDir);
        unlink($externalFile);
    }
}

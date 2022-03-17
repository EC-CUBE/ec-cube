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

class MasterDataManagePage extends AbstractAdminPageStyleGuide
{
    public static $URL = '/setting/system/masterdata';

    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * ProductListPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go(\AcceptanceTester $I)
    {
        $page = new MasterDataManagePage($I);

        return $page->goPage(self::$URL, 'マスタデータ管理システム設定');
    }

    public function 選択($option)
    {
        $this->tester->selectOption(['id' => 'admin_system_masterdata_masterdata'], $option);
        $this->tester->click('#form1 button');

        return $this;
    }

    public function 入力_ID($row, $value)
    {
        $this->tester->fillField(['css' => "#form2 table tbody tr:nth-child({$row}) td:nth-child(1) input"], $value);

        return $this;
    }

    public function 入力_Name($row, $value)
    {
        $this->tester->fillField(['css' => "#form2 table tbody tr:nth-child({$row}) td:nth-child(2) input"], $value);

        return $this;
    }

    public function 保存()
    {
        $this->tester->click(['css' => '#form2 .c-conversionArea .ladda-button']);

        return $this;
    }
}

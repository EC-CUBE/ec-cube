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

namespace Page\Admin;


class ShippingCsvUploadPage extends AbstractAdminPageStyleGuide
{
    public static $完了メッセージ = 'div.alert-primary';

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new ProductCsvUploadPage($I);
        return $page->goPage('/shipping/shipping_csv_upload', '出荷CSV登録出荷管理');
    }

    public function 入力_CSVファイル($fileName)
    {
        $this->tester->attachFile(['id' => 'admin_csv_import_import_file'], $fileName);
        return $this;
    }

    public function CSVアップロード()
    {
        $this->tester->click(['id' => 'upload-button']);
        return $this;
    }

    public function 雛形ダウンロード()
    {
        $this->tester->click(['id' => 'download-button']);
        return $this;
    }
}
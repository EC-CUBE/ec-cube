<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_DB/SC_Helper_DB_TestBase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * SC_Helper_DB::sfGetBasisDataCache()のテストクラス.
 *
 * @author Hiroko Tamagawa
 * @version $Id: SC_Helper_DB_sfGetBasisDataCache.php 22567 2013-02-18 10:09:54Z shutta $
 */
class SC_Helper_DB_sfGetBasisDataCache extends SC_Helper_DB_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->helper = new SC_Helper_DB_sfGetBasisDataCacheMock();
        $this->cashFilePath = MASTER_DATA_REALDIR . 'dtb_baseinfo.serial';
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////
    public function testSfGetBasisDataCache_キャッシュがなく生成もしない場合_空を返す()
    {
        $this->setUpBasisData();
        unlink($this->cashFilePath);
        $this->expected = array();
        $this->actual = $this->helper->sfGetBasisDataCache();
        $this->verify();
    }
    
    public function testSfGetBasisDataCache_キャッシュがなく生成する場合_キャッシュの値を返す()
    {
        $this->setUpBasisData();
        unlink($this->cashFilePath);
        $this->expected = array(
            'id' => '1',
            'company_name' => 'testshop',
            'company_kana' => 'テストショップ',
            'zip01' => '530',
            'zip02' => '0001',
            'pref' => '1',
            'addr01' => 'testaddr01',
            'addr02' => 'testaddr02',
            'tel01' => '11',
            'tel02' => '2222',
            'tel03' => '3333',
            'fax01' => '11',
            'fax02' => '2222',
            'fax03' => '3333',
            'business_hour' => '09-18',
            'law_company' => 'lawcampanyname',
            'law_manager' => 'lawmanager',
            'law_zip01' => '530',
            'law_zip02' => '0001',
            'law_pref' => '1',
            'law_addr01' => 'lawaddr01',
            'law_addr02' => 'lawaddr02',
            'law_tel01' => '11',
            'law_tel02' => '2222',
            'law_tel03' => '3333',
            'law_fax01' => '11',
            'law_fax02' => '2222',
            'law_fax03' => '3333',
            'law_email' => 'test@test.com',
            'law_url' => 'http://test.test',
            'law_term01' => 'lawterm01',
            'law_term02' => 'lawterm02',
            'law_term03' => 'lawterm03',
            'law_term04' => 'lawterm04',
            'law_term05' => 'lawterm05',
            'law_term06' => 'lawterm06',
            'law_term07' => 'lawterm07',
            'law_term08' => 'lawterm08',
            'law_term09' => 'lawterm09',
            'law_term10' => 'lawterm10',
            'tax' => '5',
            'tax_rule' => '1',
            'email01' => 'test1@test.com',
            'email02' => 'test2@test.com',
            'email03' => 'test3@test.com',
            'email04' => 'test4@test.com',
            'free_rule' => '1000',
            'shop_name' => 'shopname',
            'shop_kana' => 'ショップネーム',
            'shop_name_eng' => 'shopnameeng',
            'point_rate' => '10',
            'welcome_point' => '100',
            'update_date' => '2012-02-14 11:22:33',
            'top_tpl' => 'top.tpl',
            'product_tpl' => 'product.tpl',
            'detail_tpl' => 'detail.tpl',
            'mypage_tpl' => 'mypage.tpl',
            'good_traded' => 'goodtraded',
            'message' => 'message',
            'regular_holiday_ids' => '0|6',
            'latitude' => '30.0001',
            'longitude' => '45.0001',
            'downloadable_days' => '10',
            'downloadable_days_unlimited' => '0'
        );
        $this->actual = $this->helper->sfGetBasisDataCache(true);
        $this->verify();
    }
    
    public function testSfGetBasisDataCache_キャッシュがある場合_キャッシュの値を返す()
    {
        $this->setUpBasisData();
        unlink($this->cashFilePath);
        $this->helper->sfCreateBasisDataCache();
        $this->expected = array(
            'id' => '1',
            'company_name' => 'testshop',
            'company_kana' => 'テストショップ',
            'zip01' => '530',
            'zip02' => '0001',
            'pref' => '1',
            'addr01' => 'testaddr01',
            'addr02' => 'testaddr02',
            'tel01' => '11',
            'tel02' => '2222',
            'tel03' => '3333',
            'fax01' => '11',
            'fax02' => '2222',
            'fax03' => '3333',
            'business_hour' => '09-18',
            'law_company' => 'lawcampanyname',
            'law_manager' => 'lawmanager',
            'law_zip01' => '530',
            'law_zip02' => '0001',
            'law_pref' => '1',
            'law_addr01' => 'lawaddr01',
            'law_addr02' => 'lawaddr02',
            'law_tel01' => '11',
            'law_tel02' => '2222',
            'law_tel03' => '3333',
            'law_fax01' => '11',
            'law_fax02' => '2222',
            'law_fax03' => '3333',
            'law_email' => 'test@test.com',
            'law_url' => 'http://test.test',
            'law_term01' => 'lawterm01',
            'law_term02' => 'lawterm02',
            'law_term03' => 'lawterm03',
            'law_term04' => 'lawterm04',
            'law_term05' => 'lawterm05',
            'law_term06' => 'lawterm06',
            'law_term07' => 'lawterm07',
            'law_term08' => 'lawterm08',
            'law_term09' => 'lawterm09',
            'law_term10' => 'lawterm10',
            'tax' => '5',
            'tax_rule' => '1',
            'email01' => 'test1@test.com',
            'email02' => 'test2@test.com',
            'email03' => 'test3@test.com',
            'email04' => 'test4@test.com',
            'free_rule' => '1000',
            'shop_name' => 'shopname',
            'shop_kana' => 'ショップネーム',
            'shop_name_eng' => 'shopnameeng',
            'point_rate' => '10',
            'welcome_point' => '100',
            'update_date' => '2012-02-14 11:22:33',
            'top_tpl' => 'top.tpl',
            'product_tpl' => 'product.tpl',
            'detail_tpl' => 'detail.tpl',
            'mypage_tpl' => 'mypage.tpl',
            'good_traded' => 'goodtraded',
            'message' => 'message',
            'regular_holiday_ids' => '0|6',
            'latitude' => '30.0001',
            'longitude' => '45.0001',
            'downloadable_days' => '10',
            'downloadable_days_unlimited' => '0'
        );
        $this->actual = $this->helper->sfGetBasisDataCache();
        unlink($this->cashFilePath);
        $this->verify();
    }
    
}

class SC_Helper_DB_sfGetBasisDataCacheMock extends SC_Helper_DB_Ex
{
    function sfCreateBasisDataCache()
    {
        // テーブル名
        $name = 'dtb_baseinfo';
        // キャッシュファイルパス
        $filepath = MASTER_DATA_REALDIR . $name . '.serial';
        // データ取得
        $arrData = array(
            'id' => '1',
            'company_name' => 'testshop',
            'company_kana' => 'テストショップ',
            'zip01' => '530',
            'zip02' => '0001',
            'pref' => '1',
            'addr01' => 'testaddr01',
            'addr02' => 'testaddr02',
            'tel01' => '11',
            'tel02' => '2222',
            'tel03' => '3333',
            'fax01' => '11',
            'fax02' => '2222',
            'fax03' => '3333',
            'business_hour' => '09-18',
            'law_company' => 'lawcampanyname',
            'law_manager' => 'lawmanager',
            'law_zip01' => '530',
            'law_zip02' => '0001',
            'law_pref' => '1',
            'law_addr01' => 'lawaddr01',
            'law_addr02' => 'lawaddr02',
            'law_tel01' => '11',
            'law_tel02' => '2222',
            'law_tel03' => '3333',
            'law_fax01' => '11',
            'law_fax02' => '2222',
            'law_fax03' => '3333',
            'law_email' => 'test@test.com',
            'law_url' => 'http://test.test',
            'law_term01' => 'lawterm01',
            'law_term02' => 'lawterm02',
            'law_term03' => 'lawterm03',
            'law_term04' => 'lawterm04',
            'law_term05' => 'lawterm05',
            'law_term06' => 'lawterm06',
            'law_term07' => 'lawterm07',
            'law_term08' => 'lawterm08',
            'law_term09' => 'lawterm09',
            'law_term10' => 'lawterm10',
            'tax' => '5',
            'tax_rule' => '1',
            'email01' => 'test1@test.com',
            'email02' => 'test2@test.com',
            'email03' => 'test3@test.com',
            'email04' => 'test4@test.com',
            'free_rule' => '1000',
            'shop_name' => 'shopname',
            'shop_kana' => 'ショップネーム',
            'shop_name_eng' => 'shopnameeng',
            'point_rate' => '10',
            'welcome_point' => '100',
            'update_date' => '2012-02-14 11:22:33',
            'top_tpl' => 'top.tpl',
            'product_tpl' => 'product.tpl',
            'detail_tpl' => 'detail.tpl',
            'mypage_tpl' => 'mypage.tpl',
            'good_traded' => 'goodtraded',
            'message' => 'message',
            'regular_holiday_ids' => '0|6',
            'latitude' => '30.0001',
            'longitude' => '45.0001',
            'downloadable_days' => '10',
            'downloadable_days_unlimited' => '0'
        );
        // シリアライズ
        $data = serialize($arrData);
        // ファイルを書き出しモードで開く
        $handle = fopen($filepath, 'w');
        if (!$handle) {
            // ファイル生成失敗
            return false;
        }
        // ファイルの内容を書き出す.
        $res = fwrite($handle, $data);
        // ファイルを閉じる
        fclose($handle);
        if ( $res === false) {
            // ファイル生成失敗
            return false;
        }
        // ファイル生成成功
        return true;
    }
}
